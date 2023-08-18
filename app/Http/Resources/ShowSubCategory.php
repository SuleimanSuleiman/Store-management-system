<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowSubCategory extends JsonResource
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
            "type" => $this->type,
            "Desc" => $this->Desc,
            "model" => $this->model,
            "size" => $this->size,
            "market" => $this->market,
            "main_category_id" => $this->main_category_id,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}