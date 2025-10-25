<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Streaming Site API",
 *     description="動画配信サイト向けのAPIです。ユーザー管理、認証、配信ステータスなどを提供します。"
 * )
 *
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 *   bearerFormat="JWT"
 * )
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
 * @OA\Schema(
 *   schema="User",
 *   type="object",
 *   @OA\Property(property="id", type="string", format="uuid"),
 *   @OA\Property(property="name", type="string"),
 *   @OA\Property(property="email", type="string", format="email"),
 *   @OA\Property(property="roles", type="string"),
 *   @OA\Property(property="is_stream", type="boolean"),
 *   @OA\Property(property="bio", type="string", nullable=true),
 *   @OA\Property(property="profile_image_path", type="string", nullable=true),
 *   @OA\Property(property="cover_image_path", type="string", nullable=true),
 *   @OA\Property(property="channel_name", type="string", nullable=true),
 *   @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 * )
 *
 * @OA\Schema(
 *   schema="PaginatedUser",
 *   type="object",
 *   @OA\Property(property="current_page", type="integer", example=1),
 *   @OA\Property(
 *     property="data",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/User")
 *   ),
 *   @OA\Property(property="first_page_url", type="string"),
 *   @OA\Property(property="from", type="integer", nullable=true),
 *   @OA\Property(property="last_page", type="integer"),
 *   @OA\Property(property="last_page_url", type="string"),
 *   @OA\Property(property="links", type="array", @OA\Items(type="object")),
 *   @OA\Property(property="next_page_url", type="string", nullable=true),
 *   @OA\Property(property="path", type="string"),
 *   @OA\Property(property="per_page", type="integer"),
 *   @OA\Property(property="prev_page_url", type="string", nullable=true),
 *   @OA\Property(property="to", type="integer", nullable=true),
 *   @OA\Property(property="total", type="integer")
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs;
}
