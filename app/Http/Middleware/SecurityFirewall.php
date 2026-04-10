<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityFirewall
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = http_build_query($request->all());
        $uri = $request->getRequestUri();
        $userAgent = $request->userAgent();

        // 1. Block Bad User Agents (SEO Cloaking/Scanning bots)
        $badAgents = [
            'AhrefsBot', 'SemrushBot', 'MJ12bot', 'DotBot', 'PetalBot', 'Baiduspider',
            'libwww-perl', 'curl', 'python-requests', 'wget', 'nikto', 'sqlmap', 'nmap', 'zmeu'
        ];
        
        if ($userAgent) {
            foreach ($badAgents as $agent) {
                if (stripos($userAgent, $agent) !== false) {
                    \Illuminate\Support\Facades\Log::warning('SecurityFirewall: Blocked User-Agent', ['agent' => $userAgent, 'ip' => $request->ip()]);
                    abort(403, 'Forbidden');
                }
            }
        }

        // 2. Check Payload & URI for Malicious Strings (Webshells, Backdoors, SQLi)
        $maliciousPatterns = [
            '/<script/i', '/eval\(/i', '/base64_decode\(/i', '/system\(/i', '/exec\(/i', '/shell_exec\(/i',
            '/union\s+select/i', '/select\s+.*\s+from/i', '/information_schema/i', '/\.\.\//', '/\/etc\/passwd/i',
            '/<\?php/i'
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $input) || preg_match($pattern, $uri)) {
                \Illuminate\Support\Facades\Log::warning('SecurityFirewall: Blocked malicious pattern', ['url' => $uri, 'ip' => $request->ip()]);
                abort(403, 'Forbidden');
            }
        }

        // 3. Check for Suspicious File Access (e.g., hidden files, env, known webshells)
        $suspiciousFiles = [
            '.env', '.git', 'wp-config.php', 'config.php', 'cmd.php', 'shell.php', 'b374k.php', 'c99.php'
        ];

        foreach ($suspiciousFiles as $file) {
            if (stripos($uri, $file) !== false) {
                \Illuminate\Support\Facades\Log::warning('SecurityFirewall: Blocked access to suspicious file', ['url' => $uri, 'ip' => $request->ip()]);
                abort(403, 'Forbidden');
            }
        }

        $response = $next($request);

        // Add Security Headers
        if (method_exists($response, 'header')) {
            $response->header('X-Frame-Options', 'SAMEORIGIN');
            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('X-XSS-Protection', '1; mode=block');
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
        } elseif (property_exists($response, 'headers')) {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        }

        return $response;
    }
}
