<?php

namespace App\Http\Controllers\Api;

use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReceiptResource;
use App\Http\Controllers\Api\BaseController;

    /**
     * @group Receipt Management
     */
    
class ReceiptsController extends BaseController
{
    /**
     * All Receipt.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Receipt::query();

        if ($request->query('search')) {
            $searchTerm = $request->query('search');
    
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%');
            });
        }
        $receipts = $query->Orderby("id","desc")->paginate(5);

        return $this->sendResponse(["Receipts"=>ReceiptResource::collection($receipts),
        'pagination' => [
            'current_page' => $receipts->currentPage(),
            'last_page' => $receipts->lastPage(),
            'per_page' => $receipts->perPage(),
            'total' => $receipts->total(),
        ]], "Receipts retrieved successfully");
    }

    /**
     * Store Receipt.
     */
    public function store(Request $request): JsonResponse
    {
        $receipt = new Receipt();
        $receipt->receipt_type_id = $request->input("receipt_type_id");
        // $receipt->cancelled_by = $request->input("cancelled_by");
        $receipt->created_by = auth()->user()->profile->id;
        $receipt->receipt_no = Receipt::generateReceiptNumber();
        $receipt->receipt_date = $request->input("receipt_date");
        $receipt->name = $request->input("name");
        $receipt->gotra = $request->input("gotra");
        $receipt->address = $request->input("address");
        $receipt->pincode = $request->input("pincode");
        $receipt->mobile = $request->input("mobile");
        $receipt->email = $request->input("email");
        $receipt->narration = $request->input("narration");
        $receipt->payment_mode = $request->input("payment_mode");
        $receipt->check_no = $request->input("check_no");
        $receipt->check_date = $request->input("check_date");
        $receipt->bank_details = $request->input("bank_details");
        $receipt->special_date = $request->input("special_date");
        $receipt->remembarance = $request->input("remembarance");
        $receipt->amount = $request->input("amount");
        $receipt->amount_in_words = $request->input("amount_in_words");
        // $receipt->print_count = $request->input("print_count");
        // $receipt->cancelled = $request->input("cancelled");
        
        if(!$receipt->save()) {
            return $this->sendError("Error while saving data", ['error'=>['Error while saving data']]);
        }
        return $this->sendResponse(['Receipt'=> new ReceiptResource($receipt)], 'Receipt Created Successfully');
    }

    /**
     * Show Receipt.
     */
    public function show(string $id): JsonResponse
    {
        $receipt = Receipt::find($id);

        if(!$receipt){
            return $this->sendError("Receipt not found", ['error'=>'Receipt not found']);
        }
        return $this->sendResponse(['Receipt'=> new ReceiptResource($receipt)], "Receipt retrieved successfully");
    }

    /**
     * Update Receipt.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $receipt = Receipt::find($id);
        if(!$receipt){
            return $this->sendError("Receipt not found", ['error'=>['Receipt not found']]);
        }
        $receipt->receipt_type_id = $request->input("receipt_type_id");
        $receipt->receipt_date = $request->input("receipt_date");
        $receipt->name = $request->input("name");
        $receipt->gotra = $request->input("gotra");
        $receipt->address = $request->input("address");
        $receipt->pincode = $request->input("pincode");
        $receipt->mobile = $request->input("mobile");
        $receipt->email = $request->input("email");
        $receipt->narration = $request->input("narration");
        $receipt->payment_mode = $request->input("payment_mode");
        $receipt->check_no = $request->input("check_no");
        $receipt->check_date = $request->input("check_date");
        $receipt->bank_details = $request->input("bank_details");
        $receipt->special_date = $request->input("special_date");
        $receipt->remembarance = $request->input("remembarance");
        $receipt->amount = $request->input("amount");
        $receipt->amount_in_words = $request->input("amount_in_words");
        // $receipt->print_count = $request->input("print_count");
        
        if(!$receipt->save()) {
            return $this->sendError("Error while saving data", ['error'=>['Error while saving data']]);
        }
        return $this->sendResponse(['Receipt'=> new ReceiptResource($receipt)], 'Receipt Updated Successfully');

        
    }

    /**
     * Delete Receipt.
     */
    public function destroy(string $id): JsonResponse
    {
        $receipt = Receipt::find($id);
        if(!$receipt){
            return $this->sendError("Receipt not found", ['error'=>'Receipt not found']);
        }
        $receipt->delete();
        return $this->sendResponse([], "Receipt deleted successfully");
    }
}