<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptTypeResource extends JsonResource
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
            'receipt_head' => $this->receipt_head,
            'receipt_type' => $this->receipt_type,
            'special_date' => $this->special_date,
            'minimum_amount' => $this->minimum_amount,
            'is_pooja' => $this->isPooja,
            'show_special_date' => $this->show_special_date,
            'show_remembrance' => $this->show_remembarance,
            'list_order' => $this->list_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}