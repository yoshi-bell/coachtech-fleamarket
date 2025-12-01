<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\SoldItem
 *
 * @property int $id
 * @property int $item_id
 * @property int $buyer_id
 * @property string $postcode
 * @property string $address
 * @property string|null $building
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $buyer
 * @property-read \App\Models\Item $item
 * @method static \Illuminate\Database\Eloquent\Builder|SoldItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SoldItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SoldItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|SoldItem whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SoldItem whereBuilding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SoldItem whereBuyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SoldItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SoldItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SoldItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SoldItem wherePostcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SoldItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SoldItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'buyer_id',
        'postcode',
        'address',
        'building',
    ];

    /**
     * Get the item that was sold.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the user (buyer) who bought the item.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
