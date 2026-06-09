<?php

namespace Modules\Stripe\Tests\Fakes;

use Modules\Stripe\Services\StripeGateway;

class FakeStripeGateway implements StripeGateway
{
    public array $customers = [];
    public array $subscriptions = [];
    public array $invoices = [];
    public array $charges = [];

    public function findCustomers(string $email): array
    {
        return $this->customers;
    }

    public function listActiveSubscriptions(string $customerId): array
    {
        return $this->subscriptions;
    }

    public function listInvoices(string $customerId, int $limit): array
    {
        return array_slice($this->invoices, 0, $limit);
    }

    public function listCharges(string $customerId, int $limit): array
    {
        return array_slice($this->charges, 0, $limit);
    }
}
