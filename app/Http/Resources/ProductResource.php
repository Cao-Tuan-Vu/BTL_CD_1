<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class ProductResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'image_path' => $this->image_path,
            'image_url' => $this->image_url,
            'gallery_images' => $this->gallery_images,
            'gallery_image_urls' => $this->gallery_image_urls,
            'price' => $this->price,
            'status' => $this->status,
            'stock' => $this->stock,
            'reviews_count' => $this->whenCounted('reviews'),
            'reviews_avg_rating' => $this->when(isset($this->reviews_avg_rating), fn () => round((float) $this->reviews_avg_rating, 1)),
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
