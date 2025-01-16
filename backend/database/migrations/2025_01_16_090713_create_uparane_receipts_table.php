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
        Schema::create('uparane_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receipt_id'); 
            $table->date('uparane_draping_date')->nullable(); 
            $table->boolean('return_uparane')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uparane_receipts');
    }
};