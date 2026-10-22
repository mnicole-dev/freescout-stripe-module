<?php

namespace Modules\Stripe\Providers;

use Illuminate\Support\ServiceProvider;

class StripeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'stripe');
        // FreeScout modules translate with English strings as keys, resolved from JSON
        // files (fr.json, de.json…). A per-locale PHP array under lang/fr/ is never
        // consulted by __(), which is why every label stayed in English.
        $this->loadJsonTranslationsFrom(__DIR__ . '/../Resources/lang');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->hooks();
    }

    public function register()
    {
    }

    public function hooks()
    {
        // CSS
        \Eventy::addFilter('stylesheets', function ($styles) {
            $styles[] = asset('modules/stripe/css/stripe.css');
            return $styles;
        });

        // Link in the mailbox settings menu
        \Eventy::addAction('mailboxes.settings.menu', function ($mailbox) {
            echo \View::make('stripe::partials.settings_menu', ['mailbox' => $mailbox])->render();
        });

        // Async placeholder in the conversation customer sidebar
        \Eventy::addAction('customer.profile.extra', function ($customer, $conversation = null) {
            if (!$conversation) {
                return;
            }
            echo \View::make('stripe::partials.sidebar_placeholder', [
                'conversation' => $conversation,
            ])->render();
        }, 20, 2);
    }
}
