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
 * @OA\Schema(
 *   schema="Video",
 *   type="object",
 *   required={"id","user_id","title","is_public","views_count"},
 *   @OA\Property(property="id", type="string", format="uuid"),
 *   @OA\Property(property="user_id", type="string", format="uuid"),
 *   @OA\Property(property="title", type="string"),
 *   @OA\Property(property="description", type="string", nullable=true),
 *   @OA\Property(property="duration_sec", type="integer", nullable=true, example=123),
 *   @OA\Property(property="file_path", type="string", nullable=true),
 *   @OA\Property(property="thumbnail_path", type="string", nullable=true),
 *   @OA\Property(property="is_public", type="boolean"),
 *   @OA\Property(property="views_count", type="integer", example=3456),
 *   @OA\Property(property="published_at", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 * )
 *
 * @OA\Schema(
 *   schema="PublicVideo",
 *   type="object",
 *   required={"id","title","thumbnail_path","views_count","published_at","user_id"},
 *   @OA\Property(property="id", type="string", format="uuid"),
 *   @OA\Property(property="title", type="string"),
 *   @OA\Property(property="thumbnail_path", type="string", nullable=true),
 *   @OA\Property(property="views_count", type="integer", example=3456),
 *   @OA\Property(property="published_at", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="user_id", type="string", format="uuid")
 * )
 *
 * @OA\Schema(
 *   schema="PaginatedVideo",
 *   type="object",
 *   @OA\Property(property="current_page", type="integer", example=1),
 *   @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Video")),
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
 *
 * @OA\Schema(
 *   schema="PaginatedPublicVideo",
 *   type="object",
 *   @OA\Property(property="current_page", type="integer", example=1),
 *   @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PublicVideo")),
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
 *
 * @OA\Schema(
 *   schema="VideoCreateRequest",
 *   type="object",
 *   required={"title"},
 *   @OA\Property(property="title", type="string", maxLength=255),
 *   @OA\Property(property="description", type="string", nullable=true, maxLength=5000),
 *   @OA\Property(property="duration_sec", type="integer", nullable=true, minimum=0),
 *   @OA\Property(property="file_path", type="string", nullable=true, maxLength=1024),
 *   @OA\Property(property="thumbnail_path", type="string", nullable=true, maxLength=1024),
 *   @OA\Property(property="is_public", type="boolean", nullable=true),
 *   @OA\Property(property="published_at", type="string", format="date-time", nullable=true)
 * )
 *
 * @OA\Schema(
 *   schema="VideoUpdateRequest",
 *   type="object",
 *   @OA\Property(property="title", type="string", maxLength=255),
 *   @OA\Property(property="description", type="string", nullable=true, maxLength=5000),
 *   @OA\Property(property="duration_sec", type="integer", nullable=true, minimum=0),
 *   @OA\Property(property="file_path", type="string", nullable=true, maxLength=1024),
 *   @OA\Property(property="thumbnail_path", type="string", nullable=true, maxLength=1024),
 *   @OA\Property(property="is_public", type="boolean"),
 *   @OA\Property(property="published_at", type="string", format="date-time", nullable=true)
 * )
 *
 * @OA\Schema(
 *   schema="VideoPublishRequest",
 *   type="object",
 *   @OA\Property(property="published_at", type="string", format="date-time", nullable=true, description="ISO-8601。未指定なら現在時刻")
 * )
 *
 * @OA\Schema(
 *   schema="ValidationError",
 *   type="object",
 *   @OA\Property(property="message", type="string", example="The given data was invalid."),
 *   @OA\Property(
 *     property="errors",
 *     type="object",
 *     additionalProperties=@OA\Schema(type="array", @OA\Items(type="string")),
 *     example={"title": {"The title field is required."}}
 *   )
 * )
 *
 * @OA\Schema(
 *   schema="MessageResponse",
 *   type="object",
 *   @OA\Property(property="message", type="string", example="OK")
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs;
}
