<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('subscription_bank', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('bank_id');
            $table->timestamps();
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade');
        });

        Schema::create('subscription_currency', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('currency_id');
            $table->timestamps();
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_bank');
        Schema::dropIfExists('subscription_currency');
        Schema::dropIfExists('subscriptions');
    }
};
