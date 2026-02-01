<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommerceSetting extends Model
{
    protected $table = 'commerce_settings';

    protected $fillable = [
        'scope_type',
        'scope_id',
        'mode',
        'cart_enabled',
        'checkout_enabled',
        'orders_enabled',
        'wallet_enabled',
        'order_tracking_enabled',
        'whatsapp_number',
        'whatsapp_message_template',
        'updated_by_admin_id',
    ];
}

