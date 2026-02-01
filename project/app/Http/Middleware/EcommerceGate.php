<?php

namespace App\Http\Middleware;

use App\Services\CommerceState;
use Closure;
use Illuminate\Http\Request;

class EcommerceGate
{
    public function handle(Request $request, Closure $next, string $capability)
    {
        $state = CommerceState::current();
        $caps = isset($state['capabilities']) && is_array($state['capabilities']) ? $state['capabilities'] : [];

        $allowed = (bool) ($caps[$capability] ?? true);
        if ($allowed) {
            return $next($request);
        }

        $payload = [
            'success' => false,
            'code' => 'ecommerce_disabled',
            'capability' => $capability,
            'mode' => $state['mode'] ?? 'enabled',
            'message' => __('This feature is temporarily unavailable.'),
        ];

        // API/AJAX/JSON callers (cart actions, wallet-check, etc.)
        if ($request->is('api/*') || $request->expectsJson() || $request->ajax()) {
            return response()->json($payload, 403);
        }

        // Regular web requests
        return redirect()
            ->back()
            ->with('unsuccess', $payload['message']);
    }
}

