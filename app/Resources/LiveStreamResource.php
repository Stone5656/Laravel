<?php
// app/Http/Resources/LiveStreamResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LiveStreamResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                 => $this->id,
            'user_id'            => $this->user_id,
            'title'              => $this->title,
            'description'        => $this->description,
            'thumbnail_path'     => $this->thumbnail_path,
            'status'             => $this->status, // "SCHEDULED" | "LIVE" | "ENDED" | "CANCELLED"
            'is_public'          => (bool)$this->is_public,
            'stream_key'         => $this->stream_key,
            'scheduled_start_at' => optional($this->scheduled_start_at)->toISOString(),
            'started_at'         => optional($this->started_at)->toISOString(),
            'ended_at'           => optional($this->ended_at)->toISOString(),
            'created_at'         => optional($this->created_at)->toISOString(),
            'updated_at'         => optional($this->updated_at)->toISOString(),
        ];
    }
}
