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
        Schema::create('anteshtee_amounts', function (Blueprint $table) {
            $table->id();
            $table->decimal("day_9_amount",10,2)->nullable();
            $table->decimal("day_10_amount",10,2)->nullable();
            $table->decimal("day_11_amount",10,2)->nullable();
            $table->decimal("day_12_amount",10,2)->nullable();
            $table->decimal("day_13_amount",10,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anteshtee_amounts');
    }
};