<?php

Route::group([
    'middleware' => 'web',
    'prefix' => \Helper::getSubdirectory(),
    'namespace' => 'Modules\Stripe\Http\Controllers',
], function () {
    Route::get('/stripe/settings/{mailbox}', 'StripeSettingsController@index')->name('stripe.settings');
    Route::put('/stripe/settings/{mailbox}', 'StripeSettingsController@update')->name('stripe.settings.update');
    Route::delete('/stripe/settings/{mailbox}', 'StripeSettingsController@destroy')->name('stripe.settings.destroy');

    Route::get('/stripe/widget/{conversation}', 'StripeWidgetController@show')->name('stripe.widget');
});
