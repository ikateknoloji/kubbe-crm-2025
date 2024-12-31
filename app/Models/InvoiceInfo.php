<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceInfo extends Model
{
    use HasFactory;

    /**
     * Tablo adı.
     *
     * @var string
     */
    protected $table = 'invoice_infos';

    /**
     * Doldurulabilir alanlar.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'invoice_type',
        'company_name',
        'name',
        'surname',
        'tc_number',
        'address',
        'tax_office',
        'tax_number',
        'email',
    ];

    /**
     * İlişkiler: Sipariş.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
