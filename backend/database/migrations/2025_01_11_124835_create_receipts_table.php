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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receipt_type_id')->nullable(); 
            $table->string('receipt_no',20)->nullable(); 
            $table->date('receipt_date')->nullable(); 
            $table->string('name')->nullable(); 
            $table->string('gotra')->nullable(); 
            $table->string('address')->nullable(); 
            $table->string('pincode',50)->nullable(); 
            $table->string('mobile',20)->nullable(); 
            $table->string('email')->nullable(); 
            $table->string('narration')->nullable(); 
            $table->string('payment_mode',50)->nullable(); 
            $table->string('check_no',50)->nullable(); 
            $table->date('check_date')->nullable(); 
            $table->string('bank_details',100)->nullable(); 
            $table->date('special_date')->nullable(); 
            $table->string('remembarance')->nullable(); 
            $table->decimal('amount',18,2)->nullable(); 
            $table->string('amount_in_words')->nullable(); 
            $table->boolean('print_count')->nullable(); 
            $table->boolean('cancelled')->nullable(); 
            $table->unsignedBigInteger('cancelled_by')->nullable(); 
            $table->unsignedBigInteger('created_by')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};