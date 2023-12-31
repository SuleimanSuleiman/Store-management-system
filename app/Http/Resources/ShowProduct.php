<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowProduct extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "price" => $this->price,
            "image_path" => $this->image_path,
            "sub_category_id" => $this->sub_category_id,
            "main_category_id" => $this->main_category_id,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}