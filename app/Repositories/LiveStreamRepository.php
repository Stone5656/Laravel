<?php
// app/Repositories/LiveStreamRepository.php

namespace App\Repositories;

use App\Models\LiveStream;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class LiveStreamRepository
{
    public function findById(string $id, bool $withTrashed = false): ?LiveStream
    {
        $q = LiveStream::query();
        if ($withTrashed && in_array('Illuminate\\Database\\Eloquent\\SoftDeletes', class_uses(LiveStream::class) ?: [], true)) {
            $q->withTrashed();
        }
        return $q->find($id);
    }

    public function getPublicOrOwner(string $id, ?string $ownerId = null): ?LiveStream
    {
        return LiveStream::query()
            ->where('id', $id)
            ->where(function ($q) use ($ownerId) {
                $q->where('is_public', true)
                  ->orWhere('user_id', $ownerId);
            })
            ->first();
    }

    public function listByUser(string $userId, int $perPage = 20): LengthAwarePaginator
    {
        return LiveStream::query()
            ->where('user_id', $userId)
            ->where('is_public', true)
            ->orderByDesc('scheduled_start_at')
            ->paginate($perPage);
    }

    public function listByUserAndStatus(string $userId, string $status, int $perPage = 20): LengthAwarePaginator
    {
        return LiveStream::query()
            ->where('user_id', $userId)
            ->where('status', $status)
            ->where('is_public', true)
            ->orderByDesc('scheduled_start_at')
            ->paginate($perPage);
    }

    public function listByStatus(string $status, int $perPage = 20): LengthAwarePaginator
    {
        return LiveStream::query()
            ->where('status', $status)
            ->where('is_public', true)
            ->orderByDesc('scheduled_start_at')
            ->paginate($perPage);
    }

    public function listByStatuses(array $statuses, int $perPage = 20): LengthAwarePaginator
    {
        return LiveStream::query()
            ->whereIn('status', $statuses)
            ->where('is_public', true)
            ->orderByDesc('scheduled_start_at')
            ->paginate($perPage);
    }

    public function searchByTitleAndStatus(string $title, string $status, int $perPage = 20): LengthAwarePaginator
    {
        return LiveStream::query()
            ->where('status', $status)
            ->where('is_public', true)
            ->when($title !== '', fn($q) => $q->where('title', 'like', '%' . $title . '%'))
            ->orderByDesc('scheduled_start_at')
            ->paginate($perPage);
    }

    public function getByStreamKey(string $streamKey): ?LiveStream
    {
        return LiveStream::query()
            ->where('stream_key', $streamKey)
            ->first();
    }

    public function create(string $ownerId, array $data): LiveStream
    {
        $ls = new LiveStream();
        $ls->fill([
            'user_id'            => $ownerId,
            'title'              => $data['title'] ?? '',
            'description'        => $data['description'] ?? null,
            'thumbnail_path'     => $data['thumbnail_path'] ?? null,
            'is_public'          => (bool)($data['is_public'] ?? false),
            'status'             => 'SCHEDULED',
            'scheduled_start_at' => isset($data['scheduled_start_at']) ? Carbon::parse($data['scheduled_start_at']) : null,
        ]);
        // stream_key の生成は Model の creating フックなどに寄せても良い
        $ls->stream_key = $ls->stream_key ?: bin2hex(random_bytes(16));
        $ls->save();
        return $ls->refresh();
    }

    public function update(LiveStream $ls, array $data): LiveStream
    {
        $fillable = ['title','description','thumbnail_path','is_public','status','scheduled_start_at'];
        foreach ($fillable as $f) {
            if (array_key_exists($f, $data)) {
                $ls->{$f} = $f === 'scheduled_start_at' && $data[$f]
                    ? Carbon::parse($data[$f]) : $data[$f];
            }
        }
        $ls->save();
        return $ls->refresh();
    }

    public function reschedule(LiveStream $ls, array $data): LiveStream
    {
        $ls->scheduled_at = Carbon::parse($data['scheduled_at']);
        if (!empty($data['status'])) {
            $ls->status = $data['status'];
        }
        $ls->save();
        return $ls->refresh();
    }

    public function setLive(LiveStream $ls): LiveStream
    {
        $ls->status = 'LIVE';
        $ls->started_at = $ls->started_at ?: now();
        $ls->save();
        return $ls->refresh();
    }

    public function setEnded(LiveStream $ls): LiveStream
    {
        $ls->status = 'ENDED';
        $ls->ended_at = $ls->ended_at ?: now();
        $ls->save();
        return $ls->refresh();
    }

    public function setCancelled(LiveStream $ls): LiveStream
    {
        $ls->status = 'CANCELLED';
        $ls->save();
        return $ls->refresh();
    }

    public function delete(LiveStream $ls): void
    {
        $ls->delete();
    }
}
