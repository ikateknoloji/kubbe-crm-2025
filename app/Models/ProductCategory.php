<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCategory extends Model
{
    use HasFactory;
    protected $fillable = ['category'];

    public function productTypes()
    {
        return $this->hasMany(ProductType::class, 'product_category_id');
    }
}
