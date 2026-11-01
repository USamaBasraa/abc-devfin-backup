<?php

namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use App\Services\StoreProcurement\PinnacleApiService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class PinnacleController extends Controller
{
    private $pinnacleApiService;

    public function __construct(PinnacleApiService $pinnacleApiService)
    {
        $this->pinnacleApiService = $pinnacleApiService;
    }

    public function fetch(Request $request, string $endpointKey)
    {
        try {
            return response()->json([
                'status' => 'success',
                'endpoint_key' => $endpointKey,
                'data' => $this->pinnacleApiService->fetch($endpointKey, $request->query()),
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'allowed_endpoints' => $this->pinnacleApiService->endpointKeys(),
            ], 404);
        } catch (RequestException $exception) {
            Log::error('Pinnacle API request failed.', [
                'endpoint_key' => $endpointKey,
                'status' => optional($exception->response)->status(),
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Pinnacle API request failed.',
            ], 502);
        } catch (ConnectionException $exception) {
            Log::error('Pinnacle API connection failed.', [
                'endpoint_key' => $endpointKey,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to connect to Pinnacle API.',
            ], 503);
        } catch (Throwable $exception) {
            Log::error('Unexpected Pinnacle API error.', [
                'endpoint_key' => $endpointKey,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unexpected Pinnacle API error.',
            ], 500);
        }
    }
}
