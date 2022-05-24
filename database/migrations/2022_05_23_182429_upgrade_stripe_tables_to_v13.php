<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'stripe_plan')) {
                $table->renameColumn('stripe_plan', 'stripe_price');
            }
        });

        Schema::table('subscription_items', function (Blueprint $table) {
            if (Schema::hasColumn('subscription_items', 'stripe_plan')) {
                $table->renameColumn('stripe_plan', 'stripe_price');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'card_brand')) {
                $table->renameColumn('card_brand', 'pm_type');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'card_last_four')) {
                $table->renameColumn('card_last_four', 'pm_last_four');
            }
        });

        Schema::table('subscription_items', function (Blueprint $table) {
            if (! Schema::hasColumn('subscription_items', 'stripe_product')) {
                $table->string('stripe_product')->nullable()->after('stripe_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->renameColumn('stripe_price', 'stripe_plan');
        });

        Schema::table('subscription_items', function (Blueprint $table) {
            $table->renameColumn('stripe_price', 'stripe_plan');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('pm_type', 'card_brand');
            $table->renameColumn('pm_last_four', 'card_last_four');
        });

        Schema::table('subscription_items', function (Blueprint $table) {
            $table->dropColumn('stripe_product');
        });
    }
};
