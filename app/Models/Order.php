<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $address_id
 * @property int|null $coupon_id
 * @property string $order_date
 * @property float $total_amount
 * @property OrderStatus $status
 */
class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'address_id',
        'coupon_id',
        'order_date',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'address_id' => 'integer',
        'coupon_id' => 'integer',
        'order_date' => 'datetime',
        'total_amount' => 'float',
        'status' => OrderStatus::class,
    ];

    /**
     * @return BelongsTo<User, Order>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Address, Order>
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * @return BelongsTo<Coupon, Order>
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }
}
