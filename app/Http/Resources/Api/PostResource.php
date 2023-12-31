<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'category_id' => $this->category_id,
            'category' => $this->category->name,
            'category_slug' => $this->category->slug,
            'slug' => $this->slug,
            'image' => $this->image,
            'updated_at' => $this->updated_at
        ];
    }
}
