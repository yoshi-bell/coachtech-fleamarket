<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'condition_id',
        'name',
        'brand',
        'description',
        'price',
        'img_url',
    ];

    /**
     * Get the user (seller) that owns the item.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the condition associated with the item.
     */
    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class);
    }

    /**
     * Get the categories that belong to the item.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'item_category');
    }

    /**
     * Get the likes for the item.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Get the comments for the item.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the sold item record associated with the item.
     */
    public function soldItem(): HasOne
    {
        return $this->hasOne(SoldItem::class);
    }
}
