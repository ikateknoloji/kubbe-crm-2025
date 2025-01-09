<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['quantity', 'product_type_id', 'color_id'];

    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    /**
     * Get the order items associated with the stock.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'stock_id');
    }

    /**
     * Scope a query to filter stocks by product type and color name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $productType
     * @param string|null $colorName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, $productType = null, $colorName = null)
    {
        return $query->when($productType, function ($q, $productType) {
                return $q->whereHas('productType', function ($q) use ($productType) {
                    $q->where('product_type', $productType);
                });
            })
            ->when($colorName, function ($q, $colorName) {
                return $q->whereHas('color', function ($q) use ($colorName) {
                    $q->where('color_name', $colorName);
                });
            });
    }
}
