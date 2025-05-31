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
        Schema::create('order_details', function (Blueprint $table) {
            $table->string('order_id', 6);
            $table->string('productbook_id', 6);
            $table->unsignedBigInteger('order_price');
            $table->unsignedSmallInteger('order_stock');
            $table->timestamps();
        
            $table->primary(['order_id', 'productbook_id']);
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('productbook_id')->references('id')->on('product_books');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
