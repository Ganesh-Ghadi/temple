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
        $receipt_head = $request->input('receipt_head');

        $receipts = Receipt::with('receiptType');

        if ($from_date && $to_date) {
            // Ensure the dates are in the correct format (e.g., Y-m-d)
            $from_date = \Carbon\Carbon::parse($from_date)->startOfDay();
            $to_date = \Carbon\Carbon::parse($to_date)->endOfDay();
            $receipts->whereBetween('receipt_date', [$from_date, $to_date]);
        }
        
        if ($receipt_head) {
            $receipts->where('receipt_head', $receipt_head);
        }
    
        $receipts = $receipts->get();
        

        if(!$receipts){
            return $this->sendError("receipts not found", ['error'=>['receipts not found']]);
        }
        
        if ($receipt_head) {
            $cashTotal = Receipt::where('payment_mode', 'Cash')
        ->where('cancelled', false)
        ->Where("receipt_head", $receipt_head) 
        ->whereBetween('receipt_date', [$from_date, $to_date])
        ->sum('amount');

        $upiTotal = Receipt::where('payment_mode', 'UPI')
        ->where('cancelled', false) 
        ->where("receipt_head", $receipt_head) 
        ->whereBetween('receipt_date', [$from_date, $to_date])
        ->sum('amount');
        
        $chequeTotal = Receipt::where('payment_mode', 'Bank')
        ->where('cancelled', false) 
        ->where("receipt_head", $receipt_head) 
        ->whereBetween('receipt_date', [$from_date, $to_date])
        ->sum('amount');
        
        $cardTotal = Receipt::where('payment_mode', 'Card')
        ->where('cancelled', false) 
        ->where("receipt_head", $receipt_head) 
        ->whereBetween('receipt_date', [$from_date, $to_date])
        ->sum('amount');

        $total = Receipt::where('cancelled', false) 
        ->where("receipt_head", $receipt_head) 
        ->whereBetween('receipt_date', [$from_date, $to_date])
        ->sum('amount');

        }else{
            $cashTotal = Receipt::where('payment_mode', 'Cash')
        ->where('cancelled', false)
        ->whereBetween('receipt_date', [$from_date, $to_date])
        ->sum('amount');

        $upiTotal = Receipt::where('payment_mode', 'UPI')
        ->where('cancelled', false) 
        ->whereBetween('receipt_date', [$from_date, $to_date])
        ->sum('amount');
        
        $chequeTotal = Receipt::where('payment_mode', 'Bank')
        ->where('cancelled', false) 
        ->whereBetween('receipt_date', [$from_date, $to_date])
        ->sum('amount');
        
        $cardTotal = Receipt::where('payment_mode', 'Card')
        ->where('cancelled', false) 
        ->whereBetween('receipt_date', [$from_date, $to_date])
        ->sum('amount');

        $total = Receipt::where('cancelled', false) 
        ->whereBetween('receipt_date', [$from_date, $to_date])
        ->sum('amount');
        }
        
        

        $data = [
            'receipts' => $receipts,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'cashTotal' => $cashTotal,
            'upiTotal' => $upiTotal,
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

    public function khatReport(Request $request)
    {
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $receipts = Receipt::with('khatReceipt')
        ->where("receipt_type_id", 1)
        ->where("cancelled", false);

        if ($from_date && $to_date) {
            $from_date = \Carbon\Carbon::parse($from_date)->startOfDay();
            $to_date = \Carbon\Carbon::parse($to_date)->endOfDay();
            $receipts->whereBetween('receipt_date', [$from_date, $to_date]);
        }
        
        $total = $receipts->sum("amount");
       
      
        $receipts = $receipts->get();

        $totalQuantity = 0;
        foreach ($receipts as $receipt) {
            if ($receipt->khatReceipt) {
                $khatReceipts[] = $receipt->khatReceipt;
                $totalQuantity += $receipt->khatReceipt->quantity;
            }
        }
    
        

        if(!$receipts){
            return $this->sendError("receipts not found", ['error'=>['receipts not found']]);
        }
        
        $data = [
            'receipts' => $receipts,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'total' => $total,
            'totalQuantity' => $totalQuantity,
        ];

        // Render the Blade view to HTML
        $html = view('Reports.khatReport.index', $data)->render();

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
                <h4 style="margin: 0; padding: 0;">श्री गणेश मंदिर संस्थान - खत विक्री पावत्या ' . $fromDateFormatted . ' ते ' . $toDateFormatted . '</h4>
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
        // Output the PDF for download
        return $mpdf->Output('receipt.pdf', 'D'); // Download the PDF
        // return $this->sendResponse([], "Invoice generated successfully");
    }


    public function ReceiptSummaryReport(Request $request)
    {
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $receipt_head = $request->input('receipt_head');

        $receipts = Receipt::with('receiptType')->where('cancelled', false);;
        
        if ($receipt_head) {
            $receipts->where('receipt_head', $receipt_head);
        }
        //  topped here
        if ($from_date && $to_date) {
            // Ensure the dates are in the correct format (e.g., Y-m-d)
            $from_date = \Carbon\Carbon::parse($from_date)->startOfDay();
            $to_date = \Carbon\Carbon::parse($to_date)->endOfDay();
    
            $receipts->whereBetween('receipt_date', [$from_date, $to_date]);
        }

        // $receipts = $receipts->get()->groupBy('receipt_head');
          $receipts = $receipts->get()->groupBy('receipt_head')->map(function($group) {
            // Further group each receipt_head group by receiptType
            return $group->groupBy('receiptType.receipt_type');
        });

    
        $receiptsWithTotal = $receipts->map(function($receiptHeadGroup) {
            return $receiptHeadGroup->map(function($group) {
                // For each receiptType group, calculate the totals by payment_mode
                $totalBank = $group->where('payment_mode', 'Bank')->sum('amount');
                $totalCash = $group->where('payment_mode', 'Cash')->sum('amount');
                $totalUPI = $group->where('payment_mode', 'UPI')->sum('amount');
                $totalCard = $group->where('payment_mode', 'Card')->sum('amount');
                $totalAmount = $group->sum('amount'); // Calculate the total amount for this group
    
                return [
                    'receipts' => $group,
                    'total_bank' => $totalBank,
                    'total_cash' => $totalCash,
                    'total_upi' => $totalUPI,
                    'total_card' => $totalCard,
                    'total_amount' => $totalAmount,  // Add the total amount for this group
                ];
            });
        });
    
        if(!$receiptsWithTotal){
            return $this->sendError("receipts not found", ['error'=>['receipts not found']]);
        }
        

        $data = [
            'receiptsWithTotal' => $receiptsWithTotal,
            'from_date' => $from_date,
            'to_date' => $to_date,
        ];

        // Render the Blade view to HTML
        $html = view('Receipt.ReceiptSummary.receipt', $data)->render();

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
                <h4 style="margin: 0; padding: 0;">श्री गणेश मंदिर संस्थान - पावती सारांश ' . $fromDateFormatted . ' ते ' . $toDateFormatted . '</h4>
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
        // Output the PDF for download
        return $mpdf->Output('receipt.pdf', 'D'); // Download the PDF
        // return $this->sendResponse([], "Invoice generated successfully");
    }


    public function ChequeCollectionReport(Request $request)
    {
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $receipts = Receipt::with('receiptType')
        ->where('payment_mode', 'Bank');

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
        
        
        
        $bankTotal = Receipt::where('payment_mode', 'Bank')
        ->where('cancelled', false) 
        ->whereBetween('receipt_date', [$from_date, $to_date])
        ->sum('amount');
        
       

        $data = [
            'receipts' => $receipts,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'bankTotal' => $bankTotal,
        ];

        // Render the Blade view to HTML
        $html = view('Receipt.cheque_receipts.receipt', $data)->render();

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
                <h4 style="margin: 0; padding: 0;">श्री गणेश मंदिर संस्थान - चेक जमा सारांश ' . $fromDateFormatted . ' ते ' . $toDateFormatted . '</h4>
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


    public function upiCollectionReport(Request $request)
    {
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $receipts = Receipt::with('receiptType')
        ->where('payment_mode', 'UPI');
        
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
        
        
        
        $upiTotal = Receipt::where('payment_mode', 'UPI')
        ->where("cancelled", false)
        ->whereBetween('receipt_date', [$from_date, $to_date])
        ->sum('amount');
        
       

        $data = [
            'receipts' => $receipts,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'upiTotal' => $upiTotal,
        ];

        // Render the Blade view to HTML
        $html = view('Reports.Collections.upi_collections', $data)->render();

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
                <h4 style="margin: 0; padding: 0;">श्री गणेश मंदिर संस्थान - यू पी आय जमा सारांश ' . $fromDateFormatted . ' ते ' . $toDateFormatted . '</h4>
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



    public function naralReport(Request $request)
    {
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $receipts = Receipt::with('naralReceipt')
        ->where("receipt_type_id", 2)
        ->where("cancelled", false);

        if ($from_date && $to_date) {
            $from_date = \Carbon\Carbon::parse($from_date)->startOfDay();
            $to_date = \Carbon\Carbon::parse($to_date)->endOfDay();
            $receipts->whereBetween('receipt_date', [$from_date, $to_date]);
        }
        
        $total = $receipts->sum("amount");
       
      
        $receipts = $receipts->get();

        $totalQuantity = 0;
        foreach ($receipts as $receipt) {
            if ($receipt->naralReceipt) {
                $totalQuantity += $receipt->naralReceipt->quantity;
            }
        }
    
        if(!$receipts){
            return $this->sendError("receipts not found", ['error'=>['receipts not found']]);
        }
        
        $data = [
            'receipts' => $receipts,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'total' => $total,
            'totalQuantity' => $totalQuantity,
        ];

        // Render the Blade view to HTML
        $html = view('Reports.naralReport.index', $data)->render();

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
                <h4 style="margin: 0; padding: 0;">श्री गणेश मंदिर संस्थान - नारळ विक्री पावत्या ' . $fromDateFormatted . ' ते ' . $toDateFormatted . '</h4>
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
        // Output the PDF for download
        return $mpdf->Output('receipt.pdf', 'D'); // Download the PDF
        // return $this->sendResponse([], "Invoice generated successfully");
    }



    public function CancelledReceiptReport(Request $request)
    {
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $receipt_head = $request->input('receipt_head');

        $receipts = Receipt::with('receiptType')->where("cancelled", true);

        if ($from_date && $to_date) {
            // Ensure the dates are in the correct format (e.g., Y-m-d)
            $from_date = \Carbon\Carbon::parse($from_date)->startOfDay();
            $to_date = \Carbon\Carbon::parse($to_date)->endOfDay();
            $receipts->whereBetween('receipt_date', [$from_date, $to_date]);
        }
        
        if ($receipt_head) {
            $receipts->where('receipt_head', $receipt_head);
        }
    
        $receipts = $receipts->get();
        

        if(!$receipts){
            return $this->sendError("receipts not found", ['error'=>['receipts not found']]);
        }

        $data = [
            'receipts' => $receipts,
            'from_date' => $from_date,
            'to_date' => $to_date, 
        ];

        // Render the Blade view to HTML
        $html = view('Reports.CancelledReceiptReport.index', $data)->render();

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
                <h4 style="margin: 0; padding: 0;">श्री गणेश मंदिर संस्थान - रद्द पावत्या ' . $fromDateFormatted . ' ते ' . $toDateFormatted . '</h4>
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

/*
$receipts = Receipt::with('receiptType')
                   ->whereHas('receiptType', function($query) {
                       $query->groupBy('receipt_type');  // Group by receipt_type to ensure uniqueness
                   })
                   ->get();

                   
$receipts = Receipt::with('receiptType')->get()->groupBy('receiptType.receipt_type');

foreach ($receipts as $receiptType => $group) {
    // Each $group will contain all receipts with the same $receiptType
}

$receipts = Receipt::with('receiptType')->get()->groupBy('receipt_head');

// Calculate the total amount for each group
$receiptsWithTotal = $receipts->map(function($group) {
    $totalAmount = $group->sum('amount'); // Calculate the total amount for each group
    return [
        'receipts' => $group,
        'total_amount' => $totalAmount,  // Add the total amount for this group
    ];
});



{
    "a": {
        "receipts": [
            {"receipt_head": "a", "amount": 2},
            {"receipt_head": "a", "amount": 4},
            {"receipt_head": "a", "amount": 6}
        ],
        "total_amount": 12
    },
    "z": {
        "receipts": [
            {"receipt_head": "z", "amount": 5}
        ],
        "total_amount": 5
    },
    "x": {
        "receipts": [
            {"receipt_head": "x", "amount": 3},
            {"receipt_head": "x", "amount": 7},
            {"receipt_head": "x", "amount": 8}
        ],
        "total_amount": 18
    },
    "c": {
        "receipts": [
            {"receipt_head": "c", "amount": 10},
            {"receipt_head": "c", "amount": 20}
        ],
        "total_amount": 30
    }
}

*/