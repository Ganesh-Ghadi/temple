<?php

namespace App\Models;

use App\Models\Pooja;
use App\Models\Profile;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    // public static function generateReceiptNumber(): string
    // {
    //     // Find the latest profile number for the current month and year
    //     $latestNumber = Receipt::where('receipt_no', 'like', date('my') . '%')
    //                     ->orderBy('receipt_no', 'DESC')
    //                     ->first();

    //     // Increment the numeric part of the profile number
    //     $lastNumber = 1;

    //     if ($latestNumber) {
    //         $lastNumber = intval(substr($latestNumber->receipt_no, 4)) + 1;
    //     }
    //     return date('my') . str_pad($lastNumber, 5, '0', STR_PAD_LEFT);
    // }

    public static function generateReceiptNumber(): string
    {
        return DB::transaction(function () {

        // Get the current date
        $currentDate = now(); // 'now()' returns the current date and time.
    
        // Determine the financial year
        // Assuming financial year starts from April 1st
        $financialYearStart = now()->month >= 4 ? now()->year : now()->year - 1;
        $financialYearEnd = $financialYearStart + 1;
    
        // Format financial year as YY (last 2 digits of the year)
        // $financialYear = substr($financialYearStart, 2, 2) . substr($financialYearEnd, 2, 2);
        $financialYear = substr((string)$financialYearStart, 2, 2) . substr((string)$financialYearEnd, 2, 2);

        // Get the latest receipt for the current financial year
        $lastReceipt = Receipt::where('receipt_no', 'like', $financialYear . '%')
                            ->orderBy('created_at', 'DESC') // Order by creation date descending
                            ->first();
    
        // If no receipt exists, start with 1
        $lastNumber = 22575;
    
        if ($lastReceipt) {
            // Extract the numeric part from the receipt_no (after the hyphen)
            $lastNumber = intval(substr($lastReceipt->receipt_no, 5)) + 1;
        }
    
        // Increment receipt number and format it with leading zeros
        $newReceiptNumber = str_pad($lastNumber, 5, '0', STR_PAD_LEFT);
    
        // Generate the receipt number in the format 'YY-XXXX'
        $receiptNumber = $financialYear . '-' . $newReceiptNumber;
    
        // Return the generated receipt number
        return $receiptNumber;
    });

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

    public function profile(){
        return $this->belongsTo(Profile::class, 'created_by');
    }
}