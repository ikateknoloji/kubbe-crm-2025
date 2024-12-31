<?php

namespace App\Rules;

use App\Models\Stock;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StockQuantity implements ValidationRule
{
    protected $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }


    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // `stock_id` değerlerini gruplandır ve toplam `quantity` miktarını hesapla
        $groupedItems = collect($this->items)->groupBy('stock_id')->map(function ($group) {
            return $group->sum('quantity');
        });

        foreach ($groupedItems as $stockId => $totalQuantity) {
            $stock = Stock::find($stockId);

            if (!$stock) {
                $fail("Geçersiz stok ID: {$stockId}.");
                continue;
            }

            if ($stock->quantity < $totalQuantity) {
                $fail("Stok yetersiz. Stok ID: {$stockId}, mevcut: {$stock->quantity}, talep edilen: {$totalQuantity}.");
            }
        }
    }
}
