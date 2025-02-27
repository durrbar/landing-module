<?php

namespace Modules\Landing\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'author' => new AuthorResource($this->whenLoaded('author')),
            'createdAt' => $this->created_at, // Formatting date as needed
        ];
    }
}
