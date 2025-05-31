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
        Schema::create('orders', function (Blueprint $table) {
            $table->string('id', 6)->primary();
            $table->string('bookstore_id', 6);
            $table->string('employee_id', 6);
            $table->unsignedBigInteger('sum_price'); // 合計金額
            $table->unsignedTinyInteger('order_detail'); // 注文明細数
            $table->date('delibariy_date'); // 配送日
            $table->date('order_date'); // 受注日
            $table->timestamps();
        
            $table->foreign('bookstore_id')->references('id')->on('bookstores');
            $table->foreign('employee_id')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
