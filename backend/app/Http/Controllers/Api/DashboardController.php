<?php

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;

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
                            ->where('special_date',$today)
                            ->where("cancelled", false)
                            ->get();
    
    
    $hallBookingDetails = $hallReceipts->map(function ($receipt) {
        $hallBooking = $receipt->hallReceipt; // Assuming it's a single model
        return [
            'receipt_id' => $receipt->id,
            'hall_name' => $hallBooking->hall, 
            'from_time' => \Carbon\Carbon::parse($hallBooking->from_time)->format('h:i A'),  // 12-hour format with AM/PM
            'to_time' => \Carbon\Carbon::parse($hallBooking->to_time)->format('h:i A'),      // 12-hour format with AM/PM
            'amount' => $receipt->amount,  
            'name' => $receipt->name,
        ];
    });

    $totalPoojas = $poojaDetails->count();
    $totalHallBookings = $hallBookingDetails->count();

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
                                'HallBookingDetails'=>$hallBookingDetails,
                                'PoojaCount'=>$totalPoojas,
                                'HallBookingCount'=>$totalHallBookings,
                                'SareeDetails'=>$sareeDetails,
                                'UparaneDetails'=>$uparaneDetails,
                                ], "Dashboard data retrieved successfully");
    }

















    
//     public function todaysPooja(): JsonResponse
//     {
//         // Get today's date
//     $date = now()->toDateString();  // Using `now()` instead of Date() for simplicity

//     // Retrieve receipts with their poojas, pooja types, and devta information
//     $receipts = Receipt::with(['poojas.poojaType.devta'])  // Eager load devta info through poojaType
//         ->where("cancelled", false)
//         ->whereHas('poojas', function ($query) use ($date) {
//             // Filter poojas where the date matches today's date
//             $query->where('date', $date);
//         })
//         ->get();
    
//     // Flatten and map over the poojas to get the relevant information
//     $poojaDetails = $receipts->flatMap(function ($receipt) use ($date) {
//         // Filter the poojas to match today's date
//         return $receipt->poojas->filter(function ($pooja) use ($date) {
//             return $pooja->date == $date;  // Match pooja's date with today's date
//         })->map(function ($pooja) use ($receipt) {
//             return [
//                 'name' => $receipt->name,  // User's name from receipt
//                 'email' => $receipt->email, // Email from receipt
//                 'mobile' => $receipt->mobile, // Mobile from receipt
//                 'poojaType' => $pooja->poojaType->pooja_type,  // Name of the pooja type
//                 'devtaName' => $pooja->poojaType->devta->devta_name, // Devta name from the related devta model
//                 'date' => $pooja->date,  // Date of the pooja (today's date)
//             ];
//         });
//     });

//     // Return the list of pooja details as a JSON response
//     return response()->json($poojaDetails);
//     return $this->sendResponse(["PoojaDetails"=>$poojaDetails, 'ReceiptCount'=>$receiptCountToday, 'ReceiptAmount'=>$totalAmountToday,'CancelledReceiptCount'=>$cancelledReceipts], "Todays Pooja Details retrieved successfully");
// }

}