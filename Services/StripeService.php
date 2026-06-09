<?php

namespace Modules\Stripe\Services;

class StripeService
{
    public static function formatAmount(int $amount, string $currency): string
    {
        return number_format($amount / 100, 2, '.', ' ') . ' ' . strtoupper($currency);
    }
}
