<?php

namespace Modules\Stripe\Services;

class StripeService
{
    public function __construct(private StripeGateway $gateway)
    {
    }

    public function findCustomerByEmail(string $email): ?array
    {
        $customers = $email === '' ? [] : $this->gateway->findCustomers($email);
        if (empty($customers)) {
            return null;
        }
        usort($customers, fn ($a, $b) => ($b['created'] ?? 0) <=> ($a['created'] ?? 0));
        return $customers[0];
    }

    public static function formatAmount(int $amount, string $currency): string
    {
        return number_format($amount / 100, 2, '.', ' ') . ' ' . strtoupper($currency);
    }
}
