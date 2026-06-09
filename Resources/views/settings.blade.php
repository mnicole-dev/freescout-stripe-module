@extends('layouts.app')

@section('title_full', __('Stripe').' - '.$mailbox->name)

@section('sidebar')
    @include('partials/sidebar_menu_toggle')
    @include('mailboxes/sidebar_menu')
@endsection

@section('content')
<div class="section-heading">{{ __('Stripe') }}</div>
<div class="row-container">
    <div class="row">
        <div class="col-xs-12">
            <form class="form-horizontal margin-top" method="POST" action="{{ route('stripe.settings.update', $mailbox->id) }}">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                <div class="form-group">
                    <label class="col-sm-2 control-label">{{ __('Restricted API Key') }}</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="stripe_secret_key"
                               value="{{ $secret_key }}" placeholder="rk_live_…">
                        <p class="form-help">{!! __('Recommended: a <a href="https://dashboard.stripe.com/apikeys" target="_blank">restricted API key</a> with <strong>read</strong> permission on Customers, Subscriptions, Invoices and Charges. A full secret key also works. Stored encrypted.') !!}</p>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-6 col-sm-offset-2">
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        @if ($secret_key)
                            <button type="submit" class="btn btn-link text-danger"
                                    formaction="{{ route('stripe.settings.destroy', $mailbox->id) }}"
                                    onclick="this.closest('form').querySelector('[name=_method]').value='DELETE'">
                                {{ __('Remove') }}
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
