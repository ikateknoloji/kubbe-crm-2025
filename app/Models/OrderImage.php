<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class OrderImage extends Model
{
    use HasFactory;

    protected $table = 'order_images';

    protected $fillable = [
        'order_id',
        'image_path',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
