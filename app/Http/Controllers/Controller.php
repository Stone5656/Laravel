<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;  // ← これを追加
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Streaming Site API",
 *     description="動画配信サイト向けのAPIです。ユーザー管理、認証、配信ステータスなどを提供します。"
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Laravel 12 Sail　ローカル開発環境"
 * )
 *
 * @OA\Tag(
 *     name="Users",
 *     description="カスタムしたユーザー関連操作"
 * )
 *
 * @OA\Tag(
 *     name="Auth",
 *     description="Laravel Breezeからとってきた認証・認可関連操作"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs;
}
