<?php

namespace Modules\Stripe\Tests;

use Modules\Stripe\Services\StripeService;
use PHPUnit\Framework\TestCase;

class StripeServiceTest extends TestCase
{
    public function test_format_amount_converts_cents_and_appends_currency(): void
    {
        $this->assertSame('10.50 EUR', StripeService::formatAmount(1050, 'eur'));
        $this->assertSame('0.00 USD', StripeService::formatAmount(0, 'usd'));
        $this->assertSame('1 234.56 EUR', StripeService::formatAmount(123456, 'eur'));
    }
}
