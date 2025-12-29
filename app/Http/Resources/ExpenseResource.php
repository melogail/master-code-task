<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
            "category_id" => $this->whenLoaded('category', function () {
                return CategoryResource::make($this->category);
            }),
            "vendor_id" => $this->whenLoaded('vendor', function () {
                return VendorResource::make($this?->vendor);
            }),
            "amount" => $this->amount,
            "date" => $this->date?->format('Y-m-d') ?? null,
            "description" => $this->description ?? null,
            "created_at" => $this->created_at?->format('Y-m-d H:i:s') ?? null,
            "updated_at" => $this->updated_at?->format('Y-m-d H:i:s') ?? null,
            "deleted_at" => $this->deleted_at?->format('Y-m-d H:i:s') ?? null,
        ];
    }
}
