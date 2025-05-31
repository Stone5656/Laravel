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
        Schema::create('product_books', function (Blueprint $table) {
            $table->string('id', 6)->primary();
            $table->string('name', 32);
            $table->unsignedInteger('price'); // 価格
            $table->unsignedBigInteger('stock'); // 在庫数
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_books');
    }
};
