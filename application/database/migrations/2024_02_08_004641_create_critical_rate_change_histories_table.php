<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('critical_rate_change_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('previous_exchange_rate_id');
            $table->unsignedBigInteger('new_exchange_rate_id');
            $table->timestamps();

            $table->foreign('previous_exchange_rate_id')
                ->references('id')
                ->on('exchange_rates')
                ->onDelete('cascade');

            $table->foreign('new_exchange_rate_id')
                ->references('id')
                ->on('exchange_rates')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('critical_rate_change_histories');
    }
};
