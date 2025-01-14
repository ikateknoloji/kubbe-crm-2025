<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Order;

class ShippingRule implements ValidationRule
{
    /**
     * Geçersiz siparişlerin ID'lerini tutar.
     *
     * @var array
     */
    protected array $invalidOrders = [];

    /**
     * Doğrulama kuralını çalıştırır.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $this->invalidOrders = Order::whereIn('id', $value)
                                    ->where('status', '!=', 'SHP')
                                    ->pluck('id')
                                    ->toArray();

        if (!empty($this->invalidOrders)) {
            $fail('Aşağıdaki siparişler Kargolama aşamasında değil: ' . implode(', ', $this->invalidOrders));
        }
    }
}
