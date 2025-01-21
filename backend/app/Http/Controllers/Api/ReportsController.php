<?php

namespace App\Http\Controllers\Api;

use File;
use Response;
use Mpdf\Mpdf;
use App\Models\Receipt;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Mpdf\Config\FontVariables;
use Mpdf\Config\ConfigVariables;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;

class ReportsController extends BaseController
{
    public function AllReceiptReport(Request $request)
    {
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $receipts = Receipt::with('receiptType');

        if ($from_date && $to_date) {
            // Ensure the dates are in the correct format (e.g., Y-m-d)
            $from_date = \Carbon\Carbon::parse($from_date)->startOfDay();
            $to_date = \Carbon\Carbon::parse($to_date)->endOfDay();
    
            $receipts->whereBetween('receipt_date', [$from_date, $to_date]);
        }
    
        $receipts = $receipts->get();
        

        if(!$receipts){
            return $this->sendError("receipts not found", ['error'=>['receipts not found']]);
        }
        
        $cashTotal = Receipt::where('payment_mode', 'Cash')
        ->whereBetween('receipt_date', [$from_date, $to_date])
        ->sum('amount');
        
        $chequeTotal = Receipt::where('payment_mode', 'Bank')
        ->whereBetween('receipt_date', [$from_date, $to_date])
        ->sum('amount');
        
        $cardTotal = Receipt::where('payment_mode', 'Card')
        ->whereBetween('receipt_date', [$from_date, $to_date])
        ->sum('amount');

        $total = Receipt::whereBetween('receipt_date', [$from_date, $to_date])
        ->sum('amount');

        $data = [
            'receipts' => $receipts,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'cashTotal' => $cashTotal,
            'chequeTotal' => $chequeTotal,
            'cardTotal' => $cardTotal,
            'Total' => $total
        ];

        // Render the Blade view to HTML
        $html = view('Receipt.all_receipts.receipt', $data)->render();

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
                'margin_top' => 18,        // Set top margin to 0
                'margin_left' => 8,      // Optional: Set left margin if needed
                'margin_right' => 8,     // Optional: Set right margin if needed
                'margin_bottom' => 20,     // Optional: Set bottom margin if needed
            ]);
            
            $fromDateFormatted = \Carbon\Carbon::parse($from_date)->format('d/m/Y');
            $toDateFormatted = \Carbon\Carbon::parse($to_date)->format('d/m/Y');
            
            // Set header HTML with dynamic values
            $headerHtml = '
            <div style="text-align: center;">
                <h4 style="margin: 0; padding: 0;">श्री गणेश मंदिर संस्थान - सर्व पावत्या ' . $fromDateFormatted . ' ते ' . $toDateFormatted . '</h4>
            </div>
            <p style="border: 1px solid black; width:100%; margin:0px; padding:0px; margin-bottom:5px;"></p>';
            
            // Set the header for each page
            $mpdf->SetHTMLHeader($headerHtml);
            
            $footerHtml = '
            <div style="border-top: 1px solid black; display: flex; justify-content: space-between; padding: 5px;">
                <p style="margin: 0; text-align: center; flex: 1;">Printed on ' . \Carbon\Carbon::now()->format('d-m-Y H:i') . '</p>
                <p style="margin: 0; text-align: right; flex: 1;">Page {PAGENO} of {nb}</p>
            </div>';

            
            $mpdf->SetHTMLFooter($footerHtml);


        // Write HTML to the PDF
        $mpdf->WriteHTML($html);
        $randomNumber = rand(1000, 9999);
        // Define the file path for saving the PDF
        $filePath = 'public/Receipt/receipt' . time(). $randomNumber . '.pdf'; // Store in 'storage/app/invoices'
        $fileName = basename($filePath); // Extracts 'invoice_{timestamp}{user_id}.pdf'
             Log::info("working");
        // Output the PDF for download
        return $mpdf->Output('receipt.pdf', 'D'); // Download the PDF
        // return $this->sendResponse([], "Invoice generated successfully");
    }
}





//             $footerHtml = '
// <div style="border-top: 1px solid black; display: flex; justify-content: space-between; padding: 5px; width:100%">
//     <span style="margin: 0; text-align: center; flex: 1;">Printed on ' . \Carbon\Carbon::now()->format('d-m-Y H:i') . '</span>
//     <span style="margin: 0; text-align: right; flex: 1;">Page {PAGENO} of {nb}</span>
// </div>';


            // $footerHtml = '
            // <div style="border-top: 1px solid black; width: 100%; padding: 5px;">
            //     <table style="width: 100%; border: none;">
            //         <tr>
            //             <td style="text-align: center; width: 80%; border:none">Printed on ' . \Carbon\Carbon::now()->format('d-m-Y H:i') . '</td>
            //             <td style="text-align: right; width: 20%; border:none">Page {PAGENO} of {nb}</td>
            //         </tr>
            //     </table>
            // </div>';