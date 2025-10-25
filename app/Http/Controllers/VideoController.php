<?php

namespace App\Http\Controllers;

use App\Http\Requests\Video\{
    VideoSearchRequest,
    VideoCreateRequest,
    VideoUpdateRequest,
    VideoMyListRequest,
    VideoPublishRequest,
    VideoUnpublishRequest,
    VideoDeleteRequest,
    VideoRestoreRequest
};
use App\Http\Resources\{VideoResource, PublicVideoResource};
use App\Services\VideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class VideoController extends Controller
{
    public function __construct(private VideoService $videoService) {}

    // ----------------------------------------------------------------
    // 1) 公開API（匿名OK）: show / search / publicByUser / popular / recent / incrementViews
    // ----------------------------------------------------------------

    /**
     * @OA\Get(
     *   path="/api/videos/{id}",
     *   tags={"Videos"},
     *   summary="IDで動画を取得（公開/非公開はサービス層で可否判定）",
     *   description="公開動画 or 所有者一致の場合のみ返却します。",
     *   @OA\Parameter(
     *     name="id", in="path", required=true, description="動画ID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Video")),
     *   @OA\Response(response=404, description="見つかりません")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $video = $this->videoService->getVideo($id, auth()->id());
        if (!$video) {
            abort(404, 'Video not found');
        }
        return response()->json(new VideoResource($video));
    }

    /**
     * @OA\Get(
     *   path="/api/videos/search",
     *   tags={"Videos"},
     *   summary="公開動画の検索",
     *   description="タイトル部分一致、views_count/published_atでの降順ソート、ページングに対応します。",
     *   @OA\Parameter(name="title", in="query", required=false, description="タイトル部分一致", @OA\Schema(type="string", maxLength=255)),
     *   @OA\Parameter(name="sort",  in="query", required=false, description="ソートキー（views_count|published_at、降順固定）", @OA\Schema(type="string", enum={"views_count","published_at"})),
     *   @OA\Parameter(name="per_page", in="query", required=false, description="1ページあたり件数（1-100）", @OA\Schema(type="integer", minimum=1, maximum=100, default=20)),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PaginatedPublicVideo"))
     * )
     */
    public function search(VideoSearchRequest $request): JsonResponse
    {
        $paginator = $this->videoService->searchPublicVideos($request->validated());
        // Resourceのpaginate形をそのまま返却（メタを含む）:
        return response()->json(
            PublicVideoResource::collection($paginator)->response()->getData(true)
        );
    }

    /**
     * @OA\Get(
     *   path="/api/videos/user/{userId}",
     *   tags={"Videos"},
     *   summary="特定ユーザーの公開動画一覧",
     *   description="ユーザーIDに紐づく公開動画をページングで返します。",
     *   @OA\Parameter(
     *     name="userId", in="path", required=true, description="ユーザーID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\Parameter(name="per_page", in="query", required=false, description="1ページあたり件数（1-100）", @OA\Schema(type="integer", minimum=1, maximum=100, default=20)),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PaginatedPublicVideo")),
     *   @OA\Response(response=404, description="ユーザーが見つからない場合も空リストで返る想定")
     * )
     */
    public function publicByUser(string $userId, Request $request): JsonResponse
    {
        $per = (int) $request->query('per_page', 20);
        $paginator = $this->videoService->getPublicVideosByUser($userId, $per);
        return response()->json(
            PublicVideoResource::collection($paginator)->response()->getData(true)
        );
    }

    /**
     * @OA\Get(
     *   path="/api/videos/popular",
     *   tags={"Videos"},
     *   summary="人気動画一覧（再生数降順）",
     *   @OA\Parameter(name="per_page", in="query", required=false, description="1ページあたり件数（1-100）", @OA\Schema(type="integer", minimum=1, maximum=100, default=20)),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PaginatedPublicVideo"))
     * )
     */
    public function popular(Request $request): JsonResponse
    {
        $per = (int) $request->query('per_page', 20);
        $paginator = $this->videoService->getPopularVideos($per);
        return response()->json(
            PublicVideoResource::collection($paginator)->response()->getData(true)
        );
    }

    /**
     * @OA\Get(
     *   path="/api/videos/recent",
     *   tags={"Videos"},
     *   summary="新着動画一覧（公開日時降順）",
     *   @OA\Parameter(name="per_page", in="query", required=false, description="1ページあたり件数（1-100）", @OA\Schema(type="integer", minimum=1, maximum=100, default=20)),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PaginatedPublicVideo"))
     * )
     */
    public function recent(Request $request): JsonResponse
    {
        $per = (int) $request->query('per_page', 20);
        $paginator = $this->videoService->getRecentVideos($per);
        return response()->json(
            PublicVideoResource::collection($paginator)->response()->getData(true)
        );
    }

    /**
     * @OA\Patch(
     *   path="/api/videos/{id}/views",
     *   tags={"Videos"},
     *   summary="再生数をインクリメント（公開エンドポイント想定）",
     *   @OA\Parameter(
     *     name="id", in="path", required=true, description="動画ID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\Response(response=204, description="No Content"),
     *   @OA\Response(response=404, description="動画が見つからない場合は何もしない or 404（実装方針次第）")
     * )
     */
    public function incrementViews(string $id): JsonResponse
    {
        $this->videoService->incrementViews($id);
        return response()->json([], 204);
    }

    // ----------------------------------------------------------------
    // 2) 認証API（JWT必須）: my / store / update / destroy / publish / unpublish
    // ルート側で middleware: auth:api + permission:* を適用する前提
    // ----------------------------------------------------------------

    /**
     * @OA\Get(
     *   path="/api/videos/my",
     *   tags={"Videos"},
     *   summary="自分の動画一覧（要ログイン）",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="per_page", in="query", required=false, description="1ページあたり件数（1-100）", @OA\Schema(type="integer", minimum=1, maximum=100, default=20)),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PaginatedVideo")),
     *   @OA\Response(response=401, description="未認証"),
     *   @OA\Response(response=403, description="権限がありません")
     * )
     */
    public function my(VideoMyListRequest $request): JsonResponse
    {
        $per = (int) ($request->validated()['per_page'] ?? 20);
        $paginator = $this->videoService->getMyVideos((string) auth()->id(), $per);
        return response()->json(
            VideoResource::collection($paginator)->response()->getData(true)
        );
    }

    /**
     * @OA\Post(
     *   path="/api/videos/upload",
     *   tags={"Videos"},
     *   summary="動画をアップロード（要ログイン）",
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/VideoCreateRequest")
     *   ),
     *   @OA\Response(response=201, description="作成成功", @OA\JsonContent(ref="#/components/schemas/Video")),
     *   @OA\Response(response=401, description="未認証"),
     *   @OA\Response(response=403, description="権限がありません"),
     *   @OA\Response(response=422, description="バリデーションエラー", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(VideoCreateRequest $request): JsonResponse
    {
        $video = $this->videoService->createVideo((string) auth()->id(), $request->validated());
        return response()->json(new VideoResource($video), 201);
    }

    /**
     * @OA\Put(
     *   path="/api/videos/{id}",
     *   tags={"Videos"},
     *   summary="動画を更新（要ログイン）",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id", in="path", required=true, description="動画ID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/VideoUpdateRequest")),
     *   @OA\Response(response=200, description="更新成功", @OA\JsonContent(ref="#/components/schemas/Video")),
     *   @OA\Response(response=401, description="未認証"),
     *   @OA\Response(response=403, description="権限がありません（所有者でない等）"),
     *   @OA\Response(response=404, description="対象動画なし"),
     *   @OA\Response(response=422, description="バリデーションエラー", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function update(VideoUpdateRequest $request, string $id): JsonResponse
    {
        $video = $this->videoService->updateVideo($id, $request->validated(), (string) auth()->id());
        return response()->json(new VideoResource($video));
    }

    /**
     * @OA\Delete(
     *   path="/api/videos/{id}",
     *   tags={"Videos"},
     *   summary="動画を削除（要ログイン）",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id", in="path", required=true, description="動画ID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\Response(response=204, description="削除成功"),
     *   @OA\Response(response=401, description="未認証"),
     *   @OA\Response(response=403, description="権限がありません（所有者でない等）"),
     *   @OA\Response(response=404, description="対象動画なし")
     * )
     */
    public function destroy(VideoDeleteRequest $request, string $id): JsonResponse
    {
        $this->videoService->deleteVideo($id, (string) auth()->id());
        return response()->json([], 204);
    }

    /**
     * @OA\Patch(
     *   path="/api/videos/{id}/publish",
     *   tags={"Videos"},
     *   summary="動画を公開（要ログイン）",
     *   description="公開日時を指定し公開。未指定なら現在時刻。",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id", in="path", required=true, description="動画ID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\RequestBody(required=false, @OA\JsonContent(ref="#/components/schemas/VideoPublishRequest")),
     *   @OA\Response(response=204, description="公開成功"),
     *   @OA\Response(response=401, description="未認証"),
     *   @OA\Response(response=403, description="権限がありません（所有者でない等）"),
     *   @OA\Response(response=404, description="対象動画なし"),
     *   @OA\Response(response=422, description="バリデーションエラー", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function publish(VideoPublishRequest $request, string $id): JsonResponse
    {
        $this->videoService->publishVideo($id, $request->validated()['published_at'] ?? null, (string) auth()->id());
        return response()->json([], 204);
    }

    /**
     * @OA\Patch(
     *   path="/api/videos/{id}/unpublish",
     *   tags={"Videos"},
     *   summary="動画を非公開（要ログイン）",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id", in="path", required=true, description="動画ID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\Response(response=204, description="非公開成功"),
     *   @OA\Response(response=401, description="未認証"),
     *   @OA\Response(response=403, description="権限がありません（所有者でない等）"),
     *   @OA\Response(response=404, description="対象動画なし")
     * )
     */
    public function unpublish(VideoUnpublishRequest $request, string $id): JsonResponse
    {
        $this->videoService->unpublishVideo($id, (string) auth()->id());
        return response()->json([], 204);
    }

    // ----------------------------------------------------------------
    // 3) 管理者専用（routesで permission:video.restore を適用）
    // ----------------------------------------------------------------

    /**
     * @OA\Post(
     *   path="/api/videos/{id}/restore",
     *   tags={"Videos"},
     *   summary="削除済み動画の復元（管理者）",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id", in="path", required=true, description="動画ID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\Response(response=200, description="復元成功",
     *   @OA\JsonContent(ref="#/components/schemas/Video")),
     *   @OA\Response(response=401, description="未認証"),
     *   @OA\Response(response=403, description="権限がありません"),
     *   @OA\Response(response=404, description="対象動画なし")
     * )
     */
    public function restore(VideoRestoreRequest $request, string $id): JsonResponse
    {
        $video = $this->videoService->restoreVideo($id);
        return response()->json(new VideoResource($video));
    }
}
