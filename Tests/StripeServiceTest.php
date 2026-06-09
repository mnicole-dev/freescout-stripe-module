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
}
