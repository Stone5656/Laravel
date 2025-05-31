<?php

namespace App\DataTransferObjects;

// ユーザーIDの型に合わせて use App\Models\User; なども検討できますが、
// CLIからの入力や将来的なリクエストのIDを想定し、ここではintとします。
// 必要であれば、UploadedFile 型なども追加します。

class VideoData
{
    public function __construct(
        public readonly int $userId,
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $originalFilePath, // CLIで元動画ファイルのパスを指定する場合
        public readonly ?string $visibility = 'private', // デフォルト値を設定
        // public readonly ?string $thumbnailPath = null, // サムネイルは後処理で生成する場合など
        // public readonly ?int $duration = null,       // 動画処理後に設定する場合
        // public readonly ?string $status = 'pending', // 初期ステータス
        // public readonly ?\DateTimeInterface $publishedAt = null
    ) {}
}