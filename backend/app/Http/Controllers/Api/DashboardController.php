<?php

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Helpers\AmountToWordsHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReceiptResource;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Collection;

class DashboardController extends BaseController
{
     /**
     * Dashboard
     */
    public function index(Request $request): JsonResponse
    {
        $profileCount = Profile::count();

        $today = now()->toDateString(); 
        $receiptCountToday = Receipt::whereDate('created_at', $today)->count(); 
        $totalAmountToday = Receipt::whereDate('created_at', $today)
        ->where('cancelled',false)
        ->sum('amount');
        $cancelledReceipts = Receipt::whereDate('created_at', $today)
                                    ->where('cancelled', true)
                                    ->count();

         $date = now()->toDateString(); 

    $receipts = Receipt::with(['poojas.poojaType.devta'])  
        ->where("cancelled", false)
        ->whereHas('poojas', function ($query) use ($date) {
            $query->where('date', $date);
        })
        ->get();
    
    $poojaDetails = $receipts->flatMap(function ($receipt) use ($date) {
        return $receipt->poojas->filter(function ($pooja) use ($date) {
            return $pooja->date == $date;  
        })->map(function ($pooja) use ($receipt) {
            return [
                'name' => $receipt->name,  // User's name from receipt
                'gotra' => $receipt->gotra,  // User's name from receipt
                'email' => $receipt->email, // Email from receipt
                'mobile' => $receipt->mobile, // Mobile from receipt
                'pooja_type' => $pooja->poojaType->pooja_type,  // Name of the pooja type
                'devta_name' => $pooja->poojaType->devta->devta_name, // Devta name from the related devta model
                'date' => $pooja->date,  // Date of the pooja (today's date)
            ];
        });
    });

    $hallReceipts = Receipt::with('hallReceipt')
    ->where('receipt_type_id', 9)
    ->where('special_date', $today)
    ->where('cancelled', false)
    ->get();

// Step 1: Map raw data first
$hallBookingData = $hallReceipts->map(function ($receipt) {
    $hall = $receipt->hallReceipt;
    $fromTime = $hall?->from_time ? \Carbon\Carbon::parse($hall->from_time)->format('H:i:s') : null;
    $toTime = $hall?->to_time ? \Carbon\Carbon::parse($hall->to_time)->format('H:i:s') : null;

    return [
        'receipt_id' => $receipt->id,
        'hall_name' => $hall?->hall ?? 'N/A',
        'from_time' => $fromTime ? \Carbon\Carbon::parse($fromTime)->format('h:i A') : 'N/A',
        'to_time' => $toTime ? \Carbon\Carbon::parse($toTime)->format('h:i A') : 'N/A',
        'raw_from' => $fromTime, // for comparison
        'raw_to' => $toTime,
        'amount' => $receipt->amount,
        'name' => $receipt->name,
        'isAC' => $hall?->ac_charges ?? false,
    ];
});

// Step 2: Group by name + from_time + to_time
$grouped = $hallBookingData->groupBy(function ($item) {
    return $item['name'] . '|' . $item['raw_from'] . '|' . $item['raw_to'];
});

// Step 3: Filter only one per group, and mark duplicates
$finalData = $grouped->map(function ($group) {
    $item = $group->first();

    $item['isduplicate'] = $group->count() > 1;

    return $item;
})->values();
$finalCount = $finalData->count();

    
    
    $prasadReceipts = Receipt::where('receipt_type_id', 15)
                    ->where('special_date',$today)
                    ->where("cancelled", false)
                    ->get();
    
    $prasadReceiptDetails = $prasadReceipts->map(function ($receipt) {
            return [
                'receipt_id' => $receipt->id,
                'person_name' => $receipt->name ? $receipt->name : "N/A", // Fallback if hallReceipt is null
                'amount' => $receipt->amount,
            ];
    });

    $totalPoojas = $poojaDetails->count();
    $totalHallBookings = $hallReceipts->count();
    $totalPrasadCount = $prasadReceiptDetails->count();

    $sareeReceipt = Receipt::with('sareeReceipt')
                            ->where("cancelled", false)
                            ->whereHas('sareeReceipt', function($query) use ($date) {
                                $query->where('saree_draping_date_morning',$date);
                            })
                            ->first();

                            if ($sareeReceipt) {
                                $sareeDetails = [
                                    'saree_draping_date_morning' => $sareeReceipt->sareeReceipt->saree_draping_date_morning,
                                    'return_saree' => $sareeReceipt->sareeReceipt->return_saree,
                                    'name' => $sareeReceipt->name,
                                    'gotra' => $sareeReceipt->gotra,
                                ];
                            } else {
                                // Handle the case where no matching receipt was found
                                $sareeDetails = null;
                            }

    $uparaneReceipt = Receipt::with('uparaneReceipt')
                    ->where("cancelled", false)
                    ->whereHas('uparaneReceipt', function($query) use ($date) {
                        $query->where('uparane_draping_date_morning',$date);
                    })
                    ->first();
                    if ($uparaneReceipt) {
                       $uparaneDetails = [
                        'name' => $uparaneReceipt->name,
                        'gotra' => $uparaneReceipt->gotra,
                    ];
                } else {
                    // Handle the case where no matching receipt was found
                    $uparaneDetails = null;
            }
                            
        return $this->sendResponse(["ProfileCount"=>$profileCount,
                                'ReceiptCount'=>$receiptCountToday,
                                'ReceiptAmount'=>$totalAmountToday,
                                'CancelledReceiptCount'=>$cancelledReceipts,
                                'PoojaDetails'=>$poojaDetails,
                                'HallBookingDetails'=>$finalData,
                                'PrasadReceiptDetails'=>$prasadReceiptDetails,
                                'PoojaCount'=>$totalPoojas,
                                'HallBookingCount'=>$finalCount,
                                'PrasadCount'=>$totalPrasadCount,
                                'SareeDetails'=>$sareeDetails,
                                'UparaneDetails'=>$uparaneDetails,
                                ], "Dashboard data retrieved successfully");
    }


    
    public function addACCharges(Request $request, string $receiptId): JsonResponse
{
    $ac_amount = $request->input("ac_amount");

    $originalReceipt = Receipt::with("hallReceipt")->find($receiptId);
    if (!$originalReceipt) {
        return $this->sendError("Receipt not found", ['error' => 'Receipt not found']);
    }

    if ($originalReceipt->receipt_type_id != 9) {
        return $this->sendError("Receipt is not a hall receipt", ['error' => 'Receipt is not a hall receipt']);
    }

    if (!$originalReceipt->hallReceipt) {
        return $this->sendError("Hall receipt not found", ['error' => 'Hall receipt not found']);
    }

    // ✅ Declare the variable here so it exists in outer scope
    $newReceipt = null;

    DB::transaction(function () use ($originalReceipt, $ac_amount, &$newReceipt, &$request) {
        // Clone and create new receipt
        $newReceipt = $originalReceipt->replicate();
        $newReceipt->receipt_no = Receipt::generateReceiptNumber();
        $newReceipt->receipt_date = now();
        $newReceipt->payment_mode =  $request->input("payment_mode");
        $newReceipt->upi_number =  $request->input("upi_number");
        $newReceipt->bank_details =  $request->input("bank_details");
        $newReceipt->cheque_date =  $request->input("cheque_date");
        $newReceipt->cheque_number =  $request->input("cheque_number");
        
        $newReceipt->amount = $ac_amount;
        $newReceipt->amount_in_words = AmountToWordsHelper::amountToWords($ac_amount);
        $newReceipt->print_count = 0;
        
        $newReceipt->created_by = auth()->user()->profile->id;
        $newReceipt->updated_by = auth()->user()->profile->id;
        $newReceipt->created_at = now();
        $newReceipt->updated_at = now();
        $newReceipt->save();

        // Clone and create new hall receipt
        $newHallReceipt = $originalReceipt->hallReceipt->replicate();
        $newHallReceipt->receipt_id = $newReceipt->id;
        $newHallReceipt->ac_charges = true;
        $newHallReceipt->ac_amount = $ac_amount;
        $newHallReceipt->created_at = now();
        $newHallReceipt->updated_at = now();
        $newHallReceipt->save();
    });

    return $this->sendResponse(['Receipt' => new ReceiptResource($newReceipt)], "New receipt with AC charges created successfully");
}


}