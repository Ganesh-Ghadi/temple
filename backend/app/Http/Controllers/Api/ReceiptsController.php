<?php

namespace App\Http\Controllers\Api;

use File;
use Response;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Barryvdh\DomPDF\PDF;
use App\Models\Receipt;
use App\Models\ReceiptType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReceiptResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ReceiptTypeResource;
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
        $query = Receipt::with("receiptType");

        if ($request->query('search')) {
            $searchTerm = $request->query('search');
    
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                ->orWhere('receipt_no', 'like', '%' . $searchTerm . '%')
                ->orWhereHas('receiptType', function ($query) use ($searchTerm) {
                    $query->where('receipt_type', 'like', '%' . $searchTerm . '%');
                });
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
     * @bodyParam name string the name of the person for receipt.
     * @bodyParam gotra string the gotra of the person for receipt.
     * @bodyParam amount decimal the amount of the person for receipt.
     */
    public function store(Request $request): JsonResponse
    {
        $receipt = new Receipt();
        $receipt->receipt_type_id = $request->input("receipt_type_id");
        $receipt->created_by = auth()->user()->profile->id;
        $receipt->receipt_no = Receipt::generateReceiptNumber();
        $receipt->receipt_date = $request->input("receipt_date");
        $receipt->receipt_head = $request->input("receipt_head");
        $receipt->name = $request->input("name");
        $receipt->gotra = $request->input("gotra");
        $receipt->amount = $request->input("amount");
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
     * @bodyParam name string the name of the person for receipt.
     * @bodyParam gotra string the gotra of the person for receipt.
     * @bodyParam amount decimal the amount of the person for receipt.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $receipt = Receipt::find($id);
        if(!$receipt){
            return $this->sendError("Receipt not found", ['error'=>['Receipt not found']]);
        }
        $receipt->receipt_type_id = $request->input("receipt_type_id");
        $receipt->name = $request->input("name");
        $receipt->gotra = $request->input("gotra");      
        $receipt->amount = $request->input("amount");
        $receipt->receipt_head = $request->input("receipt_head");
        
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

     /**
     * Generate Receipt
     */

     public function generateReceipt(string $id)
     {
         $receipt = Receipt::find($id);
         if(!$receipt){
             return $this->sendError("receipt not found", ['error'=>['receipt not found']]);
         }
         
         if(!empty($receipt->receipt_file) && Storage::exists('public/Receipt/'.$receipt->receipt_file)) {
             Storage::delete('public/Receipt/'.$receipt->receipt_file);
         }
 
         $data = [
             'receipt' => $receipt,
         ];
 
         // Render the Blade view to HTML
         $html = view('Receipt.receipt', $data)->render();
 
         // Create a new mPDF instance
         // $mpdf = new Mpdf();
             // $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'orientation' => 'L']);  // 'P' is for portrait (default)
             $defaultConfig = (new ConfigVariables())->getDefaults();
             $fontDirs = $defaultConfig['fontDir'];
         
             $defaultFontConfig = (new FontVariables())->getDefaults();
             $fontData = $defaultFontConfig['fontdata'];
         
             $mpdf = new Mpdf([
                 'mode' => 'utf-8',
                 'format' => 'A4',
                 'orientation' => 'P',
                 'fontDir' => array_merge($fontDirs, [
                     storage_path('fonts/'), // Update to point to the storage/fonts directory
                 ]),
                 'fontdata' => $fontData + [
                     'notosansdevanagari' => [
                         'R' => 'NotoSansDevanagari-Regular.ttf',
                         'B' => 'NotoSansDevanagari-Bold.ttf',
                     ],
                 ],
                 'default_font' => 'notosansdevanagari',
             ]);
 
         // Write HTML to the PDF
         $mpdf->WriteHTML($html);
         $randomNumber = rand(1000, 9999);
         // Define the file path for saving the PDF
         $filePath = 'public/Receipt/receipt' . time(). $randomNumber . '.pdf'; // Store in 'storage/app/invoices'
         $fileName = basename($filePath); // Extracts 'invoice_{timestamp}{user_id}.pdf'
         $receipt->receipt_file = $fileName;
         $receipt->save();
       
         // Save PDF to storage
         Storage::put($filePath, $mpdf->Output('', 'S')); // Output as string and save to storage
 
         // Output the PDF for download
         return $mpdf->Output('receipt.pdf', 'D'); // Download the PDF
         // return $this->sendResponse([], "Invoice generated successfully");
     }

   
}