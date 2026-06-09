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

    public function getCustomerData(string $email): ?array
    {
        $customer = $this->findCustomerByEmail($email);
        if ($customer === null) {
            return null;
        }

        $cid = $customer['id'];
        $cur = $customer['currency'] ?: 'usd';

        $subs = $this->gateway->listActiveSubscriptions($cid);
        $invoices = $this->gateway->listInvoices($cid, 5);
        $charges = $this->gateway->listCharges($cid, 5);

        return [
            'customer' => [
                'id' => $customer['id'],
                'name' => $customer['name'] ?? '',
                'email' => $customer['email'] ?? '',
                'balance' => self::formatAmount((int) ($customer['balance'] ?? 0), $cur),
            ],
            'subscriptions' => array_map(fn ($s) => [
                'status' => $s['status'] ?? '',
                'plan' => $s['plan'] ?? '',
                'amount' => self::formatAmount((int) ($s['amount'] ?? 0), $s['currency'] ?? $cur),
                'current_period_end' => $s['current_period_end'] ?? null,
            ], $subs),
            'invoices' => array_map(fn ($i) => [
                'number' => $i['number'] ?? '',
                'amount' => self::formatAmount((int) ($i['amount'] ?? 0), $i['currency'] ?? $cur),
                'status' => $i['status'] ?? '',
                'created' => $i['created'] ?? null,
                'url' => $i['url'] ?? null,
            ], $invoices),
            'charges' => array_map(fn ($c) => [
                'amount' => self::formatAmount((int) ($c['amount'] ?? 0), $c['currency'] ?? $cur),
                'status' => $c['status'] ?? '',
                'created' => $c['created'] ?? null,
            ], $charges),
        ];
    }
}
