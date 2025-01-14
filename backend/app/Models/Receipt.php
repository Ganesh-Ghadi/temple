<?php

namespace App\Models;

use App\Models\Receipt;
use App\Models\ReceiptType;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    public static function generateReceiptNumber(): string
    {
        // Find the latest profile number for the current month and year
        $latestNumber = Receipt::where('receipt_no', 'like', date('my') . '%')
                        ->orderBy('receipt_no', 'DESC')
                        ->first();

        // Increment the numeric part of the profile number
        $lastNumber = 1;

        if ($latestNumber) {
            $lastNumber = intval(substr($latestNumber->receipt_no, 4)) + 1;
        }
        return date('my') . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
    }

    public function receiptType(){
        return $this->belongsTo(ReceiptType::class, 'receipt_type_id');
    }
}