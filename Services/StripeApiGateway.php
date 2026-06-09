<?php

namespace Modules\Stripe\Services;

use Stripe\StripeClient;

class StripeApiGateway implements StripeGateway
{
    /** Pinned Stripe API version. Confirm latest stable before release. */
    public const API_VERSION = '2025-03-31.basil';

    private StripeClient $client;

    public function __construct(string $secretKey)
    {
        $this->client = new StripeClient([
            'api_key' => $secretKey,
            'stripe_version' => self::API_VERSION,
        ]);
    }

    /**
     * Validate an API key with a lightweight authenticated call.
     * Uses customers.list (Customers:read) so a restricted read-only key with
     * only the scopes this module needs is sufficient — no Balance scope required.
     */
    public static function verifyKey(string $secretKey): bool
    {
        try {
            (new StripeClient(['api_key' => $secretKey, 'stripe_version' => self::API_VERSION]))
                ->customers->all(['limit' => 1]);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function findCustomers(string $email): array
    {
        $res = $this->client->customers->all(['email' => $email, 'limit' => 100]);
        return array_map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'email' => $c->email,
            'currency' => $c->currency,
            'created' => $c->created,
            'balance' => $c->balance,
        ], $res->data);
    }

    public function listActiveSubscriptions(string $customerId): array
    {
        $res = $this->client->subscriptions->all([
            'customer' => $customerId, 'status' => 'active', 'limit' => 10,
        ]);
        return array_map(function ($s) {
            $price = $s->items->data[0]->price ?? null;
            return [
                'status' => $s->status,
                'plan' => $price->nickname ?? ($price->id ?? ''),
                'amount' => $price->unit_amount ?? 0,
                'currency' => $s->currency,
                'current_period_end' => $s->current_period_end,
            ];
        }, $res->data);
    }

    public function listInvoices(string $customerId, int $limit): array
    {
        $res = $this->client->invoices->all(['customer' => $customerId, 'limit' => $limit]);
        return array_map(fn ($i) => [
            'number' => $i->number,
            'amount' => $i->total,
            'currency' => $i->currency,
            'status' => $i->status,
            'created' => $i->created,
            'url' => $i->hosted_invoice_url,
        ], $res->data);
    }

    public function listCharges(string $customerId, int $limit): array
    {
        $res = $this->client->charges->all(['customer' => $customerId, 'limit' => $limit]);
        return array_map(fn ($c) => [
            'amount' => $c->amount,
            'currency' => $c->currency,
            'status' => $c->status,
            'created' => $c->created,
        ], $res->data);
    }
}
