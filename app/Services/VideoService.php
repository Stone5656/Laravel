<?php

namespace App\Services;

use App\Models\Video;
use App\Repositories\VideoRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Validation\UnauthorizedException;

class VideoService
{
    public function __construct(private VideoRepository $videos) {}

    // 1) 公開API
    public function getVideo(string $id, ?string $requestUserId = null): ?Video
    {
        return $this->videos->getByIdPublicOrOwner($id, $requestUserId);
    }

    public function searchPublicVideos(array $filters): LengthAwarePaginator
    {
        $per = (int)($filters['per_page'] ?? 20);
        return $this->videos->searchPublic($filters, $per);
    }

    public function getPublicVideosByUser(string $userId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->videos->getPublicByUser($userId, $perPage);
    }

    public function getPopularVideos(int $perPage = 20): LengthAwarePaginator
    {
        return $this->videos->getPopular($perPage);
    }

    public function getRecentVideos(int $perPage = 20): LengthAwarePaginator
    {
        return $this->videos->getRecent($perPage);
    }

    public function incrementViews(string $id): void
    {
        $this->videos->incrementViews($id);
    }

    // 2) 認証API
    public function getMyVideos(string $userId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->videos->getMine($userId, $perPage);
    }

    public function createVideo(string $userId, array $data): Video
    {
        return $this->videos->create($userId, $data);
    }

    public function updateVideo(string $videoId, array $data, ?string $actorId = null): Video
    {
        $video = $this->videos->findById($videoId);
        if (!$video) abort(404, 'Video not found');
        if ($actorId && $video->user_id !== $actorId) {
            throw new UnauthorizedException('Not owner');
        }
        return $this->videos->update($video, $data);
    }

    public function deleteVideo(string $videoId, ?string $actorId = null): void
    {
        $video = $this->videos->findById($videoId);
        if (!$video) abort(404, 'Video not found');
        if ($actorId && $video->user_id !== $actorId) {
            throw new UnauthorizedException('Not owner');
        }
        $this->videos->delete($video);
    }

    public function publishVideo(string $videoId, ?string $iso8601, ?string $actorId = null): void
    {
        $video = $this->videos->findById($videoId);
        if (!$video) abort(404, 'Video not found');
        if ($actorId && $video->user_id !== $actorId) {
            throw new UnauthorizedException('Not owner');
        }
        $dt = $iso8601 ? Carbon::parse($iso8601) : null;
        $this->videos->publish($video, $dt);
    }

    public function unpublishVideo(string $videoId, ?string $actorId = null): void
    {
        $video = $this->videos->findById($videoId);
        if (!$video) abort(404, 'Video not found');
        if ($actorId && $video->user_id !== $actorId) {
            throw new UnauthorizedException('Not owner');
        }
        $this->videos->unpublish($video);
    }

    // 3) 管理者専用
    public function restoreVideo(string $videoId): Video
    {
        $video = $this->videos->restore($videoId);
        if (!$video) abort(404, 'Video not found');
        return $video;
    }
}
