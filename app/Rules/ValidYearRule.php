<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class ValidYearRule implements ValidationRule
{
    /**
     * Geçerli yılı saklayalım.
     */
    protected $currentYear;

    public function __construct()
    {
        $this->currentYear = Carbon::now()->year;
    }

    /**
     * Doğrulama mantığı
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_numeric($value) || $value < 2000 || $value > $this->currentYear) {
            $fail("Yıl 2000 ile {$this->currentYear} arasında olmalıdır.");
        }
    }
}
