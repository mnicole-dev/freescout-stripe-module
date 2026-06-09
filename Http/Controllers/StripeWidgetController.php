<?php

namespace Modules\Stripe\Http\Controllers;

use App\Conversation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Modules\Stripe\Entities\StripeSetting;
use Modules\Stripe\Services\StripeApiGateway;
use Modules\Stripe\Services\StripeService;

class StripeWidgetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($conversation)
    {
        $conversation = Conversation::findOrFail($conversation);
        $this->authorize('viewCached', $conversation);

        $setting = StripeSetting::where('mailbox_id', $conversation->mailbox_id)->first();
        if (!$setting || !$setting->stripe_secret_key) {
            return response('', 204); // no key for this mailbox → render nothing
        }

        $email = $conversation->customer_email
            ?: (optional($conversation->customer)->getMainEmail() ?? '');

        if ($email === '') {
            return view('stripe::widget', ['data' => null, 'error' => null]);
        }

        try {
            $key = Crypt::decryptString($setting->stripe_secret_key);
            $data = Cache::remember(
                'stripe.widget.' . $conversation->mailbox_id . '.' . md5($email),
                60,
                fn () => (new StripeService(new StripeApiGateway($key)))->getCustomerData($email)
            );
            return view('stripe::widget', ['data' => $data, 'error' => null]);
        } catch (\Throwable $e) {
            \Log::error('[stripe-module] widget error: ' . $e->getMessage());
            return view('stripe::widget', ['data' => null, 'error' => __('Could not load Stripe data.')]);
        }
    }
}
