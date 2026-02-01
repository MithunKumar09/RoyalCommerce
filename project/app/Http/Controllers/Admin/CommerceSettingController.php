<?php

namespace App\Http\Controllers\Admin;

use App\Models\CommerceSetting;
use App\Services\CommerceState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CommerceSettingController extends AdminBaseController
{
    public function index()
    {
        if (!Schema::hasTable('commerce_settings')) {
            return view('admin.generalsetting.commerce', [
                'setting' => null,
                'state' => CommerceState::current(),
                'schemaReady' => false,
            ]);
        }

        $setting = CommerceSetting::query()
            ->firstOrCreate(
                ['scope_type' => 'platform', 'scope_id' => null],
                [
                    'mode' => 'enabled',
                    'cart_enabled' => true,
                    'checkout_enabled' => true,
                    'orders_enabled' => true,
                    'wallet_enabled' => true,
                    'order_tracking_enabled' => true,
                ]
            );

        return view('admin.generalsetting.commerce', [
            'setting' => $setting,
            'state' => CommerceState::current(),
            'schemaReady' => true,
        ]);
    }

    public function update(Request $request)
    {
        if (!Schema::hasTable('commerce_settings')) {
            $msg = 'Commerce settings table is not available. Run migrations first.';
            if ($request->ajax() || $request->expectsJson()) {
                return response($msg, 422);
            }
            return redirect()->back()->with('unsuccess', $msg);
        }

        $validated = $request->validate([
            'mode' => 'required|in:enabled,disabled,whatsapp_only',
            'cart_enabled' => 'nullable|boolean',
            'checkout_enabled' => 'nullable|boolean',
            'orders_enabled' => 'nullable|boolean',
            'wallet_enabled' => 'nullable|boolean',
            'order_tracking_enabled' => 'nullable|boolean',
            'whatsapp_number' => 'nullable|string|max:64',
            'whatsapp_message_template' => 'nullable|string|max:5000',
        ]);

        $setting = CommerceSetting::query()->firstOrCreate(
            ['scope_type' => 'platform', 'scope_id' => null],
            [
                'mode' => 'enabled',
                'cart_enabled' => true,
                'checkout_enabled' => true,
                'orders_enabled' => true,
                'wallet_enabled' => true,
                'order_tracking_enabled' => true,
            ]
        );

        // Checkbox-style booleans
        $setting->mode = $validated['mode'];
        $setting->cart_enabled = (bool) ($request->input('cart_enabled', 0));
        $setting->checkout_enabled = (bool) ($request->input('checkout_enabled', 0));
        $setting->orders_enabled = (bool) ($request->input('orders_enabled', 0));
        $setting->wallet_enabled = (bool) ($request->input('wallet_enabled', 0));
        $setting->order_tracking_enabled = (bool) ($request->input('order_tracking_enabled', 0));
        $setting->whatsapp_number = $validated['whatsapp_number'] ?? null;
        $setting->whatsapp_message_template = $validated['whatsapp_message_template'] ?? null;
        $setting->updated_by_admin_id = Auth::guard('admin')->check() ? Auth::guard('admin')->id() : null;
        $setting->save();

        // Push state to cache + bump version for SSE clients.
        CommerceState::refresh();

        $msg = 'Commerce settings updated successfully.';
        if ($request->ajax() || $request->expectsJson()) {
            return response($msg);
        }
        return redirect()->back()->with('success', $msg);
    }
}

