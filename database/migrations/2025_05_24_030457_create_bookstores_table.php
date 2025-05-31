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
        Schema::create('bookstores', function (Blueprint $table) {
            $table->string('id', 6)->primary(); // 書店ID
            $table->string('name', 32);
            $table->string('phone_number', 13);
            $table->string('post_code', 8);
            $table->string('address', 40);
            $table->unsignedTinyInteger('discount_rate'); // 割引率
            $table->boolean('delete_flag')->default(false); // 論理削除用
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookstores');
    }
};
