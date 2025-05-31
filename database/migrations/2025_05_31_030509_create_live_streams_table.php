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
        Schema::create('live_streams', function (Blueprint $table) {
            $table->id(); // 主キー (PK)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // 外部キー (FK) usersテーブルを参照
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('stream_key')->unique(); // 配信キー (ユニーク制約)
            $table->string('status')->default('upcoming'); // 例: upcoming, live, ended
            $table->timestamp('scheduled_at')->nullable(); // 配信予定日時
            $table->timestamp('started_at')->nullable();   // 配信開始日時
            $table->timestamp('ended_at')->nullable();     // 配信終了日時
            $table->string('thumbnail_path')->nullable();  // サムネイル画像パス
            // $table->foreignId('archive_video_id')->nullable()->constrained('videos')->onDelete('set null'); // 外部キー (FK) videosテーブルを参照、null許容、参照先削除時null設定
            $table->timestamps(); // created_at と updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_streams');
    }
};