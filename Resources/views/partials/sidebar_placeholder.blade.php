<div id="stripe-widget" class="stripe-widget" data-url="{{ route('stripe.widget', $conversation->id) }}">
    <div class="stripe-widget-loading text-help">{{ __('Loading Stripe…') }}</div>
</div>
<script>
(function () {
    var el = document.getElementById('stripe-widget');
    if (!el) return;
    // A failure must stay visible: silently emptying the block makes a broken
    // widget indistinguishable from a customer with no Stripe account.
    var fail = function (reason) {
        console.error('[stripe-module] widget failed:', reason);
        el.innerHTML = '<div class="stripe-empty text-help">{{ __('Stripe unavailable') }}<\/div>';
    };
    fetch(el.getAttribute('data-url'), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function (r) {
            if (r.status === 204) return '';           // no key for this mailbox
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.text();
        })
        .then(function (html) { el.innerHTML = html; })
        .catch(fail);
})();
</script>
