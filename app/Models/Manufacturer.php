<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    use HasFactory;

    /**
     * Veritabanı tablosu
     */
    protected $table = 'manufacturers';

    /**
     * Doldurulabilir alanlar
     */
    protected $fillable = [
        'name',
        'image',
    ];

    /**
     * Üreticinin siparişlerle ilişkisi
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'manufacturer_id');
    }

    /**
     * Üreticinin ManufacturerOrder ile ilişkisi
     */
    public function manufacturerOrders()
    {
        return $this->hasMany(ManufacturerOrder::class, 'manufacturer_id');
    }
}
