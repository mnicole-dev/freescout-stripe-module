<li class="@if (Route::currentRouteName() == 'stripe.settings') active @endif">
    <a href="{{ route('stripe.settings', $mailbox->id) }}">{{ __('Stripe') }}</a>
</li>
