# FreeScout Stripe Module

Show a customer's Stripe data — balance, active subscriptions, recent invoices and payments —
directly in the FreeScout conversation sidebar. Read-only. One Stripe API key per mailbox.

## Features
- Per-mailbox Stripe API key (stored encrypted) — use a read-only restricted key
- Customer matched by email (most recent Stripe customer wins)
- Sidebar panel loaded asynchronously — never blocks or breaks a conversation
- Short server-side caching (60s) to limit API calls
- i18n (English, French)

## Requirements
- FreeScout ≥ 1.8.0, PHP ≥ 8.1
- A Stripe account (one per mailbox if needed)

## Installation
```bash
cd Modules
git clone https://github.com/mnicole-dev/freescout-stripe-module Stripe
cd Stripe && composer install --no-dev --optimize-autoloader
```
Then activate **Stripe** in **Manage → Modules**, and set the API key per mailbox in
**Manage → Mailboxes → [mailbox] → Settings → Stripe**.

### Recommended: a restricted, read-only key
Rather than a full secret key, create a [restricted API key](https://dashboard.stripe.com/apikeys)
with **read** permission only on the resources this module uses:
**Customers**, **Subscriptions**, **Invoices**, **Charges** (all *Read*). This module never writes
to Stripe, so a read-only key is sufficient and safer. A full secret key (`sk_…`) also works.

## Two versions to keep straight

`stripe/stripe-php` (the package) and the Stripe API version (the `Stripe-Version`
header) move independently, and this module pins both.

The package constraint lives in `composer.json`. Keep it current: the 17.x line still
called `curl_close()`, which PHP 8.5 deprecates on every API call. That was fixed
upstream in v19.1.0 and the call is now guarded by a version check.

The API version is `StripeApiGateway::API_VERSION`. Pinning it is what stops Stripe
from reshaping the objects we read when the package is upgraded, so bump it on its own
and read the API changelog first. Fields do move: Basil took `current_period_start` and
`current_period_end` off the subscription and put them on each subscription item, so a
subscription can carry several billing cycles. Code reading them from the subscription
gets `null` with no error.

## License
AGPL-3.0 — see [LICENSE](LICENSE).
