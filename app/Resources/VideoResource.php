<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'user_id'        => $this->user_id,
            'title'          => $this->title,
            'description'    => $this->description,
            'duration_sec'   => $this->duration_sec,
            'file_path'      => $this->file_path,
            'thumbnail_path' => $this->thumbnail_path,
            'is_public'      => (bool)$this->is_public,
            'views_count'    => (int)$this->views_count,
            'published_at'   => optional($this->published_at)->toISOString(),
            'created_at'     => optional($this->created_at)->toISOString(),
            'updated_at'     => optional($this->updated_at)->toISOString(),
        ];
    }
}
