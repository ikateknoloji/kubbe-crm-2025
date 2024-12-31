<?php

namespace App\Rules;

use App\Models\Stock;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StockItemValidation implements ValidationRule
{
    protected $stockId;

    public function __construct($stockId)
    {
        $this->stockId = $stockId;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $stock = Stock::find($this->stockId);

        if (!$stock) {
            $fail("Geçersiz stok ID: {$this->stockId}.");
            return;
        }

        if ($stock->quantity < $value) {
            $fail("Stok yetersiz. Mevcut miktar: {$stock->quantity}, talep edilen: {$value}.");
        }
    }
}
