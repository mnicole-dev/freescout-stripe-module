<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStripeSettingsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('stripe_settings')) {
            return; // reuse the table left by the previous (weDevs) module — keep existing keys
        }
        Schema::create('stripe_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->text('stripe_secret_key')->nullable();
            $table->unsignedBigInteger('mailbox_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        // Do not drop on rollback: the table may predate this module (shared with weDevs data).
    }
}
