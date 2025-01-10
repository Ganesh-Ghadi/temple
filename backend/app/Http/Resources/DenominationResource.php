<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DenominationResource extends JsonResource
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
            'deposit_date' => $this->deposit_date,
            'n_2000' => $this->n_2000,
            'n_500' => $this->n_500,
            'n_200' => $this->n_200,
            'n_100' => $this->n_100,
            'n_50' => $this->n_50,
            'n_20' => $this->n_20,
            'n_10' => $this->n_10,
            'c_20' => $this->c_20,
            'c_10' => $this->c_10,
            'c_5' => $this->c_5,
            'c_2' => $this->c_2,
            'c_1' => $this->c_1,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}