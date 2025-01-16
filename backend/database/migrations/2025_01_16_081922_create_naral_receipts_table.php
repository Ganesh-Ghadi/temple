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
        Schema::create('naral_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receipt_id'); 
            $table->integer('quantity'); 
            $table->decimal('rate',10,2); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('naral_receipts');
    }
};