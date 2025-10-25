<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PublicVideoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'thumbnail_path' => $this->thumbnail_path,
            'views_count'    => (int)$this->views_count,
            'published_at'   => optional($this->published_at)->toISOString(),
            'user_id'        => $this->user_id,
        ];
    }
}
