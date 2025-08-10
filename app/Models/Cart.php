<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $items
 */
class Cart extends Model
{
    /** @use HasFactory<\Database\Factories\CartFactory> */
    use HasFactory;
    use SoftDeletes;
    use CascadeSoftDeletes;

    /** @var array<string> */
    protected $cascadeDeletes = ['items'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
    ];

    /**
     * @return BelongsTo<User, Cart>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<CartItem, Cart>
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    protected static function booted()
    {
        static::restoring(function ($cart) {
            $cart->items()->withTrashed()->get()->each->restore();
        });
    }
}
