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
        Schema::create('camp_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receipt_id'); 
            $table->string('member_name')->nullable(); 
            $table->date('from_date')->nullable(); 
            $table->date('to_date')->nullable(); 
            $table->boolean('Mallakhamb')->nullable(); 
            $table->boolean('zanj')->nullable(); 
            $table->boolean('dhol')->nullable(); 
            $table->boolean('lezim')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camp_receipts');
    }
};