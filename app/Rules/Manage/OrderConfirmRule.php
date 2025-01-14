<?php

namespace App\Rules\Manage;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Order;

class OrderConfirmRule implements ValidationRule
{
    /**
     * Beklenen sipariş durumu.
     *
     * @var string
     */
    protected string $expectedStatus;

    /**
     * Yeni Rule sınıfı oluşturur.
     *
     * @param string $expectedStatus Beklenen sipariş durumu
     */
    public function __construct(string $expectedStatus)
    {
        $this->expectedStatus = $expectedStatus;
    }

    /**
     * Doğrulama kuralını çalıştırır.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $order = Order::find($value);

        if (!$order) {
            $fail("Sipariş ID {$value} mevcut değil.");
            return;
        }


        if ($order->status === $this->expectedStatus) {

            $fail("Sipariş '{$order->order_name}' belirtilen durum aşamasında değil.");
        }
    }

}
