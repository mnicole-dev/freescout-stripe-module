<div id="stripe-widget" class="stripe-widget" data-url="{{ route('stripe.widget', $conversation->id) }}">
    <div class="stripe-widget-loading text-help">{{ __('Loading Stripe…') }}</div>
</div>
<script>
(function () {
    var el = document.getElementById('stripe-widget');
    if (!el) return;
    fetch(el.getAttribute('data-url'), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function (r) { return r.status === 204 ? '' : r.text(); })
        .then(function (html) { el.innerHTML = html; })
        .catch(function () { el.innerHTML = ''; });
})();
</script>
