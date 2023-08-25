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
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->uuid("id")->primary();

            $table->bigInteger('quantity');

            $table->foreignUuid('user_id');
            $table->foreignUuid('product_id');
            $table->foreignUuid('transaction_id');

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('transaction_details', function ($table) {
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('transaction_details', function ($table) {
            $table->foreign('product_id')->references('id')->on('products');
        });

        Schema::table('transaction_details', function ($table) {
            $table->foreign('transaction_id')->references('id')->on('transactions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
