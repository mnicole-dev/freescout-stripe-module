<?php

namespace Modules\Stripe\Services;

interface StripeGateway
{
    /**
     * Customers matching an email. Each item is an assoc array with keys:
     * id, name, email, currency, created, balance.
     * @return array<int, array<string, mixed>>
     */
    public function findCustomers(string $email): array;

    /**
     * Active subscriptions. Keys: status, plan, amount(int cents), currency, current_period_end.
     * @return array<int, array<string, mixed>>
     */
    public function listActiveSubscriptions(string $customerId): array;

    /**
     * Recent invoices. Keys: number, amount(int cents), currency, status, created, url.
     * @return array<int, array<string, mixed>>
     */
    public function listInvoices(string $customerId, int $limit): array;

    /**
     * Recent charges. Keys: amount(int cents), currency, status, created.
     * @return array<int, array<string, mixed>>
     */
    public function listCharges(string $customerId, int $limit): array;
}
