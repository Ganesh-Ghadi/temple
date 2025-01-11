<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptResource extends JsonResource
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
            'receipt_type_id' => $this->receipt_type_id,
            'receipt_no' => $this->receipt_no,
            'receipt_date' => $this->receipt_date,
            'name' => $this->name,
            'gotra' => $this->gotra,
            'address' => $this->address,
            'pincode' => $this->pincode,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'narration' => $this->narration,
            'payment_mode' => $this->payment_mode,
            'check_no' => $this->check_no,
            'check_date' => $this->check_date,
            'bank_details' => $this->bank_details,
            'special_date' => $this->special_date,
            'remembarance' => $this->remembarance,
            'amount' => $this->amount,
            'amount_in_words' => $this->amount_in_words,
            'print_count' => $this->print_count,
            'cancelled' => $this->cancelled,
            'cancelled_by' => $this->cancelled_by,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}