<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnteshteeAmountResource extends JsonResource
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
            'day_9_amount' => $this->day_9_amount,
            'day_10_amount' => $this->day_10_amount,
            'day_11_amount' => $this->day_11_amount,
            'day_12_amount' => $this->day_12_amount,
            'day_13_amount' => $this->day_13_amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}