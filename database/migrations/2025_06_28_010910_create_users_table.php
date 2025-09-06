<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\RoleEnum;
use App\Enums\UserStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // =========================
            // [Primary]
            // =========================
            $table->uuid('id')->primary();

            // =========================
            // [Auth Core]
            // =========================
            $table->string('name', 30)->unique();
            $table->string('email', 320)->unique();
            $table->string('password');

            // =========================
            // [Profile]
            // =========================
            $table->text('bio')->nullable();
            $table->string('channel_name', 255)->nullable();
            $table->string('profile_image_path', 1024)->nullable();
            $table->string('cover_image_path', 1024)->nullable();

            // =========================
            // [Flags & Roles]
            // =========================
            $table->boolean('is_streamer')->default(false);
            $table->string('role', 20)->default(RoleEnum::USER->value);
            $table->string('status', 20)->default(UserStatusEnum::ACTIVE->value);

            // =========================
            // [Verification]
            // =========================
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();

            // =========================
            // [Locale & Preferences]
            // =========================
            $table->string('language', 10)->nullable();
            $table->string('timezone', 100)->nullable();
            $table->string('phone_number', 30)->nullable();
            $table->date('birthday')->nullable();

            // =========================
            // [Login Info]
            // =========================
            $table->timestamp('last_login_at')->nullable();
            $table->integer('login_failure_count')->default(0);

            // =========================
            // [Relations]
            // =========================
            $table->uuid('primary_email_id')->nullable();

            // =========================
            // [Audit]
            // =========================
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
