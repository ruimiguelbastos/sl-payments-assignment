<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_id')->unique();
            $table->string('email')->unique();
            $table->string('name');
            $table->timestamps();
        });
        
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_id')->unique();
            $table->string('name');
            $table->timestamps();
        });
        
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_id')->unique();
            $table->string('name');
            $table->float('amount_off')->nullable();
            $table->float('percent_off')->nullable();
            $table->enum('currency', ['usd', 'gbp', 'eur']);
            $table->unsignedInteger('duration_in_months');
            $table->timestamps();
        });
        
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('stripe_id')->unique();
            $table->float('amount');
            $table->enum('interval', ['day','week','month','year']);
            $table->integer('interval_count');
            $table->enum('currency', ['usd', 'gbp', 'eur']);
            $table->foreign('product_id')->references('id')->on('products');
            $table->timestamps();
        });
        
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('plan_id');
            $table->string('stripe_id')->unique();
            $table->timestamp('start_date');
            $table->timestamp('current_period_start');
            $table->timestamp('current_period_end');
            $table->timestamp('trial_start')->nullable();
            $table->timestamp('trial_end')->nullable();
            $table->timestamp('cancel_at')->nullable();
            $table->enum('currency', ['usd', 'gbp', 'eur']);
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('plan_id')->references('id')->on('plans');
            $table->timestamps();
        });
        
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('coupon_id');
            $table->string('stripe_id')->unique();
            $table->timestamp('start');
            $table->timestamp('end');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
            $table->foreign('coupon_id')->references('id')->on('coupons');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('products');
        Schema::dropIfExists('customers');
    }
};
