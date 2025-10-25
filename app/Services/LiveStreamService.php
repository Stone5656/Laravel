<?php
// app/Services/LiveStreamService.php

namespace App\Services;

use App\Models\LiveStream;
use App\Repositories\LiveStreamRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\UnauthorizedException;

class LiveStreamService
{
    public function __construct(private LiveStreamRepository $streams) {}

    // 公開API
    public function getLiveStreamById(string $id, ?string $actorId = null): ?LiveStream
    {
        return $this->streams->getPublicOrOwner($id, $actorId);
    }

    public function getLiveStreamsByUser(string $userId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->streams->listByUser($userId, $perPage);
    }

    public function getLiveStreamsByUserAndStatus(string $userId, string $status, int $perPage = 20): LengthAwarePaginator
    {
        return $this->streams->listByUserAndStatus($userId, $status, $perPage);
    }

    public function getLiveStreamsByStatus(string $status, int $perPage = 20): LengthAwarePaginator
    {
        return $this->streams->listByStatus($status, $perPage);
    }

    public function getLiveStreamsByStatuses(array $statuses, int $perPage = 20): LengthAwarePaginator
    {
        return $this->streams->listByStatuses($statuses, $perPage);
    }

    public function getLiveStreamsByTitleAndStatus(string $title, string $status, int $perPage = 20): LengthAwarePaginator
    {
        return $this->streams->searchByTitleAndStatus($title, $status, $perPage);
    }

    public function getLiveStreamByStreamKey(string $streamKey): ?LiveStream
    {
        return $this->streams->getByStreamKey($streamKey);
    }

    // 認証API
    public function createLiveStream(string $ownerId, array $data): LiveStream
    {
        return $this->streams->create($ownerId, $data);
    }

    public function updateLiveStream(string $id, array $data, ?string $actorId = null): LiveStream
    {
        $ls = $this->streams->findById($id);
        if (!$ls) abort(404, 'LiveStream not found');
        if ($actorId && $ls->user_id !== $actorId) {
            throw new UnauthorizedException('Not owner');
        }
        return $this->streams->update($ls, $data);
    }

    public function rescheduleLiveStream(string $id, array $data, ?string $actorId = null): LiveStream
    {
        $ls = $this->streams->findById($id);
        if (!$ls) abort(404, 'LiveStream not found');
        if ($actorId && $ls->user_id !== $actorId) {
            throw new UnauthorizedException('Not owner');
        }
        return $this->streams->reschedule($ls, $data);
    }

    public function openLiveStream(string $id, ?string $actorId = null): LiveStream
    {
        $ls = $this->streams->findById($id);
        if (!$ls) abort(404, 'LiveStream not found');
        if ($actorId && $ls->user_id !== $actorId) {
            throw new UnauthorizedException('Not owner');
        }
        return $this->streams->setLive($ls);
    }

    public function closeLiveStream(string $id, ?string $actorId = null): LiveStream
    {
        $ls = $this->streams->findById($id);
        if (!$ls) abort(404, 'LiveStream not found');
        if ($actorId && $ls->user_id !== $actorId) {
            throw new UnauthorizedException('Not owner');
        }
        return $this->streams->setEnded($ls);
    }

    public function cancelLiveStream(string $id, ?string $actorId = null): LiveStream
    {
        $ls = $this->streams->findById($id);
        if (!$ls) abort(404, 'LiveStream not found');
        if ($actorId && $ls->user_id !== $actorId) {
            throw new UnauthorizedException('Not owner');
        }
        return $this->streams->setCancelled($ls);
    }

    public function deleteLiveStream(string $id, ?string $actorId = null): void
    {
        $ls = $this->streams->findById($id);
        if (!$ls) abort(404, 'LiveStream not found');
        if ($actorId && $ls->user_id !== $actorId) {
            throw new UnauthorizedException('Not owner');
        }
        $this->streams->delete($ls);
    }
}
