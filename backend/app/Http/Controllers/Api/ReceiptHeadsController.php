<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;

class ReceiptHeadsController extends BaseController
{
     /**
     * Fetch All receipt Heads.
     */
    
     public function allReceiptHeads(Request $request): JsonResponse
     {
        $receipt_heads = config("data.receipt_heads");
        if(!$receipt_heads){
            return $this->sendError("Receipt heads not found", ['error'=>'Receipt heads not found']);
        }
        return $this->sendResponse(["ReceiptHeads"=>$receipt_heads], "Receipt Heads retrieved successfully");

     }
}