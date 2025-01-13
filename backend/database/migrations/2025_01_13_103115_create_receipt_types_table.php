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
        Schema::create('receipt_types', function (Blueprint $table) {
            $table->id();
            $table->string("receipt_head")->nullable();
            $table->string("receipt_type")->nullable();
            $table->date("special_date")->nullable();
            $table->decimal("minimum_amount",10,2)->nullable();
            $table->boolean("is_pooja")->nullable();
            $table->boolean("show_special_date")->nullable();
            $table->boolean("show_remembarance")->nullable();
            $table->string("list_order",5)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_types');
    }
};