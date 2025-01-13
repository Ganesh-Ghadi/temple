<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PoojaDateResource extends JsonResource
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
            'pooja_type_id' => $this->pooja_type_id,
            'pooja_type' => $this->poojaType ? $this->poojaType->pooja_type : null, // If there's a relationship with Devta model
            'pooja_date' => $this->pooja_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}