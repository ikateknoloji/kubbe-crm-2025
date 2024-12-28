<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Color extends Model
{
    use HasFactory;
    protected $fillable = ['color_name', 'color_hex'];

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'color_id');
    }
}
