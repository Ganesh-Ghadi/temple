<?php

namespace App\Http\Controllers\Api;

use File;
use Response;
use Mpdf\Mpdf;
use Carbon\Carbon;
use App\Models\Pooja;
use App\Models\Receipt;
use Mpdf\WatermarkText;
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
use App\Models\AnteshteeReceipt;
use App\Models\StudyRoomReceipt;
use Mpdf\Config\ConfigVariables;
use App\Models\VasturupeeReceipt;
use Illuminate\Http\JsonResponse;
use App\Helpers\AmountToWordsHelper;
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
        $receipts = $query->Orderby("id","desc")->paginate(20);

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
        $anteshteeReceiptId = 11;
        $poojaReceiptId = 12;
        $poojaPavtiAnekReceiptId = 13;
        $bharaniShradhhId = 14;

        //validation start
        //saree validation
        if ($request->input("receipt_type_id") == $sareeReceiptId) {
            // Get the date from the request
            $sareeDrapingDateMorningInput = $request->input("saree_draping_date_morning");
            $sareeDrapingDateEveningInput = $request->input("saree_draping_date_evening");

            if (!$sareeDrapingDateMorningInput && !$sareeDrapingDateEveningInput) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'saree_days' => ['date field is required.']
                    ],
                ], 422);
            }

            // Only query if the date is provided
            if ($sareeDrapingDateMorningInput) {
                $sareeDrapingDateMorning = SareeReceipt::where('saree_draping_date_morning', $sareeDrapingDateMorningInput)->first();
                
                // Check if the date exists in the database
                if ($sareeDrapingDateMorning) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validation failed',
                        'errors' => [
                            'saree_draping_date_morning' => ['The selected morning saree draping date is already taken.']
                        ],
                    ], 422);
                }
            }

            if ($sareeDrapingDateEveningInput) {
                $sareeDrapingDateEvening = SareeReceipt::where('saree_draping_date_evening', $sareeDrapingDateEveningInput)->first();
                
                // Check if the date exists in the database
                if ($sareeDrapingDateEvening) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validation failed',
                        'errors' => [
                            'saree_draping_date_evening' => ['The selected evening saree draping date is already taken.']
                        ],
                    ], 422);
                }
            }

        }

         //uparane validation
         if ($request->input("receipt_type_id") == $uparaneReceiptId) {
            // Get the date from the request
            $uparaneDrapingDateMorningInput = $request->input("uparane_draping_date_morning");
            $uparaneDrapingDateEveningInput = $request->input("uparane_draping_date_evening");

            if (!$uparaneDrapingDateMorningInput && !$uparaneDrapingDateEveningInput) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'uparane_days' => ['date field is required.']
                    ],
                ], 422);
            }
            
            // Only query if the date is provided
            if ($uparaneDrapingDateMorningInput) {
                $uparaneDrapingDateMorning = UparaneReceipt::where('uparane_draping_date_morning', $uparaneDrapingDateMorningInput)->first();
                
                // Check if the date exists in the database
                if ($uparaneDrapingDateMorning) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validation failed',
                        'errors' => [
                            'uparane_draping_date_morning' => ['The selected morning uparane draping date is already taken.']
                        ],
                    ], 422);
                }
            }

            if ($uparaneDrapingDateEveningInput) {
                $uparaneDrapingDateEvening = UparaneReceipt::where('uparane_draping_date_evening', $uparaneDrapingDateEveningInput)->first();
                
                if ($uparaneDrapingDateEvening) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validation failed',
                        'errors' => [
                            'uparane_draping_date_evening' => ['The selected evening uparane draping date is already taken.']
                        ],
                    ], 422);
                }
            }

        }

        // pooja validation
        if (($request->input("receipt_type_id") == $poojaReceiptId) || ($request->input('pooja_type_id') && $request->input('date'))) {
            $poojaTypeId = $request->input("pooja_type_id");
            $date = $request->input("date");

            if (!$poojaTypeId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'pooja_type_id' => ['Pooja type field is required.']
                    ],
                ], 422);   
            }

            if (!$date) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'date' => ['Date field is required.']
                    ],
                ], 422);   
            }
        }

          $isPooja = ReceiptType::where('id',$request->input('receipt_type_id'))->first()->is_pooja;
          if($isPooja){
            $poojaTypeId = $request->input("pooja_type_id");
            $date = $request->input("date");

            if (!$poojaTypeId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'pooja_type_id' => ['Pooja type field is required.']
                    ],
                ], 422);   
            }

            if (!$date) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'date' => ['Date field is required.']
                    ],
                ], 422);   
            }
          }
         // pooja anek validation
         if (($request->input("receipt_type_id") == $poojaPavtiAnekReceiptId)) {
            $poojaTypeId = $request->input("pooja_type_id");
            $multiple_dates = $request->input("multiple_dates");

            if (!$poojaTypeId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'pooja_type_id' => ['Pooja type field is required.']
                    ],
                ], 422);   
            }

            if (!$multiple_dates) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'multiple_dates' => ['date field is required.']
                    ],
                ], 422);   
            }
        }

           // bhangar validation
           if (($request->input("receipt_type_id") == $bhangarReceiptId)) {
            $description = $request->input("description");

            if (!$description) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'description' => ['Description field is required.']
                    ],
                ], 422);   
            }

        }

        // vasturupi dengi validation
        if (($request->input("receipt_type_id") == $vasturupeeReceiptId)) {
            $description = $request->input("description");

            if (!$description) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'description' => ['Description field is required.']
                    ],
                ], 422);   
            }

        }

        // anteshti validation
        if (($request->input("receipt_type_id") == $anteshteeReceiptId)) {
            $day_9_date = $request->input("day_9_date");
            $day_10_date = $request->input("day_10_date");
            $day_11_date = $request->input("day_11_date");
            $day_12_date = $request->input("day_12_date");
            $day_13_date = $request->input("day_13_date");


            if (!$day_9_date && !$day_10_date && !$day_11_date && !$day_12_date && !$day_13_date) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'anteshti_dates' => ['anteshti date field is required']
                    ],
                ], 422);
            }

        }

           // hall receipt validation
           if (($request->input("receipt_type_id") == $hallReceiptId)) {
            $hall = $request->input("hall");
            $from_time = $request->input("from_time");
            $to_time = $request->input("to_time");
            $special_date = $request->input("special_date");
            
            if ($from_time && isset($from_time['hour']) && isset($from_time['minute'])) {
                $fromTime = sprintf('%02d:%02d:00', $from_time['hour'], $from_time['minute']);                
            }       
            if ($to_time && isset($to_time['hour']) && isset($to_time['minute'])) {
                $toTime = sprintf('%02d:%02d:00', $to_time['hour'], $to_time['minute']);
            }       
            
            $bookingExists = HallReceipt::whereHas('receipt',function ($query) use ($special_date){
                 $query->where('special_date', $special_date);
            })
            ->where('hall', $hall)
            ->where(function ($query) use ($fromTime, $toTime) {
                $query->whereBetween('from_time', [$fromTime, $toTime])
                      ->orWhereBetween('to_time', [$fromTime, $toTime])
                      ->orWhere(function ($query) use ($fromTime, $toTime) {
                          $query->where('from_time', '<', $toTime)
                                ->where('to_time', '>', $fromTime);
                      });
            })
            ->exists();
            
            if ($bookingExists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'hall_booked' => ['This hall is already booked for the selected time range.']
                    ],
                ], 422);
            }

        }
        
        // validation end


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
        $receipt->upi_number = $request->input("upi_number");
        $receipt->cheque_number = $request->input("cheque_number");
        $receipt->cheque_date = $request->input("cheque_date");
        $receipt->remembrance = $request->input("remembrance");
        $amountInWords = AmountToWordsHelper::amountToWords($request->input("amount"));
        $receipt->amount_in_words = $amountInWords;
        $receipt->save();

        $multiple_dates = $request->input("multiple_dates");

   
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
            $saree_receipt->saree_draping_date_morning = $request->input("saree_draping_date_morning");
            $saree_receipt->saree_draping_date_evening = $request->input("saree_draping_date_evening");
            $saree_receipt->return_saree = $request->input("return_saree");
            $saree_receipt->save();
        }

        if ($request->input("receipt_type_id") == $uparaneReceiptId) {
            $uparane_receipt = new UparaneReceipt();
            $uparane_receipt->receipt_id = $receipt->id;
            $uparane_receipt->uparane_draping_date_morning = $request->input("uparane_draping_date_morning");
            $uparane_receipt->uparane_draping_date_evening = $request->input("uparane_draping_date_evening");
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
            $hall_receipt->ac_charges = $request->input("ac_charges"); 
            $hall_receipt->ac_amount = $request->input("ac_amount");   
            // $hall_receipt->from_time = $request->input("from_time"); 
            // $hall_receipt->to_time = $request->input("to_time");                      
            $from_time = $request->input('from_time'); // Get the from_time object

            if ($from_time && isset($from_time['hour']) && isset($from_time['minute'])) {
                $formatted_from_time = sprintf('%02d:%02d:00', $from_time['hour'], $from_time['minute']);
                
                $hall_receipt->from_time = $formatted_from_time;
            }       

            $to_time = $request->input('to_time'); // Get the from_time object

            if ($to_time && isset($to_time['hour']) && isset($to_time['minute'])) {
                $formatted_to_time = sprintf('%02d:%02d:00', $to_time['hour'], $to_time['minute']);
                
                $hall_receipt->to_time = $formatted_to_time;
            }       
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

        if ($request->input("receipt_type_id") == $anteshteeReceiptId) {
            $anteshtee_receipt = new AnteshteeReceipt();
            $anteshtee_receipt->receipt_id = $receipt->id;
            $anteshtee_receipt->guruji = $request->input("guruji"); 
            $anteshtee_receipt->yajman = $request->input("yajman");            
            $anteshtee_receipt->karma_number = $request->input("karma_number"); 
            $anteshtee_receipt->day_9 = $request->input("day_9");                                                                                   
            $anteshtee_receipt->day_10 = $request->input("day_10");                                          
            $anteshtee_receipt->day_11 = $request->input("day_11");                                          
            $anteshtee_receipt->day_12 = $request->input("day_12");                                          
            $anteshtee_receipt->day_13 = $request->input("day_13");  
            $anteshtee_receipt->day_9_date = $request->input("day_9_date");    
            $anteshtee_receipt->day_10_date = $request->input("day_10_date");    
            $anteshtee_receipt->day_11_date = $request->input("day_11_date");   
            $anteshtee_receipt->day_12_date = $request->input("day_12_date");    
            $anteshtee_receipt->day_13_date = $request->input("day_13_date");                                                                                            
            $anteshtee_receipt->save();
        }

        if (($request->input("receipt_type_id") == $poojaReceiptId) || ($request->input('pooja_type_id') && $request->input('date'))) {
            
            $pooja_receipt = new Pooja();
            $pooja_receipt->receipt_id = $receipt->id;
            $pooja_receipt->pooja_type_id = $request->input("pooja_type_id"); 
            $pooja_receipt->date = $request->input("date");                                                                                   
            $pooja_receipt->save();
        }

        if ($request->input("receipt_type_id") == $poojaPavtiAnekReceiptId) {
            foreach ($multiple_dates as $date) {
                $multiplePooja = new Pooja();
                $multiplePooja->receipt_id = $receipt->id;
                $multiplePooja->pooja_type_id = $request->input("pooja_type_id"); 
                $multiplePooja->date = $date;                                                                                   
                $multiplePooja->save();
            }
        }
        
        if ($request->input("receipt_type_id") == $bharaniShradhhId) {
            $bharani_shradhh_receipt = new AnteshteeReceipt();
            $bharani_shradhh_receipt->receipt_id = $receipt->id;
            $bharani_shradhh_receipt->guruji = $request->input("guruji");                                                                                       
            $bharani_shradhh_receipt->save();
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

    //  public function generateReceipt(string $id)
    //  {
    //      $receipt = Receipt::with('receiptType')->find($id);
    //      if(!$receipt){
    //          return $this->sendError("receipt not found", ['error'=>['receipt not found']]);
    //      }
         
    //      if(!empty($receipt->receipt_file) && Storage::exists('public/Receipt/'.$receipt->receipt_file)) {
    //          Storage::delete('public/Receipt/'.$receipt->receipt_file);
    //      }
 
    //      $data = [
    //          'receipt' => $receipt,
    //      ];

    //      $defaultConfig = (new ConfigVariables())->getDefaults();
    //      $fontDirs = $defaultConfig['fontDir'];
     
    //      $defaultFontConfig = (new FontVariables())->getDefaults();
    //      $fontData = $defaultFontConfig['fontdata'];
     
    //      $mpdf = new Mpdf([
    //          'mode' => 'utf-8',
    //          'format' => [135,135],
    //          'orientation' => 'P',
    //          'fontDir' => array_merge($fontDirs, [
    //              storage_path('fonts/'), // Update to point to the storage/fonts directory
    //          ]),
    //          'fontdata' => $fontData + [
    //              'notosansdevanagari' => [
    //                  'R' => 'NotoSansDevanagari-Regular.ttf',
    //                  'B' => 'NotoSansDevanagari-Bold.ttf',
    //              ],
    //          ],
    //          'default_font' => 'notosansdevanagari',
    //          'margin_top' => 8,        // Set top margin to 0
    //          'margin_left' => 11,      // Optional: Set left margin if needed
    //          'margin_right' => 11,     // Optional: Set right margin if needed
    //          'margin_bottom' => 8,     // Optional: Set bottom margin if needed
    //      ]);

    //       //  strt watermar
    //         // Add watermark "Duplicate" before content rendering
    //     $mpdf->SetFont('notosansdevanagari', 'B', 50); // Set font and size for watermark
    //     $mpdf->SetTextColor(200, 200, 200); // Set text color to light gray

    //     // Get page width and height
    //     $pageWidth = $mpdf->w;
    //     $pageHeight = $mpdf->h;

    //     // Place watermark diagonally from bottom-left to top-right
    //     $mpdf->Text($pageWidth * 0.1, $pageHeight * 0.9, 'Duplicate');  // Start at bottom-left and go diagonally
    //     $mpdf->Text($pageWidth * 0.3, $pageHeight * 0.7, 'Duplicate');
    //     $mpdf->Text($pageWidth * 0.5, $pageHeight * 0.5, 'Duplicate');
    //     $mpdf->Text($pageWidth * 0.7, $pageHeight * 0.3, 'Duplicate');
    //     $mpdf->Text($pageWidth * 0.9, $pageHeight * 0.1, 'Duplicate'); // 
    //             // end watermarkl
 
    //      // Render the Blade view to HTML
    //      $html = view('Receipt.receipt', $data)->render();

 
    //      // Write HTML to the PDF
    //      $mpdf->WriteHTML($html);
    //      $randomNumber = rand(1000, 9999);
    //      // Define the file path for saving the PDF
    //      $filePath = 'public/Receipt/receipt' . time(). $randomNumber . '.pdf'; // Store in 'storage/app/invoices'
    //      $fileName = basename($filePath); // Extracts 'invoice_{timestamp}{user_id}.pdf'
    //      $receipt->receipt_file = $fileName;
    //      $printCount = $receipt->print_count;
    //      if($receipt->print_count >=1){
    //          $receipt->print_count = $printCount +1;
    //      }else{
    //         $receipt->print_count = 1;
    //      }
    //      $receipt->save();
       
    //      // Save PDF to storage
    //      Storage::put($filePath, $mpdf->Output('', 'S')); // Output as string and save to storage
 
    //      // Output the PDF for download
    //      return $mpdf->Output('receipt.pdf', 'D'); // Download the PDF
    //      // return $this->sendResponse([], "Invoice generated successfully");
    //  }
    public function generateReceipt(string $id)
    {
        $receipt = Receipt::with('libraryReceipt','hallReceipt','vasturupeeReceipt','poojas','bhangarReceipt','anteshteeReceipt','uparaneReceipt','sareeReceipt','naralReceipt','khatReceipt','profile','pooja.poojaType','receiptType')->find($id);
        if (!$receipt) {
            return $this->sendError("receipt not found", ['error' => ['receipt not found']]);
        }
    
        // if (!empty($receipt->receipt_file) && Storage::exists('public/Receipt/' . $receipt->receipt_file)) {
        //     Storage::delete('public/Receipt/' . $receipt->receipt_file);
        // }
    
        $data = [
            'receipt' => $receipt,
        ];
    
        // Initialize mPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            // 'format' => [135, 135],
            'format' => [152.4, 154.94],            
            'orientation' => 'P',
            'margin_top' => 37,        // Set top margin to 0
            'margin_left' => 25,      // Optional: Set left margin if needed
            'margin_right' => 25,     // Optional: Set right margin if needed
            'margin_bottom' => 15,     // Optional: Set bottom margin if needed
        ]);
        $printCount = $receipt->print_count;

        if($receipt->cancelled){
            $mpdf->SetWatermarkText('Cancelled'); 
            $mpdf->showWatermarkText = true; 
        }elseif($receipt->print_count >=1)
        {
            $mpdf->SetWatermarkText('Duplicate '.$printCount); 
            $mpdf->showWatermarkText = true; 
        }
       
        if($receipt->receipt_type_id == 12){
            $html = view('Receipt.dainandin_abhishek_receipt.index', $data)->render();
        }
        else if($receipt->receipt_type_id == 1){
            $html = view('Receipt.khat_vikri_receipt.index', $data)->render();
        }
        else if($receipt->receipt_type_id == 2){
            $html = view('Receipt.naral_vikri_receipt.index', $data)->render();
        }
        else if($receipt->receipt_type_id == 3){
            $html = view('Receipt.bhangar_receipt.index', $data)->render();
        }
        else if($receipt->receipt_type_id == 4){
            $html = view('Receipt.saree_receipt.index', $data)->render();
        }
        else if($receipt->receipt_type_id == 5){
            $html = view('Receipt.uparane_receipt.index', $data)->render();
        }
        else if($receipt->receipt_type_id == 6){
            $html = view('Receipt.vasturupi_receipt.index', $data)->render();
        }
        else if($receipt->receipt_type_id == 8 || $receipt->receipt_type_id == 42 || $receipt->receipt_type_id == 43){
            $html = view('Receipt.library_receipt.index', $data)->render();
        }
        else if($receipt->receipt_type_id == 9){
            $html = view('Receipt.hall_receipt.index', $data)->render();
        }
        else if($receipt->receipt_type_id == 11){
            $html = view('Receipt.anteshti_karma_receipt.index', $data)->render();
        }
        else if($receipt->receipt_type_id == 13){
            $html = view('Receipt.pooja_receipt_anek.index', $data)->render();
        }
        else if($receipt->receipt_type_id == 14){
            $html = view('Receipt.bharani_shradhh_receipt.index', $data)->render();
        }
        else{
            $html = view('Receipt.receipt', $data)->render();
        }
    
        $mpdf->WriteHTML($html);
    
        $randomNumber = rand(1000, 9999);
        // Define the file path for saving the PDF
        $filePath = 'public/Receipt/receipt' . time() . $randomNumber . '.pdf'; // Store in 'storage/app/invoices'
        $fileName = basename($filePath); // Extracts 'invoice_{timestamp}{user_id}.pdf'
        // $receipt->receipt_file = $fileName;
        if ($receipt->print_count >= 1) {
            $receipt->print_count = $printCount + 1;
        } else {
            $receipt->print_count = 1;
        }
        $receipt->save();
    
        // Save PDF to storage
        // Storage::put($filePath, $mpdf->Output('', 'S')); // Output as string and save to storage
    
        // Output the PDF for download
        return $mpdf->Output('receipt.pdf', 'D'); // Download the PDF
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

     /**
     * Saree Date morning.
     */
    public function SareeDate(): JsonResponse
    {
        $sareeData = Receipt::with("sareeReceipt")
        ->where('receipt_type_id', 4)
        ->whereHas('sareeReceipt', function ($query) {
            $query->whereNotNull('saree_draping_date_morning');  // Exclude records where saree_draping_date_morning is null
        })
        ->latest()
        ->first();

        if (!$sareeData || !$sareeData->sareeReceipt) {
        return $this->sendError("No saree receipt found", ['error' => 'No saree receipt found']);
        }

        $oldDate = $sareeData->sareeReceipt->saree_draping_date_morning;

        $newDate = Carbon::parse($oldDate)->addDay(); // You can replace `addDay()` with `addDays($number)` for multiple days.

        return $this->sendResponse([
        "SareeDrapingDateMorning" => $newDate->format('Y-m-d'), // Format as per your requirement
        ], "Saree Date retrieved successfully");
    }

    /**
     * Saree Date evening.
     */
    public function SareeDateEvening(): JsonResponse
    {
        $sareeData = Receipt::with("sareeReceipt")
        ->where('receipt_type_id', 4)
        ->whereHas('sareeReceipt', function ($query) {
            $query->whereNotNull('saree_draping_date_evening');  // Exclude records where saree_draping_date_morning is null
        })
        ->latest()
        ->first();
    
        if (!$sareeData || !$sareeData->sareeReceipt) {
        return $this->sendError("No saree receipt found", ['error' => 'No saree receipt found']);
        }

        $oldDate = $sareeData->sareeReceipt->saree_draping_date_evening;

        $newDate = Carbon::parse($oldDate)->addDay(); // You can replace `addDay()` with `addDays($number)` for multiple days.

        return $this->sendResponse([
        "SareeDrapingDateEvening" => $newDate->format('Y-m-d'), // Format as per your requirement
        ], "Saree evening Date retrieved successfully");
    }

     /**
     * Uparane Date.
     */
    public function UparaneDate(): JsonResponse
    {
        $uparaneData = Receipt::with("uparaneReceipt")
        ->where('receipt_type_id', 5)
        ->whereHas('uparaneReceipt', function ($query) {
            $query->whereNotNull('uparane_draping_date_morning');  // Exclude records where saree_draping_date_morning is null
        })
        ->latest() // Orders by 'created_at' descending by default
        ->first(); // Gets the latest (first) record

        if (!$uparaneData || !$uparaneData->uparaneReceipt) {
        // Handle case where no sareeReceipt or sareeData is found
        return $this->sendError("No uparane receipt found", ['error' => 'No uparane receipt found']);
        }

        // Assuming 'saree_draping_date' is a date field in the sareeReceipt
        $oldDate = $uparaneData->uparaneReceipt->uparane_draping_date_morning;

        // Get the next date (for example, 1 day after the old date)
        $newDate = Carbon::parse($oldDate)->addDay(); // You can replace `addDay()` with `addDays($number)` for multiple days.

        return $this->sendResponse([
        "UparaneDrapingDate" => $newDate->format('Y-m-d'), // Format as per your requirement
        ], "Uparane Date retrieved successfully");
    }

     /**
     * Uparane Date evening.
     */
    public function UparaneDateEvening(): JsonResponse
    {
        $uparaneData = Receipt::with("uparaneReceipt")
        ->where('receipt_type_id', 5)
        ->whereHas('uparaneReceipt', function ($query) {
            $query->whereNotNull('uparane_draping_date_evening');  // Exclude records where saree_draping_date_morning is null
        })
        ->latest()
        ->first();
    
        if (!$uparaneData || !$uparaneData->uparaneReceipt) {
        return $this->sendError("No uparane receipt found", ['error' => 'No uparane receipt found']);
        }

        $oldDate = $uparaneData->uparaneReceipt->uparane_draping_date_evening;

        $newDate = Carbon::parse($oldDate)->addDay(); // You can replace `addDay()` with `addDays($number)` for multiple days.

        return $this->sendResponse([
        "UparaneDrapingDateEvening" => $newDate->format('Y-m-d'), // Format as per your requirement
        ], "Uparane evening Date retrieved successfully");
    }
   
}