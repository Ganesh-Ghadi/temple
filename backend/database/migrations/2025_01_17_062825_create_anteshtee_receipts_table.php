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
        Schema::create('anteshtee_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receipt_id'); 
            $table->string('guruji')->nullable();
            $table->string('yajman')->nullable();
            $table->string('karma_number',50)->nullable();
            $table->boolean('day_9')->nullable();
            $table->boolean('day_10')->nullable();
            $table->boolean('day_11')->nullable();
            $table->boolean('day_12')->nullable();
            $table->boolean('day_13')->nullable();
            $table->date('day_9_date')->nullable();
            $table->date('day_10_date')->nullable();
            $table->date('day_11_date')->nullable();
            $table->date('day_12_date')->nullable();
            $table->date('day_13_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anteshtee_receipts');
    }
};