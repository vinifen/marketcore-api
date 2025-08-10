<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int|null $product_id
 * @property string|null $description
 * @property string $start_date
 * @property string $end_date
 * @property float $discount_percentage
 */
class Discount extends Model
{
    /** @use HasFactory<\Database\Factories\DiscountFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'description',
        'start_date',
        'end_date',
        'discount_percentage',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'product_id' => 'integer',
        'description' => 'string',
        'start_date' => 'date',
        'end_date' => 'date',
        'discount_percentage' => 'float',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Product, Discount>
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
