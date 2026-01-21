<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorySlider extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'subtitle',
        'photo',
        'link',
        'sort_order',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
