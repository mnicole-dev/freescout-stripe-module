@if (!empty($error))
    <div class="stripe-widget"><div class="stripe-empty">{{ $error }}</div></div>
@elseif (empty($data))
    <div class="stripe-widget"><div class="stripe-empty">{{ __('No Stripe customer') }}</div></div>
@else
    <div class="stripe-widget">
        <h4>{{ __('Stripe') }}</h4>
        <div class="stripe-row"><strong>{{ $data['customer']['name'] ?: $data['customer']['email'] }}</strong></div>
        <div class="stripe-row"><span>{{ __('Balance') }}</span><span>{{ $data['customer']['balance'] }}</span></div>

        @if (count($data['subscriptions']))
            <h4>{{ __('Subscriptions') }}</h4>
            @foreach ($data['subscriptions'] as $s)
                <div class="stripe-row">
                    <span>{{ $s['plan'] }} <span class="stripe-status">{{ $s['status'] }}</span></span>
                    <span>{{ $s['amount'] }}</span>
                </div>
            @endforeach
        @endif

        @if (count($data['invoices']))
            <h4>{{ __('Invoices') }}</h4>
            @foreach ($data['invoices'] as $i)
                <div class="stripe-row">
                    <span>@if ($i['url'])<a href="{{ $i['url'] }}" target="_blank">{{ $i['number'] ?: __('Invoice') }}</a>@else {{ $i['number'] }} @endif <span class="stripe-status">{{ $i['status'] }}</span></span>
                    <span>{{ $i['amount'] }}</span>
                </div>
            @endforeach
        @endif

        @if (count($data['charges']))
            <h4>{{ __('Payments') }}</h4>
            @foreach ($data['charges'] as $c)
                <div class="stripe-row">
                    <span class="stripe-status">{{ $c['status'] }}</span>
                    <span>{{ $c['amount'] }}</span>
                </div>
            @endforeach
        @endif
    </div>
@endif
