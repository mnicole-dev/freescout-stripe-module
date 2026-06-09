<?php

namespace Modules\Stripe\Tests;

use Modules\Stripe\Services\StripeService;
use Modules\Stripe\Tests\Fakes\FakeStripeGateway;
use PHPUnit\Framework\TestCase;

class StripeServiceTest extends TestCase
{
    public function test_format_amount_converts_cents_and_appends_currency(): void
    {
        $this->assertSame('10.50 EUR', StripeService::formatAmount(1050, 'eur'));
        $this->assertSame('0.00 USD', StripeService::formatAmount(0, 'usd'));
        $this->assertSame('1 234.56 EUR', StripeService::formatAmount(123456, 'eur'));
    }

    public function test_find_customer_returns_null_when_email_blank(): void
    {
        $service = new StripeService(new FakeStripeGateway());
        $this->assertNull($service->findCustomerByEmail(''));
    }

    public function test_find_customer_returns_null_when_no_match(): void
    {
        $service = new StripeService(new FakeStripeGateway());
        $this->assertNull($service->findCustomerByEmail('nobody@example.com'));
    }

    public function test_find_customer_returns_most_recent_when_multiple(): void
    {
        $gw = new FakeStripeGateway();
        $gw->customers = [
            ['id' => 'cus_old', 'name' => 'Old', 'email' => 'a@b.c', 'currency' => 'eur', 'created' => 100, 'balance' => 0],
            ['id' => 'cus_new', 'name' => 'New', 'email' => 'a@b.c', 'currency' => 'eur', 'created' => 200, 'balance' => 0],
        ];
        $service = new StripeService($gw);
        $this->assertSame('cus_new', $service->findCustomerByEmail('a@b.c')['id']);
    }

    public function test_get_customer_data_assembles_normalized_view_model(): void
    {
        $gw = new FakeStripeGateway();
        $gw->customers = [
            ['id' => 'cus_1', 'name' => 'Alice', 'email' => 'alice@b.c', 'currency' => 'eur', 'created' => 1, 'balance' => -500],
        ];
        $gw->subscriptions = [
            ['status' => 'active', 'plan' => 'Pro', 'amount' => 2000, 'currency' => 'eur', 'current_period_end' => 1750000000],
        ];
        $gw->invoices = [
            ['number' => 'INV-1', 'amount' => 2000, 'currency' => 'eur', 'status' => 'paid', 'created' => 1700000000, 'url' => 'https://inv'],
        ];
        $gw->charges = [
            ['amount' => 2000, 'currency' => 'eur', 'status' => 'succeeded', 'created' => 1700000001],
        ];

        $data = (new StripeService($gw))->getCustomerData('alice@b.c');

        $this->assertSame('Alice', $data['customer']['name']);
        $this->assertSame('-5.00 EUR', $data['customer']['balance']);
        $this->assertSame('20.00 EUR', $data['subscriptions'][0]['amount']);
        $this->assertSame('active', $data['subscriptions'][0]['status']);
        $this->assertSame('INV-1', $data['invoices'][0]['number']);
        $this->assertSame('https://inv', $data['invoices'][0]['url']);
        $this->assertSame('20.00 EUR', $data['charges'][0]['amount']);
    }

    public function test_get_customer_data_returns_null_without_customer(): void
    {
        $this->assertNull((new StripeService(new FakeStripeGateway()))->getCustomerData('x@y.z'));
    }
}
