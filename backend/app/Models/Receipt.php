<?php

namespace App\Models;

use App\Models\Pooja;
use App\Models\Receipt;
use App\Models\PoojaType;
use App\Models\CampReceipt;
use App\Models\HallReceipt;
use App\Models\KhatReceipt;
use App\Models\ReceiptType;
use App\Models\NaralReceipt;
use App\Models\SareeReceipt;
use App\Models\BhangarReceipt;
use App\Models\LibraryReceipt;
use App\Models\UparaneReceipt;
use App\Models\AnteshteeReceipt;
use App\Models\StudyRoomReceipt;
use App\Models\VasturupeeReceipt;
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
        return date('my') . str_pad($lastNumber, 5, '0', STR_PAD_LEFT);
    }

    public function receiptType(){
        return $this->belongsTo(ReceiptType::class, 'receipt_type_id');
    }

    public function khatReceipt(){
        return $this->hasOne(KhatReceipt::class, 'receipt_id');
    }

    public function naralReceipt(){
        return $this->hasOne(NaralReceipt::class, 'receipt_id');
    }

    public function pooja(){
        return $this->hasOne(Pooja::class, 'receipt_id');
    }

    public function poojas(){
        return $this->hasMany(Pooja::class, 'receipt_id');
    }

    public function bhangarReceipt(){
        return $this->hasOne(BhangarReceipt::class, 'receipt_id');
    }

    public function sareeReceipt(){
        return $this->hasOne(SareeReceipt::class, 'receipt_id');
    }

    public function uparaneReceipt(){
        return $this->hasOne(UparaneReceipt::class, 'receipt_id');
    }

    public function vasturupeeReceipt(){
        return $this->hasOne(VasturupeeReceipt::class, 'receipt_id');
    }

    public function campReceipt(){
        return $this->hasOne(CampReceipt::class, 'receipt_id');
    }

    public function hallReceipt(){
        return $this->hasOne(HallReceipt::class, 'receipt_id');
    }

    public function libraryReceipt(){
        return $this->hasOne(LibraryReceipt::class, 'receipt_id');
    }

    public function studyRoomReceipt(){
        return $this->hasOne(StudyRoomReceipt::class, 'receipt_id');
    }

    public function anteshteeReceipt(){
        return $this->hasOne(AnteshteeReceipt::class, 'receipt_id');
    }
}