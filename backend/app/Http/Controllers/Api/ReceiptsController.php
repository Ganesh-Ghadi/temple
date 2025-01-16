<?php

namespace App\Http\Controllers\Api;

use File;
use Response;
use Mpdf\Mpdf;
use App\Models\Receipt;
use Barryvdh\DomPDF\PDF;
use App\Models\CampReceipt;
use App\Models\HallReceipt;
use App\Models\KhatReceipt;
use App\Models\ReceiptType;
use App\Models\NaralReceipt;
use App\Models\SareeReceipt;
use Illuminate\Http\Request;
use App\Models\BhangarReceipt;
use App\Models\LibraryReceipt;
use App\Models\UparaneReceipt;
use Mpdf\Config\FontVariables;
use App\Models\StudyRoomReceipt;
use Mpdf\Config\ConfigVariables;
use App\Models\VasturupeeReceipt;
use Illuminate\Http\JsonResponse;
use App\Helpers\NumberToWordsHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReceiptResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreReceiptRequest;
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
    public function store(StoreReceiptRequest $request): JsonResponse
    {
        $khatReceiptId = 1;
        $naralReceiptId = 2;
        $bhangarReceiptId = 3;
        $sareeReceiptId = 4;
        $uparaneReceiptId = 5;
        $vasturupeeReceiptId = 6;
        $campReceiptId = 7;
        $libraryReceiptId = 8;
        $hallReceiptId = 9;
        $studyRoomReceiptId = 10;


        $receipt = new Receipt();
        $receipt->receipt_type_id = $request->input("receipt_type_id");
        $receipt->created_by = auth()->user()->profile->id;
        $receipt->receipt_no = Receipt::generateReceiptNumber();
        $receipt->receipt_date = $request->input("receipt_date");
        $receipt->receipt_head = $request->input("receipt_head");
        $receipt->name = $request->input("name");
        $receipt->gotra = $request->input("gotra");
        $receipt->amount = $request->input("amount");
        $receipt->email = $request->input("email");
        $receipt->mobile = $request->input("mobile");
        $receipt->address = $request->input("address");
        $receipt->narration = $request->input("narration");
        $receipt->pincode = $request->input("pincode");
        $receipt->payment_mode = $request->input("payment_mode");
        $receipt->special_date = $request->input("special_date");
        $receipt->bank_details = $request->input("bank_details");
        $receipt->cheque_number = $request->input("cheque_number");
        $receipt->cheque_date = $request->input("cheque_date");
        $receipt->remembrance = $request->input("remembrance");
        $receipt->save();

        // खत विक्री पावती
   
        if ($request->has("quantity") && $request->has("rate") && $request->input("receipt_type_id") == $khatReceiptId) {
            $khat_receipt = new KhatReceipt();
            $khat_receipt->receipt_id = $receipt->id;
            $khat_receipt->quantity = $request->input("quantity");
            $khat_receipt->rate = $request->input("rate");
            $khat_receipt->save();
        }

        if ($request->has("quantity") && $request->has("rate") && $request->input("receipt_type_id") == $naralReceiptId) {
            $naral_receipt = new NaralReceipt();
            $naral_receipt->receipt_id = $receipt->id;
            $naral_receipt->quantity = $request->input("quantity");
            $naral_receipt->rate = $request->input("rate");
            $naral_receipt->save();
        }

        if ($request->input("receipt_type_id") == $bhangarReceiptId) {
            $bhangar_receipt = new BhangarReceipt();
            $bhangar_receipt->receipt_id = $receipt->id;
            $bhangar_receipt->description = $request->input("description");
            $bhangar_receipt->save();
        }

        if ($request->input("receipt_type_id") == $sareeReceiptId) {
            $saree_receipt = new SareeReceipt();
            $saree_receipt->receipt_id = $receipt->id;
            $saree_receipt->saree_draping_date = $request->input("saree_draping_date");
            $saree_receipt->return_saree = $request->input("return_saree");
            $saree_receipt->save();
        }

        if ($request->input("receipt_type_id") == $uparaneReceiptId) {
            $uparane_receipt = new UparaneReceipt();
            $uparane_receipt->receipt_id = $receipt->id;
            $uparane_receipt->uparane_draping_date = $request->input("uparane_draping_date");
            $uparane_receipt->return_uparane = $request->input("return_uparane");
            $uparane_receipt->save();
        }

        if ($request->input("receipt_type_id") == $vasturupeeReceiptId) {
            $vasturupee_receipt = new VasturupeeReceipt();
            $vasturupee_receipt->receipt_id = $receipt->id;
            $vasturupee_receipt->description = $request->input("description");
            $vasturupee_receipt->save();
        }

        if ($request->input("receipt_type_id") == $campReceiptId) {
            $camp_receipt = new CampReceipt();
            $camp_receipt->receipt_id = $receipt->id;
            $camp_receipt->member_name = $request->input("member_name");
            $camp_receipt->from_date = $request->input("from_date");
            $camp_receipt->to_date = $request->input("to_date");
            $camp_receipt->Mallakhamb = $request->input("Mallakhamb");
            $camp_receipt->zanj = $request->input("zanj");
            $camp_receipt->dhol = $request->input("dhol");
            $camp_receipt->lezim = $request->input("lezim");
            $camp_receipt->save();
        }

        if ($request->input("receipt_type_id") == $hallReceiptId) {
            $hall_receipt = new HallReceipt();
            $hall_receipt->receipt_id = $receipt->id;
            $hall_receipt->hall = $request->input("hall");            
            $hall_receipt->save();
        }

        if ($request->input("receipt_type_id") == $libraryReceiptId) {
            $library_receipt = new LibraryReceipt();
            $library_receipt->receipt_id = $receipt->id;
            $library_receipt->membership_no = $request->input("membership_no"); 
            $library_receipt->from_date = $request->input("from_date");            
            $library_receipt->to_date = $request->input("to_date");                       
            $library_receipt->save();
        }

        if ($request->input("receipt_type_id") == $studyRoomReceiptId) {
            $study_room_receipt = new StudyRoomReceipt();
            $study_room_receipt->receipt_id = $receipt->id;
            $study_room_receipt->membership_no = $request->input("membership_no"); 
            $study_room_receipt->from_date = $request->input("from_date");            
            $study_room_receipt->to_date = $request->input("to_date");    
            $study_room_receipt->timing = $request->input("timing");                                          
            $study_room_receipt->save();
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

     /**
     * Cancle Receipt.
     */
    public function cancelReceipt(string $id): JsonResponse
    {
        $receipt = Receipt::find($id);
        if(!$receipt){
            return $this->sendError("Receipt not found", ['error'=>'Receipt not found']);
        }
        $val = 1;
        $receipt->cancelled = $val;
        $receipt->cancelled_by = auth()->user()->profile->id;
        $receipt->save();
        return $this->sendResponse([], "Receipt Cancelled successfully");
    }
}