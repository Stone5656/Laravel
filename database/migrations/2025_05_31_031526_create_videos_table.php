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
        Schema::create('videos', function (Blueprint $table) {
            $table->id(); // 主キー (PK)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // 投稿者ユーザーID (FK)
            $table->string('title'); // 動画タイトル
            $table->text('description')->nullable(); // 動画説明文
            $table->string('video_path'); // 動画ファイルの保存パス
            $table->string('thumbnail_path')->nullable(); // サムネイル画像の保存パス
            $table->integer('duration')->nullable(); // 動画の長さ（秒単位など）
            $table->string('visibility')->default('private'); // 公開設定 (public, private, unlistedなど)
            $table->string('status')->default('pending'); // 動画の状態 (pending, processing, published, failedなど)
            $table->unsignedBigInteger('views_count')->default(0); // 視聴回数
            $table->timestamp('published_at')->nullable(); // 公開日時
            $table->timestamps(); // created_at と updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};