<?php

namespace Modules\Stripe\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mailbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Modules\Stripe\Entities\StripeSetting;
use Modules\Stripe\Services\StripeApiGateway;

class StripeSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($mailbox)
    {
        $mailbox = Mailbox::findOrFail($mailbox);
        $this->authorize('update', $mailbox);

        $setting = StripeSetting::where('mailbox_id', $mailbox->id)->first();
        $secretKey = '';
        if ($setting && $setting->stripe_secret_key) {
            try {
                $secretKey = Crypt::decryptString($setting->stripe_secret_key);
            } catch (\Throwable $e) {
                $secretKey = '';
            }
        }

        return view('stripe::settings', ['mailbox' => $mailbox, 'secret_key' => $secretKey]);
    }

    public function update(Request $request, $mailbox)
    {
        $mailbox = Mailbox::findOrFail($mailbox);
        $this->authorize('update', $mailbox);

        $this->validate($request, ['stripe_secret_key' => 'required|max:250']);

        if (!StripeApiGateway::verifyKey($request->stripe_secret_key)) {
            \Session::flash('flash_error_floating', __('Invalid Stripe API key.'));
            return redirect()->back();
        }

        StripeSetting::updateOrCreate(
            ['mailbox_id' => $mailbox->id],
            ['stripe_secret_key' => Crypt::encryptString($request->stripe_secret_key)]
        );

        \Session::flash('flash_success_floating', __('Stripe API key saved.'));
        return redirect()->route('stripe.settings', $mailbox->id);
    }

    public function destroy(Request $request, $mailbox)
    {
        $mailbox = Mailbox::findOrFail($mailbox);
        $this->authorize('update', $mailbox);

        StripeSetting::where('mailbox_id', $mailbox->id)->delete();

        \Session::flash('flash_success_floating', __('Stripe API key removed.'));
        return redirect()->route('stripe.settings', $mailbox->id);
    }
}
