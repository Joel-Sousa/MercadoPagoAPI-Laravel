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
        Schema::create('bank_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('id_payment')->nullable();
            $table->string('_token')->nullable();
            $table->string('description')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('identification_type')->nullable();
            $table->string('identification_number')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('street')->nullable();
            $table->string('street_number')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
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
        Schema::dropIfExists('bank_tickets');
    }
};
