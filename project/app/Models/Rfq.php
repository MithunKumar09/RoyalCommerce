<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rfq extends Model
{
    protected $table = 'rfqs';

    protected $fillable = [
        'product_id',
        'user_id',
        'product_name',
        'sku',

        'first_name',
        'last_name',
        'email',
        'phone',
        'country',
        'company_name',

        'product_type',
        'estimate_budget',
        'message',
        'attachment',

        'status',
        'ip_address',
        'user_agent',
    ];
}

