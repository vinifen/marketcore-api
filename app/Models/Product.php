<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $category_id
 * @property string $name
 * @property int $stock
 * @property float $price
 */
class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'stock',
        'price',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
        'name' => 'string',
        'stock' => 'integer',
        'price' => 'float',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Category, Product>
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function discount()
    {
        return $this->hasMany(Discount::class);
    }

    public function getDiscountAvailable(): Discount|null
    {
        return $this->discount()
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    public function getActiveDiscounts()
    {
        return $this->discount()
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();
    }

    public function getTotalDiscountPercentage(): float
    {
        $total = $this->getActiveDiscounts()->sum('discount_percentage');
        return min($total, 99.0);
    }

    public function getDiscountedPrice(): ?float
    {
        $totalDiscount = $this->getTotalDiscountPercentage();

        if ($totalDiscount <= 0) {
            return null;
        }

        return $this->price * (1 - ($totalDiscount / 100));
    }
}
