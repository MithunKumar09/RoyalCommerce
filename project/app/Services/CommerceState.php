<?php

namespace App\Services;

use App\Models\CommerceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Central access point for platform-wide commerce state.
 *
 * - Default-safe: if table not migrated yet, returns fully enabled state.
 * - Cache-backed: fast reads for middleware and SSE.
 */
class CommerceState
{
    public const CACHE_KEY_STATE = 'commerce.state';
    public const CACHE_KEY_VERSION = 'commerce.state.version';

    /**
     * @return array{
     *   version:int,
     *   mode:string,
     *   flags: array{cart_enabled:bool,checkout_enabled:bool,orders_enabled:bool,wallet_enabled:bool,order_tracking_enabled:bool},
     *   whatsapp: array{number:?string,template:?string},
     *   capabilities: array{cart:bool,checkout:bool,orders:bool,wallet:bool,order_tracking:bool}
     * }
     */
    public static function current(): array
    {
        $cached = Cache::get(self::CACHE_KEY_STATE);
        if (is_array($cached) && isset($cached['capabilities'])) {
            return $cached;
        }

        return self::prime();
    }

    public static function version(): int
    {
        return (int) Cache::get(self::CACHE_KEY_VERSION, 1);
    }

    public static function can(string $capability): bool
    {
        $state = self::current();
        $caps = isset($state['capabilities']) && is_array($state['capabilities']) ? $state['capabilities'] : [];
        return (bool) ($caps[$capability] ?? true);
    }

    /**
     * Standardized denial response for notify/callback enforcement.
     */
    public static function denyResponse(Request $request, string $capability, ?string $message = null)
    {
        $state = self::current();
        $payload = [
            'success' => false,
            'code' => 'ecommerce_disabled',
            'capability' => $capability,
            'mode' => $state['mode'] ?? 'enabled',
            'message' => $message ?: __('This feature is temporarily unavailable.'),
        ];

        if ($request->is('api/*') || $request->expectsJson() || $request->ajax()) {
            return response()->json($payload, 403);
        }

        // For browser redirects back from payment providers, send user to cart with message.
        return redirect()->route('front.cart')->with('unsuccess', $payload['message']);
    }

    /**
     * Rebuilds cached state from DB and increments version.
     */
    public static function refresh(): array
    {
        $state = self::buildStateFromDatabase();

        // Always bump version when refreshing (admin updates should call refresh()).
        $version = (int) Cache::get(self::CACHE_KEY_VERSION, 1);
        $version++;
        $state['version'] = $version;

        Cache::put(self::CACHE_KEY_VERSION, $version);
        Cache::put(self::CACHE_KEY_STATE, $state);

        return $state;
    }

    /**
     * Populates cache without changing version (safe for reads/SSE connects).
     */
    public static function prime(): array
    {
        $state = self::buildStateFromDatabase();
        $version = (int) Cache::get(self::CACHE_KEY_VERSION, 1);
        $state['version'] = $version;
        Cache::put(self::CACHE_KEY_STATE, $state);
        return $state;
    }

    private static function buildStateFromDatabase(): array
    {
        // Safe default if migrations not applied yet.
        if (!Schema::hasTable('commerce_settings')) {
            return self::defaultEnabledState();
        }

        $row = CommerceSetting::query()
            ->where('scope_type', 'platform')
            ->whereNull('scope_id')
            ->first();

        if (!$row) {
            return self::defaultEnabledState();
        }

        $mode = (string) ($row->mode ?: 'enabled');

        $flags = [
            'cart_enabled' => (bool) $row->cart_enabled,
            'checkout_enabled' => (bool) $row->checkout_enabled,
            'orders_enabled' => (bool) $row->orders_enabled,
            'wallet_enabled' => (bool) $row->wallet_enabled,
            'order_tracking_enabled' => (bool) $row->order_tracking_enabled,
        ];

        return self::normalize($mode, $flags, [
            'number' => $row->whatsapp_number ? (string) $row->whatsapp_number : null,
            'template' => $row->whatsapp_message_template ? (string) $row->whatsapp_message_template : null,
        ]);
    }

    private static function defaultEnabledState(): array
    {
        return self::normalize('enabled', [
            'cart_enabled' => true,
            'checkout_enabled' => true,
            'orders_enabled' => true,
            'wallet_enabled' => true,
            'order_tracking_enabled' => true,
        ], [
            'number' => null,
            'template' => null,
        ]);
    }

    /**
     * Converts mode+flags into final capabilities (single source).
     */
    private static function normalize(string $mode, array $flags, array $whatsapp): array
    {
        $mode = in_array($mode, ['enabled', 'disabled', 'whatsapp_only'], true) ? $mode : 'enabled';

        // Mode-level overrides
        if ($mode === 'disabled') {
            $flags['cart_enabled'] = false;
            $flags['checkout_enabled'] = false;
            $flags['orders_enabled'] = false;
            $flags['wallet_enabled'] = false;
            $flags['order_tracking_enabled'] = false;
        }

        if ($mode === 'whatsapp_only') {
            // WhatsApp-only: no cart/checkout flow; still allow read-only order history.
            $flags['cart_enabled'] = false;
            $flags['checkout_enabled'] = false;
            $flags['orders_enabled'] = false;
            // wallet is part of checkout flow; disable for now.
            $flags['wallet_enabled'] = false;
            // tracking can be policy-driven; keep enabled by default unless explicitly turned off.
        }

        $capabilities = [
            'cart' => (bool) ($flags['cart_enabled'] ?? false),
            'checkout' => (bool) ($flags['checkout_enabled'] ?? false),
            'orders' => (bool) ($flags['orders_enabled'] ?? false),
            'wallet' => (bool) ($flags['wallet_enabled'] ?? false),
            'order_tracking' => (bool) ($flags['order_tracking_enabled'] ?? false),
        ];

        return [
            'version' => 1, // overwritten by refresh()
            'mode' => $mode,
            'flags' => [
                'cart_enabled' => (bool) ($flags['cart_enabled'] ?? false),
                'checkout_enabled' => (bool) ($flags['checkout_enabled'] ?? false),
                'orders_enabled' => (bool) ($flags['orders_enabled'] ?? false),
                'wallet_enabled' => (bool) ($flags['wallet_enabled'] ?? false),
                'order_tracking_enabled' => (bool) ($flags['order_tracking_enabled'] ?? false),
            ],
            'whatsapp' => [
                'number' => isset($whatsapp['number']) ? $whatsapp['number'] : null,
                'template' => isset($whatsapp['template']) ? $whatsapp['template'] : null,
            ],
            'capabilities' => $capabilities,
        ];
    }
}

