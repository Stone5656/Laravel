<?php

namespace App\Repositories;

use App\Models\Video;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class VideoRepository
{
    public function findById(string $id): ?Video
    {
        return Video::query()->withTrashed()->find($id);
    }

    public function getByIdPublicOrOwner(string $id, ?string $ownerId = null): ?Video
    {
        return Video::query()
            ->where('id', $id)
            ->where(function ($q) use ($ownerId) {
                $q->where('is_public', true)
                  ->orWhere('user_id', $ownerId);
            })
            ->first();
    }

    public function searchPublic(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $q = Video::query()->where('is_public', true);

        if (!empty($filters['title'])) {
            $q->where('title', 'like', '%' . $filters['title'] . '%');
        }
        if (!empty($filters['sort'])) {
            // views_count|published_at の降順のみ許可
            $sort = $filters['sort'];
            $dir  = 'desc';
            if (in_array($sort, ['views_count', 'published_at'], true)) {
                $q->orderBy($sort, $dir);
            }
        } else {
            $q->orderBy('published_at', 'desc');
        }
        return $q->paginate($perPage);
    }

    public function getPublicByUser(string $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Video::query()
            ->where('user_id', $userId)
            ->where('is_public', true)
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }

    public function getPopular(int $perPage = 20): LengthAwarePaginator
    {
        return Video::query()
            ->where('is_public', true)
            ->orderBy('views_count', 'desc')
            ->paginate($perPage);
    }

    public function getRecent(int $perPage = 20): LengthAwarePaginator
    {
        return Video::query()
            ->where('is_public', true)
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }

    public function incrementViews(string $id): void
    {
        Video::query()->where('id', $id)->increment('views_count');
    }

    public function getMine(string $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Video::query()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(string $ownerId, array $data): Video
    {
        $video = new Video();
        $video->fill([
            'user_id'        => $ownerId,
            'title'          => $data['title'] ?? '',
            'description'    => $data['description'] ?? null,
            'duration_sec'   => $data['duration_sec'] ?? null,
            'file_path'      => $data['file_path'] ?? null,
            'thumbnail_path' => $data['thumbnail_path'] ?? null,
            'is_public'      => (bool)($data['is_public'] ?? false),
            'published_at'   => isset($data['published_at']) ? Carbon::parse($data['published_at']) : null,
        ]);
        $video->views_count = 0;
        $video->save();
        return $video->refresh();
    }

    public function update(Video $video, array $data): Video
    {
        $fillable = ['title','description','duration_sec','file_path','thumbnail_path','is_public','published_at'];
        foreach ($fillable as $f) {
            if (array_key_exists($f, $data)) {
                $video->{$f} = in_array($f, ['published_at'], true) && $data[$f]
                    ? Carbon::parse($data[$f]) : $data[$f];
            }
        }
        $video->save();
        return $video->refresh();
    }

    public function delete(Video $video): void
    {
        $video->delete();
    }

    public function publish(Video $video, ?Carbon $publishedAt = null): Video
    {
        $video->is_public = true;
        $video->published_at = $publishedAt ?: now();
        $video->save();
        return $video->refresh();
    }

    public function unpublish(Video $video): Video
    {
        $video->is_public = false;
        $video->save();
        return $video->refresh();
    }

    public function restore(string $id): ?Video
    {
        $video = Video::withTrashed()->find($id);
        if ($video && $video->trashed()) {
            $video->restore();
        }
        return $video;
    }
}
