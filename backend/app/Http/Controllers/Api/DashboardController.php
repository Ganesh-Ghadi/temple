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
        return $this->sendResponse(["ProfileCount"=>$profileCount, 'ReceiptCount'=>$receiptCountToday, 'ReceiptAmount'=>$totalAmountToday,'CancelledReceiptCount'=>$cancelledReceipts], "Dashboard data retrieved successfully");
    }
}