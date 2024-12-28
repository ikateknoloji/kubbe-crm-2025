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
}
