<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $code
 * @property string $start_date
 * @property string $end_date
 * @property float $discount_percentage
 */
class Coupon extends Model
{
    /** @use HasFactory<\Database\Factories\CouponFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'start_date',
        'end_date',
        'discount_percentage',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'code' => 'string',
        'start_date' => 'date',
        'end_date' => 'date',
        'discount_percentage' => 'float',
    ];

    public static function findCouponByCode(string $code): ?self
    {
        return self::where('code', $code)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }
}
