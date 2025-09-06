<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * users テーブルを “1 本” に統合する migration。
 *
 * 設計方針:
 * - UUID 主キー
 * - name / email はユニーク
 * - プロフィール系・配信系・ロール・ロケール・連絡先などを1本化
 * - remember_token / softDeletes を追加（将来の運用を楽にするため）
 *
 * 既存にデータがある場合:
 * - 直接 drop せず、別途 ALTER 用の差分 migration を用意してください。
 * - 開発段階でデータが不要なら `php artisan migrate:fresh` でOK。
 */
return new class extends Migration
{
    /**
     * up():
     * マイグレーション適用時に実行される処理。
     * ここでは users テーブルを “新規作成” します。
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // =========================
            // [Primary & Auth Core]
            // =========================
            $table->uuid('id')->primary();          // UUID を主キーに採用
            $table->string('name')->unique();       // 表示名など。ユニーク運用（要件に応じて外して可）
            $table->string('email')->unique();      // ログイン用メール。ユニーク必須
            $table->string('password');             // ハッシュ済パスワード（Laravel 10+ なら hashed cast で管理）

            // =========================
            // [Profile & Channel]
            // =========================
            $table->string('profile_image_path')->nullable(); // プロフィール画像パス
            $table->string('cover_image_path')->nullable();   // カバー画像パス
            $table->text('bio')->nullable();                  // 自己紹介
            $table->string('channel_name')->nullable();       // 配信用チャンネル名（任意）
            // もしチャンネル名を一意にしたい場合は以下をコメント解除:
            // $table->unique('channel_name');

            // =========================
            // [Flags & Roles]
            // =========================
            $table->boolean('is_stream')->default(false);     // 配信中フラグ（モデル側も is_stream に合わせる）
            $table->string('roles')->default('user')->index(); // 役割（Enum の value を string 保持）

            // =========================
            // [Contact & Verification]
            // =========================
            $table->string('pending_email')->nullable();      // メール変更フロー用の仮メール
            $table->timestamp('email_verified_at')->nullable(); // メール認証日時（null=未認証）
            $table->rememberToken();                          // 「ログイン状態を保持する」トークン

            // =========================
            // [Locale & Preferences]
            // =========================
            $table->string('language')->nullable();           // 表示言語 (例: 'ja', 'en')
            $table->string('time_zone')->nullable();          // タイムゾーン (例: 'Asia/Tokyo')
            $table->string('phone_number')->nullable();       // 電話番号（必要に応じて unique 化）
            $table->date('birth_day')->nullable();            // 生年月日

            // =========================
            // [Lifecycle & Audit]
            // =========================
            $table->timestamps();                             // created_at / updated_at
            $table->softDeletes();                            // 論理削除（運用で復元/監査を可能に）
        });
    }

    /**
     * down():
     * マイグレーションを「ロールバック」する際に実行される処理。
     * ここでは users テーブルを “削除” します。
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
