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
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->id();
            $table->string('id_payment')->nullable();
            $table->string('token')->nullable();
            $table->string('description')->nullable();
            $table->string('type_document')->nullable();
            $table->string('number_document')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->nullable();
            $table->string('status_detail')->nullable();
            $table->float('transaction_amount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_cards');
    }
};
