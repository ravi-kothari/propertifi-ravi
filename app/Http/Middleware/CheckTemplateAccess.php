<?php

namespace App\Http\Middleware;

use App\Models\DocumentTemplate;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class CheckTemplateAccess
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
        $templateId = $request->route('id');

        // Find the template
        $template = DocumentTemplate::active()->find($templateId);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found or inactive.',
            ], 404);
        }

        // Check if template requires signup and user is not authenticated
        if ($template->requires_signup && !Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to download this template.',
                'requires_signup' => true,
            ], 401);
        }

        // Rate limiting: 10 downloads per hour per IP for free templates
        if ($template->is_free) {
            $key = 'template-download:' . $request->ip();

            if (RateLimiter::tooManyAttempts($key, 10)) {
                $seconds = RateLimiter::availableIn($key);

                return response()->json([
                    'success' => false,
                    'message' => 'Too many download attempts. Please try again later.',
                    'retry_after' => $seconds,
                ], 429);
            }

            RateLimiter::hit($key, 3600); // 1 hour
        }

        // Store template in request for controller use
        $request->merge(['template' => $template]);

        return $next($request);
    }
}
