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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->text('address')->nullable();
            $table->float('price_total')->default(0);
            $table->float('price_shipping')->default(0);
            $table->string('payment')->default("MANUAL");
            $table->string('status')->default("PENDING");

            $table->foreignUuid('user_id');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('transactions', function ($table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
