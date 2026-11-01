<?php

namespace App\Services\StoreProcurement;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class PinnacleApiService
{
    private const ENDPOINTS = [
        'cost_centers' => 'a1f0',
        'budget_vs_actual' => 'c3b0',
        'budget_annexure' => 'b2a0',
        'building_blocks' => 'b2a1',
        'capital_expenditure_items' => 'b2a2',
        'chart_of_accounts_suppliers' => 'b2a3',
        'expense_items' => 'b2a4',
        'fixed_asset_locations' => 'b2a5',
        'store_items' => 'b2a6',
    ];

    private const CACHE_KEY_API_TOKEN = 'store_procurement.pinnacle.api_token_no';

    private const CACHE_KEY_LOGIN_TOKEN = 'store_procurement.pinnacle.login_jwt';

    public function fetch(string $endpointKey, array $query = []): array
    {
        $endpointCode = $this->endpointCode($endpointKey);
        $url = $this->url($endpointCode);

        return $this->getJson($url, $query, true);
    }

    public function endpointCode(string $endpointKey): string
    {
        if (!array_key_exists($endpointKey, self::ENDPOINTS)) {
            throw new InvalidArgumentException('Invalid Pinnacle endpoint key.');
        }

        return self::ENDPOINTS[$endpointKey];
    }

    public function endpointKeys(): array
    {
        return array_keys(self::ENDPOINTS);
    }

    public function forgetCachedTokens(): void
    {
        Cache::forget(self::CACHE_KEY_API_TOKEN);
        Cache::forget(self::CACHE_KEY_LOGIN_TOKEN);
    }

    private function getJson(string $url, array $query, bool $retryOnUnauthorized): array
    {
        try {
            $response = Http::timeout(30)
                ->retry(2, 1000)
                ->acceptJson()
                ->withToken($this->token())
                ->get($url, $query);

            $response->throw();
        } catch (RequestException $exception) {
            if ($retryOnUnauthorized && optional($exception->response)->status() === 401) {
                Log::info('Pinnacle API returned 401; refreshing API token.');
                $this->forgetCachedTokens();

                return $this->getJson($url, $query, false);
            }

            throw $exception;
        }

        return $response->json() ?? [];
    }

    private function url(string $endpointCode): string
    {
        $baseUrl = rtrim($this->setting('base_url', 'PINNACLE_API_BASE_URL'), '/');

        if ($baseUrl === '') {
            throw new ConnectionException('Pinnacle API base URL is not configured.');
        }

        return $baseUrl . '/' . ltrim($endpointCode, '/');
    }

    private function token(): string
    {
        if ($this->usesAutomaticToken()) {
            $cached = Cache::get(self::CACHE_KEY_API_TOKEN);

            if (is_string($cached) && trim($cached) !== '') {
                return trim($cached);
            }

            $tokenNo = $this->generateApiToken();
            Cache::put(self::CACHE_KEY_API_TOKEN, $tokenNo, $this->apiTokenCacheTtlSeconds());

            return $tokenNo;
        }

        $token = trim($this->setting('token', 'PINNACLE_API_TOKEN'));
        $token = preg_replace('/^Bearer\s+/i', '', $token);

        if ($token === '') {
            throw new ConnectionException(
                'Pinnacle API token is not configured. Set PINNACLE_API_TOKEN or PINNACLE_API_USERNAME and PINNACLE_API_PASSWORD.'
            );
        }

        return $token;
    }

    private function usesAutomaticToken(): bool
    {
        return $this->setting('username', 'PINNACLE_API_USERNAME') !== ''
            && $this->setting('password', 'PINNACLE_API_PASSWORD') !== '';
    }

    private function generateApiToken(): string
    {
        $loginToken = $this->loginToken();

        $response = Http::timeout(30)
            ->acceptJson()
            ->withToken($loginToken)
            ->post($this->externalTokensUrl(), [
                'fromDate' => $this->apiTokenFromDate(),
                'noOfDays' => $this->apiTokenNoOfDays(),
                'noOfPersons' => $this->apiTokenNoOfPersons(),
            ]);

        $response->throw();

        $payload = $response->json() ?? [];
        $tokenNo = trim((string) ($payload['tokenNo'] ?? $payload['token_no'] ?? ''));

        if ($tokenNo === '') {
            throw new ConnectionException('Pinnacle API did not return tokenNo from external-tokens.');
        }

        return $tokenNo;
    }

    private function loginToken(): string
    {
        $cached = Cache::get(self::CACHE_KEY_LOGIN_TOKEN);

        if (is_string($cached) && trim($cached) !== '') {
            return trim($cached);
        }

        $username = $this->setting('username', 'PINNACLE_API_USERNAME');
        $password = $this->setting('password', 'PINNACLE_API_PASSWORD');

        $response = Http::timeout(30)
            ->acceptJson()
            ->post($this->loginUrl(), [
                'username' => $username,
                'password' => $password,
            ]);

        $response->throw();

        $payload = $response->json() ?? [];
        $token = trim((string) ($payload['token'] ?? ''));

        if ($token === '') {
            throw new ConnectionException('Pinnacle login did not return a token.');
        }

        Cache::put(self::CACHE_KEY_LOGIN_TOKEN, $token, 7 * 3600);

        return $token;
    }

    private function loginUrl(): string
    {
        return rtrim($this->setting('base_url', 'PINNACLE_API_BASE_URL'), '/') . '/s0/l';
    }

    private function externalTokensUrl(): string
    {
        $configured = trim($this->setting('external_tokens_url', 'PINNACLE_API_EXTERNAL_TOKENS_URL'));

        if ($configured !== '') {
            return $configured;
        }

        $baseUrl = rtrim($this->setting('base_url', 'PINNACLE_API_BASE_URL'), '/');
        $parts = parse_url($baseUrl);

        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            throw new ConnectionException('Pinnacle API base URL is invalid.');
        }

        $port = isset($parts['port']) ? ':' . $parts['port'] : '';

        return $parts['scheme'] . '://' . $parts['host'] . $port . '/api/external-tokens';
    }

    private function apiTokenFromDate(): string
    {
        $fromDate = trim($this->setting('token_from_date', 'PINNACLE_API_TOKEN_FROM_DATE'));

        if ($fromDate !== '') {
            return $fromDate;
        }

        return date('Y-m-d');
    }

    private function apiTokenNoOfDays(): int
    {
        $days = (int) $this->setting('token_no_of_days', 'PINNACLE_API_TOKEN_NO_OF_DAYS');

        return $days >= 1 ? $days : 7;
    }

    private function apiTokenNoOfPersons(): int
    {
        $persons = (int) $this->setting('token_no_of_persons', 'PINNACLE_API_TOKEN_NO_OF_PERSONS');

        return $persons >= 1 ? $persons : 1;
    }

    private function apiTokenCacheTtlSeconds(): int
    {
        $days = $this->apiTokenNoOfDays();

        return max(3600, ($days * 86400) - 300);
    }

    private function setting(string $configKey, string $envKey): string
    {
        $value = config('services.pinnacle.' . $configKey);

        if ($value !== null && trim((string) $value) !== '') {
            return trim((string) $value);
        }

        $value = env($envKey) ?: getenv($envKey) ?: ($_ENV[$envKey] ?? null);

        if ($value !== null && trim((string) $value) !== '') {
            return trim((string) $value);
        }

        return $this->envFileValue($envKey);
    }

    private function envFileValue(string $key): string
    {
        $envPath = base_path('.env');

        if (!is_readable($envPath)) {
            return '';
        }

        try {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        } catch (Throwable $exception) {
            return '';
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);

            if (trim($name) !== $key) {
                continue;
            }

            return trim(trim($value), '"\'');
        }

        return '';
    }
}
