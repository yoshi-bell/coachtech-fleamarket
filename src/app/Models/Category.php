<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
    ];

    /**
     * Get the items that belong to the category.
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_category');
    }
}
