<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'description'    => $this->description,
            'group_id'       => $this->group_id,
            'post_count'     => $this->post_count,
            'original_poster'=> $this->author->name ?? 'Unknown System User',
            'created_at'     => $this->created_at->toDateTimeString(),
        ];
    }
}