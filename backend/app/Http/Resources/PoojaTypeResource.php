<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PoojaTypeResource extends JsonResource
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
            'devta_id' => $this->devta_id,
            'devta_name' => $this->devta ? $this->devta->devta_name : null, // If there's a relationship with Devta model
            'pooja_type' => $this->pooja_type,
            'multiple' => $this->multiple,
            'contribution' => $this->contribution,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}