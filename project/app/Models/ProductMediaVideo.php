<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMediaVideo extends Model
{
    protected $fillable = [
        'product_id',
        'target_type',
        'target_id',
        'source_type',
        'video_url',
        'video_path',
    ];
}
