<?php

namespace App\Models;

use App\Models\Pooja;
use App\Models\Guruji;
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
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
   

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
        $lastNumber = 1;
    
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

    public function guruji(){
        return $this->belongsTo(Guruji::class, 'guruji_id');
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

    public static function sendWhatsAppMessage($receipt){
          // Prepare WhatsApp payload
          $paymentModeMap = [
            'Cash' => 'रोख',
            'Card' => 'Card',
            'UPI'  => 'UPI',
            'Bank' => 'धनादेश',
        ];
          $payload = [
            "messaging_product" => "whatsapp",
            "to" => "91". $receipt->mobile,
            "type" => "template",
            "template" => [
                "name" => "general_receipt",
                "language" => [
                    "code" => "en"
                ],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            [ "type" => "text", "text" => \Carbon\Carbon::parse($receipt->receipt_date)->format('d/m/Y'), ],
                            [ "type" => "text", "text" => $receipt->receiptType->receipt_type],
                            [ "type" => "text", "text" => $receipt->receipt_no ],
                            [ "type" => "text", "text" => (string)$receipt->amount ],
                            [ "type" => "text", "text" => $paymentModeMap[$receipt->payment_mode] ]
                        ]
                    ]
                ]
            ]
        ];
         
         $apiKey = config('data.whatsapp.api_key');
          $response = Http::withHeaders([
              'Authorization' => 'Bearer '.$apiKey,
              'Content-Type' => 'application/json',
          ])->post('https://graph.facebook.com/v22.0/659774710543447/messages', $payload);

          // Log or handle the response
          if ($response->successful()) {
              \Log::info('WhatsApp message sent successfully.');
          } else {
              \Log::error('Failed to send WhatsApp message', [
                  'response' => $response->body()
              ]);
          }
    }


    public static function sendSareeWhatsAppMessageDaily($receipt){
        // Prepare WhatsApp payload
        
        $payload = [
          "messaging_product" => "whatsapp",
          "to" => "91". $receipt->mobile,
          "type" => "template",
          "template" => [
              "name" => "saree_template",
              "language" => [
                  "code" => "en"
              ],
              "components" => [
                  [
                      "type" => "body",
                      "parameters" => [
                          [ "type" => "text", "text" => \Carbon\Carbon::parse($receipt->receipt_date)->format('d/m/Y'), ],
                          [ "type" => "text", "text" => $receipt->receipt_no ],
                          [ "type" => "text", "text" => \Carbon\Carbon::today()->format('d/m/Y') ],  // <-- today's date here
                        ]
                  ]
              ]
          ]
      ];
       
       $apiKey = config('data.whatsapp.api_key');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://graph.facebook.com/v22.0/659774710543447/messages', $payload);

        // Log or handle the response
        if ($response->successful()) {
            \Log::info('saree WhatsApp message sent successfully.');
        } else {
            \Log::error('Failed to send saree WhatsApp message', [
                'response' => $response->body()
            ]);
        }
  }
}