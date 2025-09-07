<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ItemCategory extends Pivot
{
    use HasFactory;

    protected $table = 'item_category';

    protected $fillable = [
        'item_id',
        'category_id',
    ];

    // 中間テーブルなので、ここにリレーションは定義ない。
}
