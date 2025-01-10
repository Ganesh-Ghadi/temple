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
        Schema::create('denominations', function (Blueprint $table) {
            $table->id();
            $table->date("deposit_date")->nullable();
            $table->integer("n_2000")->nullable();
            $table->integer("n_500")->nullable();
            $table->integer("n_200")->nullable();
            $table->integer("n_100")->nullable();
            $table->integer("n_50")->nullable();
            $table->integer("n_20")->nullable();
            $table->integer("n_10")->nullable();
            $table->integer("c_20")->nullable();
            $table->integer("c_10")->nullable();
            $table->integer("c_5")->nullable();
            $table->integer("c_2")->nullable();
            $table->integer("c_1")->nullable();
            $table->decimal("amount",10,2)->nullable();
            $table->string("denomination_file")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denominations');
    }
};