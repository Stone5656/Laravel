<?php

namespace App\Http\Controllers;

use App\Http\Requests\LiveStream\{
    LiveStreamCreateRequest,
    LiveStreamUpdateRequest,
    LiveStreamRescheduleRequest,
    LiveStreamMyListRequest,   // （必要なら）自分の配信一覧などを作る場合に使用
    LiveStreamDeleteRequest
};
use App\Http\Requests\LiveStream\PublicListRequest; // 公開一覧用の per_page など（任意）
use App\Http\Resources\LiveStreamResource;
use App\Services\LiveStreamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LiveStreamController extends Controller
{
    public function __construct(private LiveStreamService $liveStreamService) {}

    // ================================================================
    // 1) 公開API（Anonymous OK）
    //    - サービス層で公開可否（公開 or 所有者一致など）を判定する方針
    // ================================================================

    /**
     * @OA\Get(
     *   path="/api/live-streams/{id}",
     *   tags={"LiveStreams"},
     *   summary="ライブ配信詳細取得",
     *   description="指定IDのライブ配信情報を取得（公開/非公開の可否はサービス層で判定）",
     *   @OA\Parameter(
     *     name="id", in="path", required=true, description="ライブ配信ID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/LiveStream")),
     *   @OA\Response(response=404, description="見つかりません")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $stream = $this->liveStreamService->getLiveStreamById($id, auth()->id());
        if (!$stream) {
            abort(404, 'LiveStream not found');
        }
        return response()->json(new LiveStreamResource($stream));
    }

    /**
     * @OA\Get(
     *   path="/api/live-streams/user/{userId}",
     *   tags={"LiveStreams"},
     *   summary="ユーザー別ライブ一覧取得（公開のみ）",
     *   description="指定ユーザーIDに紐づく公開ライブ配信一覧（ページング）を返します。",
     *   @OA\Parameter(
     *     name="userId", in="path", required=true, description="ユーザーID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", minimum=1, maximum=100, default=20)),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PaginatedLiveStream"))
     * )
     */
    public function byUser(string $userId, Request $request): JsonResponse
    {
        $per = (int) $request->query('per_page', 20);
        $paginator = $this->liveStreamService->getLiveStreamsByUser($userId, $per);
        return response()->json(
            LiveStreamResource::collection($paginator)->response()->getData(true)
        );
    }

    /**
     * @OA\Get(
     *   path="/api/live-streams/user/{userId}/status",
     *   tags={"LiveStreams"},
     *   summary="ユーザー＆ステータス別ライブ一覧（公開のみ）",
     *   @OA\Parameter(
     *     name="userId", in="path", required=true, description="ユーザーID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\Parameter(
     *     name="status", in="query", required=true, description="配信ステータス",
     *     @OA\Schema(type="string", enum={"SCHEDULED","LIVE","ENDED","CANCELLED"})
     *   ),
     *   @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", minimum=1, maximum=100, default=20)),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PaginatedLiveStream"))
     * )
     */
    public function byUserAndStatus(string $userId, Request $request): JsonResponse
    {
        $status = (string) $request->query('status');
        $per    = (int) $request->query('per_page', 20);
        $paginator = $this->liveStreamService->getLiveStreamsByUserAndStatus($userId, $status, $per);
        return response()->json(
            LiveStreamResource::collection($paginator)->response()->getData(true)
        );
    }

    /**
     * @OA\Get(
     *   path="/api/live-streams/status",
     *   tags={"LiveStreams"},
     *   summary="ステータス別ライブ一覧（公開のみ）",
     *   @OA\Parameter(
     *     name="status", in="query", required=true, description="配信ステータス",
     *     @OA\Schema(type="string", enum={"SCHEDULED","LIVE","ENDED","CANCELLED"})
     *   ),
     *   @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", minimum=1, maximum=100, default=20)),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PaginatedLiveStream"))
     * )
     */
    public function byStatus(Request $request): JsonResponse
    {
        $status = (string) $request->query('status');
        $per    = (int) $request->query('per_page', 20);
        $paginator = $this->liveStreamService->getLiveStreamsByStatus($status, $per);
        return response()->json(
            LiveStreamResource::collection($paginator)->response()->getData(true)
        );
    }

    /**
     * @OA\Get(
     *   path="/api/live-streams/statuses",
     *   tags={"LiveStreams"},
     *   summary="複数ステータスのライブ一覧（公開のみ）",
     *   @OA\Parameter(
     *     name="statuses[]", in="query", required=true, explode=true,
     *     description="配信ステータスの配列（クエリは statuses[]=LIVE&statuses[]=SCHEDULED の形式）",
     *     @OA\Schema(type="array", @OA\Items(type="string", enum={"SCHEDULED","LIVE","ENDED","CANCELLED"}))
     *   ),
     *   @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", minimum=1, maximum=100, default=20)),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PaginatedLiveStream"))
     * )
     */
    public function byStatuses(Request $request): JsonResponse
    {
        $statuses = (array) $request->query('statuses', []);
        $per      = (int) $request->query('per_page', 20);
        $paginator = $this->liveStreamService->getLiveStreamsByStatuses($statuses, $per);
        return response()->json(
            LiveStreamResource::collection($paginator)->response()->getData(true)
        );
    }

    /**
     * @OA\Get(
     *   path="/api/live-streams/search",
     *   tags={"LiveStreams"},
     *   summary="タイトル検索＋ステータス指定（公開のみ）",
     *   @OA\Parameter(name="title", in="query", required=true, description="タイトル部分一致", @OA\Schema(type="string")),
     *   @OA\Parameter(name="status", in="query", required=true, description="配信ステータス", @OA\Schema(type="string", enum={"SCHEDULED","LIVE","ENDED","CANCELLED"})),
     *   @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", minimum=1, maximum=100, default=20)),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PaginatedLiveStream"))
     * )
     */
    public function searchByTitleAndStatus(Request $request): JsonResponse
    {
        $title  = (string) $request->query('title', '');
        $status = (string) $request->query('status', '');
        $per    = (int) $request->query('per_page', 20);
        $paginator = $this->liveStreamService->getLiveStreamsByTitleAndStatus($title, $status, $per);
        return response()->json(
            LiveStreamResource::collection($paginator)->response()->getData(true)
        );
    }

    /**
     * @OA\Get(
     *   path="/api/live-streams/key/{streamKey}",
     *   tags={"LiveStreams"},
     *   summary="StreamKeyから配信取得（公開のみ）",
     *   @OA\Parameter(
     *     name="streamKey", in="path", required=true, description="ストリームキー",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/LiveStream")),
     *   @OA\Response(response=404, description="見つかりません")
     * )
     */
    public function byStreamKey(string $streamKey): JsonResponse
    {
        $stream = $this->liveStreamService->getLiveStreamByStreamKey($streamKey);
        if (!$stream) {
            abort(404, 'LiveStream not found');
        }
        return response()->json(new LiveStreamResource($stream));
    }

    // ================================================================
    // 2) 認証API（JWT必須）
    //    - ルートで middleware: auth:api + permission:* を付与する前提
    // ================================================================

    /**
     * @OA\Post(
     *   path="/api/live-streams",
     *   tags={"LiveStreams"},
     *   summary="ライブ配信作成（要ログイン）",
     *   description="新規ライブ配信を作成。ストリームキーはサーバ側で自動生成。",
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/LiveStreamCreateRequest")),
     *   @OA\Response(response=201, description="作成成功", @OA\JsonContent(ref="#/components/schemas/LiveStream")),
     *   @OA\Response(response=401, description="未認証"),
     *   @OA\Response(response=403, description="権限がありません"),
     *   @OA\Response(response=422, description="バリデーションエラー", @OA\JsonContent(ref="#/components/schemas/ValidationError")))
     * )
     */
    public function store(LiveStreamCreateRequest $request): JsonResponse
    {
        $stream = $this->liveStreamService->createLiveStream((string) auth()->id(), $request->validated());
        return response()->json(new LiveStreamResource($stream), 201);
    }

    /**
     * @OA\Put(
     *   path="/api/live-streams/{id}",
     *   tags={"LiveStreams"},
     *   summary="ライブ配信情報更新（要ログイン）",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id", in="path", required=true, description="ライブ配信ID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/LiveStreamUpdateRequest")),
     *   @OA\Response(response=200, description="更新成功", @OA\JsonContent(ref="#/components/schemas/LiveStream")),
     *   @OA\Response(response=401, description="未認証"),
     *   @OA\Response(response=403, description="権限がありません（所有者でない等）"),
     *   @OA\Response(response=404, description="対象なし"),
     *   @OA\Response(response=422, description="バリデーションエラー", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function update(LiveStreamUpdateRequest $request, string $id): JsonResponse
    {
        $stream = $this->liveStreamService->updateLiveStream($id, $request->validated(), (string) auth()->id());
        return response()->json(new LiveStreamResource($stream));
    }

    /**
     * @OA\Put(
     *   path="/api/live-streams/{id}/reschedule",
     *   tags={"LiveStreams"},
     *   summary="ライブ配信スケジュール更新（要ログイン）",
     *   description="予定日時やステータスを更新（延期/変更など）。",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id", in="path", required=true, description="ライブ配信ID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/LiveStreamRescheduleRequest")),
     *   @OA\Response(response=200, description="更新成功", @OA\JsonContent(ref="#/components/schemas/LiveStream")),
     *   @OA\Response(response=401, description="未認証"),
     *   @OA\Response(response=403, description="権限がありません（所有者でない等）"),
     *   @OA\Response(response=404, description="対象なし"),
     *   @OA\Response(response=422, description="バリデーションエラー", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function reschedule(LiveStreamRescheduleRequest $request, string $id): JsonResponse
    {
        $stream = $this->liveStreamService->rescheduleLiveStream($id, $request->validated(), (string) auth()->id());
        return response()->json(new LiveStreamResource($stream));
    }

    /**
     * @OA\Post(
     *   path="/api/live-streams/{id}/open",
     *   tags={"LiveStreams"},
     *   summary="ライブ開始（要ログイン）",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id", in="path", required=true, description="ライブ配信ID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\Response(response=200, description="開始成功", @OA\JsonContent(ref="#/components/schemas/LiveStream")),
     *   @OA\Response(response=401, description="未認証"),
     *   @OA\Response(response=403, description="権限がありません（所有者でない等）"),
     *   @OA\Response(response=404, description="対象なし")
     * )
     */
    public function open(string $id): JsonResponse
    {
        $stream = $this->liveStreamService->openLiveStream($id, (string) auth()->id());
        return response()->json(new LiveStreamResource($stream));
    }

    /**
     * @OA\Post(
     *   path="/api/live-streams/{id}/close",
     *   tags={"LiveStreams"},
     *   summary="ライブ終了（要ログイン）",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id", in="path", required=true, description="ライブ配信ID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\Response(response=200, description="終了成功", @OA\JsonContent(ref="#/components/schemas/LiveStream")),
     *   @OA\Response(response=401, description="未認証"),
     *   @OA\Response(response=403, description="権限がありません（所有者でない等）"),
 *   @OA\Response(response=404, description="対象なし")
     * )
     */
    public function close(string $id): JsonResponse
    {
        $stream = $this->liveStreamService->closeLiveStream($id, (string) auth()->id());
        return response()->json(new LiveStreamResource($stream));
    }

    /**
     * @OA\Post(
     *   path="/api/live-streams/{id}/cancel",
     *   tags={"LiveStreams"},
     *   summary="ライブキャンセル（要ログイン）",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id", in="path", required=true, description="ライブ配信ID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\Response(response=200, description="キャンセル成功", @OA\JsonContent(ref="#/components/schemas/LiveStream")),
     *   @OA\Response(response=401, description="未認証"),
     *   @OA\Response(response=403, description="権限がありません（所有者でない等）"),
     *   @OA\Response(response=404, description="対象なし")
     * )
     */
    public function cancel(string $id): JsonResponse
    {
        $stream = $this->liveStreamService->cancelLiveStream($id, (string) auth()->id());
        return response()->json(new LiveStreamResource($stream));
    }

    /**
     * @OA\Delete(
     *   path="/api/live-streams/{id}",
     *   tags={"LiveStreams"},
     *   summary="ライブ配信削除（要ログイン）",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id", in="path", required=true, description="ライブ配信ID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\Response(response=204, description="削除成功"),
     *   @OA\Response(response=401, description="未認証"),
     *   @OA\Response(response=403, description="権限がありません（所有者でない等）"),
     *   @OA\Response(response=404, description="対象なし")
     * )
     */
    public function destroy(LiveStreamDeleteRequest $request, string $id): JsonResponse
    {
        $this->liveStreamService->deleteLiveStream($id, (string) auth()->id());
        return response()->json([], 204);
    }
}
