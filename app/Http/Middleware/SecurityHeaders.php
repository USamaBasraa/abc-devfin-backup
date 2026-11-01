<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->is('teacher/add_question') && $request->isMethod('post')) {
            if ($request->method() !== 'GET' && $request->session()->token() !== $request->input('_token')) {
                // Log the CSRF failure for debugging or security auditing
                Log::warning('CSRF token mismatch', ['url' => $request->url()]);
                // Return a 419 status code or redirect to a specific error page
                return response('CSRF token mismatch', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
        
        // Retrieve the current base URL dynamically from the request
        $scheme = request()->getScheme(); // 'http' or 'https'
        $host = request()->getHost();     // The domain name or IP address
        $port = request()->getPort();     // The port number (optional, default 80 for http and 443 for https)
        // Construct the base URL
        $baseUrl = $scheme . '://' . $host;
        if ($port && (($scheme === 'http' && $port != 80) || ($scheme === 'https' && $port != 443))) {
            $baseUrl .= ':' . $port;
        }
        // Ensure the base URL ends with a trailing slash
        $baseUrl = rtrim($baseUrl, '/') . '/';
        // frame-src lists all three environments. The staff app serves the fee-challan
        // PDFs (chalanfin.php / chalanfinPreview.php) that the challan preview modal
        // frames, and its host changes per environment: staff.bnu.edu.pk in production,
        // teststaff.bnu.edu.pk on test, devstaff.bnu.edu.pk on dev. Drop a host here and
        // that modal goes blank while download and open-in-new-tab still work.
        // Updated Content-Security-Policy header with YouTube allowed in frame-src.
        $cspHeader = "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.gstatic.com https://unpkg.com; " .
        "style-src-elem 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.gstatic.com https://unpkg.com; " .
        // "img-src 'self' data: https://*.googleusercontent.com https://*.googleapis.com; " .
        "font-src 'self' https://fonts.gstatic.com; " .
        "connect-src 'self' https://www.googleapis.com https://api.bnu.edu.pk " .
        "http://devapi.bnu.edu.pk https://devapi.bnu.edu.pk http://testapi.bnu.edu.pk https://testapi.bnu.edu.pk; " .
        "frame-src 'self' https://www.youtube.com " .
        "https://cms.bnu.edu.pk/ http://testcms.bnu.edu.pk/ https://testcms.bnu.edu.pk/ http://devcms.bnu.edu.pk/ " .
        "https://staff.bnu.edu.pk/ http://teststaff.bnu.edu.pk/ https://teststaff.bnu.edu.pk/ " .
        "http://devstaff.bnu.edu.pk/ https://devstaff.bnu.edu.pk/; " .
        "object-src 'none'; " .
        "base-uri 'self';";

        // Set the Content-Security-Policy header
        $response->headers->set('Content-Security-Policy', $cspHeader);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Referrer-Policy', 'no-referrer');

        return $response;
    }
}
