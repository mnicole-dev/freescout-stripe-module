<?php

namespace Modules\Stripe\Entities;

use Illuminate\Database\Eloquent\Model;

class StripeSetting extends Model
{
    protected $table = 'stripe_settings';

    protected $fillable = ['mailbox_id', 'stripe_secret_key'];
}
