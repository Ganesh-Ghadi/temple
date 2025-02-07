<?php

namespace App\Http\Controllers\Api;

use App\Models\ReceiptType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PoojaTypeResource;
use App\Http\Resources\ReceiptTypeResource;
use App\Http\Controllers\Api\BaseController;

    /**
     * @group Receipt Type Management
     */
class ReceiptTypesController extends BaseController
{
    /**
     * All Receipt Types.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ReceiptType::query();

        if ($request->query('search')) {
            $searchTerm = $request->query('search');
    
            $query->where(function ($query) use ($searchTerm) {
                $query->where('receipt_head', 'like', '%' . $searchTerm . '%')
                ->orWhere('receipt_type', 'like', '%' . $searchTerm . '%');
                
            });
        }
        $receiptTypes = $query->Orderby("id","desc")->paginate(20);

        return $this->sendResponse(["ReceiptTypes"=>ReceiptTypeResource::collection($receiptTypes),
        'pagination' => [
            'current_page' => $receiptTypes->currentPage(),
            'last_page' => $receiptTypes->lastPage(),
            'per_page' => $receiptTypes->perPage(),
            'total' => $receiptTypes->total(),
        ]], "Receipt Types retrieved successfully");
    }

    /**
     * Store Receipt Type.
     * @bodyParam receipt_head string The name of the Receipt head.
     * @bodyParam receipt_type string The name of the Receipt type.
     * @bodyParam special_date string The name of the special date.
     * @bodyParam minimum_amount string The name of the Minimum amount field.
     * @bodyParam is_pooja boolean The name of the pooja.
     * @bodyParam show_special_date boolean The name of the show_special.
     * @bodyParam show_remembarance boolean The name of the show_remembarance.\
     * @bodyParam list_order boolean The name of the List Order.
     */
    public function store(Request $request): JsonResponse
    {
        $receiptType = new ReceiptType();
        $receiptType->receipt_head = $request->input("receipt_head");
        $receiptType->receipt_type = $request->input("receipt_type");
        $receiptType->special_date = $request->input("special_date");
        $receiptType->minimum_amount = $request->input("minimum_amount");
        $receiptType->is_pooja = $request->input("is_pooja");
        $receiptType->show_special_date = $request->input("show_special_date");
        $receiptType->show_remembarance = $request->input("show_remembarance");
        $receiptType->list_order = $request->input("list_order");

        if(!$receiptType->save()) {
            return $this->sendError("Error while saving data", ['error'=>['Error while saving data']]);
        }
        return $this->sendResponse(['ReceiptType'=> new ReceiptTypeResource($receiptType)], 'Receipt Type Created Successfully');
    }

    /**
     * Show Receipt Type.
     */
    public function show(string $id): JsonResponse
    {
        $receiptType = ReceiptType::find($id);

        if(!$receiptType){
            return $this->sendError("Receipt Type not found", ['error'=>'Receipt Type not found']);
        }
        return $this->sendResponse(['ReceiptType'=> new ReceiptTypeResource($receiptType)], "Receipt Type retrieved successfully");
    }

    /**
     * Update Receipt Type.
     * @bodyParam receipt_head string The name of the Receipt head.
     * @bodyParam receipt_type string The name of the Receipt type.
     * @bodyParam special_date string The name of the special date.
     * @bodyParam minimum_amount string The name of the Minimum amount field.
     * @bodyParam is_pooja boolean The name of the pooja.
     * @bodyParam show_special_date boolean The name of the show_special.
     * @bodyParam show_remembarance boolean The name of the show_remembarance.
     * @bodyParam list_order boolean The name of the List Order.

     */
    public function update(Request $request, string $id): JsonResponse
    {
        $receiptType = ReceiptType::find($id);
        if(!$receiptType){
            return $this->sendError("Receipt Type not found", ['error'=>['Receipt Type not found']]);
        }
        $receiptType->receipt_head = $request->input("receipt_head");
        $receiptType->receipt_type = $request->input("receipt_type");
        $receiptType->special_date = $request->input("special_date");
        $receiptType->minimum_amount = $request->input("minimum_amount");
        $receiptType->is_pooja = $request->input("is_pooja");
        $receiptType->show_special_date = $request->input("show_special_date");
        $receiptType->show_remembarance = $request->input("show_remembarance");
        $receiptType->list_order = $request->input("list_order");

        if(!$receiptType->save()) {
            return $this->sendError("Error while saving data", ['error'=>['Error while saving data']]);
        }
        return $this->sendResponse(["ReceiptType"=> new ReceiptTypeResource($receiptType)], "Receipt Type Updated successfully");
    }

    /**
     * Delete Receipt Type.
     */
    public function destroy(string $id): JsonResponse
    {
        $receiptType = ReceiptType::find($id);
        if(!$receiptType){
            return $this->sendError("Receipt Type not found", ['error'=>'Receipt Type not found']);
        }
        $receiptType->delete();
        return $this->sendResponse([], "Receipt Type deleted successfully");
    }

     /**
     * Fetch All Receipt Types.
     */
    public function allReceiptTypes(Request $request): JsonResponse
    {
        $receipt_head = $request->query("receipt_head");
        // if($receipt_head){
            $receiptTypes = ReceiptType::where('receipt_head', $receipt_head)
            ->orderBy('list_order')
            ->get();
        // }else{
        //    $receiptTypes = ReceiptType::all();
        // }

        return $this->sendResponse(["ReceiptTypes"=>ReceiptTypeResource::collection($receiptTypes),
        ], "All Receipt Types retrieved successfully");

    }
}