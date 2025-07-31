<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $product_id
 * @property string $description
 * @property string $startDate
 * @property string $endDate
 * @property float $discountPercentage
 */
class Discount extends Model
{
    /** @use HasFactory<\Database\Factories\DiscountFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'description',
        'startDate',
        'endDate',
        'discountPercentage',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'product_id' => 'integer',
        'description' => 'string',
        'startDate' => 'date',
        'endDate' => 'date',
        'discountPercentage' => 'float',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Product, Discount>
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
