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

## License
AGPL-3.0 — see [LICENSE](LICENSE).
