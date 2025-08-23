<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID主キー
            $table->string('name')->unique(); // 名前（ユニーク）
            $table->string('email')->unique(); // ユニークメール（uniqueにindexが含まれているのでindex不要）
            $table->string('password'); // パスワード
            $table->string('profile_image_path')->nullable(); // プロフィール画像
            $table->string('cover_image_path')->nullable(); // カバー画像
            $table->text('bio')->nullable(); // 自己紹介
            $table->string('channel_name')->nullable(); // チャンネル名（任意）
            $table->boolean('is_streamer')->default(false); // 配信者フラグ
            $table->string('roles')->default('user')->index(); // ロール（検索のためindex）

            $table->timestamp('email_verified_at')->nullable(); // メール認証日時
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
