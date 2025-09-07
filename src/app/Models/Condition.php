<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Condition extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
    ];

    /**
     * Get the items for the condition.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
