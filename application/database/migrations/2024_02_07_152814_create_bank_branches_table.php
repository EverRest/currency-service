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
        Schema::create('bank_branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_id');
            $table->string('external_id', 255)->nullable()->index();
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade');
            $table->string('address',255);
            $table->double('lat')->nullable()->index();
            $table->double('lng')->nullable()->index();
            $table->string('phone_number', 100)->nullable();
            $table->string('department_name', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_branches');
    }
};
