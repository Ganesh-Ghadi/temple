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
            'Total' => $total,
            'receiptHead' => $receipt_head,
        ];

        // Render the Blade view to HTML
        $html = view('Reports.AllReceiptReport.index', $data)->render();

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
                <p style="margin: 0; padding: 0; font-size:17px;">श्री गणेश मंदिर संस्थान - सर्व पावत्या '. $receipt_head .' ' . $fromDateFormatted . ' ते ' . $toDateFormatted . '</p>
            </div>
            <p style="border: 1px solid black; width:100%; margin:0px; padding:0px; margin-bottom:5px;"></p>';
            
            // Set the header for each page
            $mpdf->SetHTMLHeader($headerHtml);
            
            // $footerHtml = '
            // <div style="border-top: 1px solid black; display: flex; justify-content: space-between; padding: 5px;">
            //     <p style="margin: 0; text-align: center; flex: 1;">Printed on ' . \Carbon\Carbon::now()->format('d-m-Y H:i') . '</p>
            //     <p style="margin: 0; text-align: right; flex: 1;">Page {PAGENO} of {nb}</p>
            // </div>';
            $footerHtml = '
            <div style="border-top: 1px solid black; margin-top: 5px;"></div> <!-- Line above the footer -->
            <div style="width: 100%; text-align: center; padding-top: 5px;">
                <span>Printed on ' . \Carbon\Carbon::now()->format('d/m/Y h:i A') . '</span>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                 <span>Page {PAGENO} of {nb}</span>
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
                <p style="margin: 0; padding: 0; font-size:17px;">श्री गणेश मंदिर संस्थान - खत विक्री पावत्या ' . $fromDateFormatted . ' ते ' . $toDateFormatted . '</p>
            </div>
            <p style="border: 1px solid black; width:100%; margin:0px; padding:0px; margin-bottom:5px;"></p>';
            
            // Set the header for each page
            $mpdf->SetHTMLHeader($headerHtml);
            
            // $footerHtml = '
            // <div style="border-top: 1px solid black; display: flex; justify-content: space-between; padding: 5px;">
            //     <p style="margin: 0; text-align: center; flex: 1;">Printed on ' . \Carbon\Carbon::now()->format('d-m-Y H:i') . '</p>
            //     <p style="margin: 0; text-align: right; flex: 1;">Page {PAGENO} of {nb}</p>
            // </div>';

            $footerHtml = '
            <div style="border-top: 1px solid black; margin-top: 5px;"></div> <!-- Line above the footer -->
            <div style="width: 100%; text-align: center; padding-top: 5px;">
                <span>Printed on ' . \Carbon\Carbon::now()->format('d/m/Y h:i A') . '</span>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                 <span>Page {PAGENO} of {nb}</span>
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

        $receipts = Receipt::with('receiptType')->where('cancelled', false);
        
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
            'receiptsWithTotal' => $receiptsWithTotal,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'cashTotal' => $cashTotal,
            'upiTotal' => $upiTotal,
            'chequeTotal' => $chequeTotal,
            'cardTotal' => $cardTotal,
            'Total' => $total,
        ];

        // Render the Blade view to HTML
        $html = view('Reports.ReceiptSummaryReport.index', $data)->render();

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
                <p style="margin: 0; padding: 0; font-size:17px;">श्री गणेश मंदिर संस्थान - पावती सारांश '. $receipt_head .' ' . $fromDateFormatted . ' ते ' . $toDateFormatted . '</p>
            </div>
            <p style="border: 1px solid black; width:100%; margin:0px; padding:0px; margin-bottom:5px;"></p>';
            
            // Set the header for each page
            $mpdf->SetHTMLHeader($headerHtml);
            
            // $footerHtml = '
            // <div style="border-top: 1px solid black; display: flex; justify-content: space-between; padding: 5px;">
            //     <p style="margin: 0; text-align: center; flex: 1;">Printed on ' . \Carbon\Carbon::now()->format('d-m-Y H:i') . '</p>
            //     <p style="margin: 0; text-align: right; flex: 1;">Page {PAGENO} of {nb}</p>
            // </div>';

            $footerHtml = '
            <div style="border-top: 1px solid black; margin-top: 5px;"></div> <!-- Line above the footer -->
            <div style="width: 100%; text-align: center; padding-top: 5px;">
                <span>Printed on ' . \Carbon\Carbon::now()->format('d/m/Y h:i A') . '</span>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                 <span>Page {PAGENO} of {nb}</span>
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
        $html = view('Reports.Collections.cheque_collections', $data)->render();

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
                <p style="margin: 0; padding: 0; font-size:17px;">श्री गणेश मंदिर संस्थान - चेक जमा सारांश ' . $fromDateFormatted . ' ते ' . $toDateFormatted . '</p>
            </div>
            <p style="border: 1px solid black; width:100%; margin:0px; padding:0px; margin-bottom:5px;"></p>';
            
            // Set the header for each page
            $mpdf->SetHTMLHeader($headerHtml);
            
            // $footerHtml = '
            // <div style="border-top: 1px solid black; display: flex; justify-content: space-between; padding: 5px;">
            //     <p style="margin: 0; text-align: center; flex: 1;">Printed on ' . \Carbon\Carbon::now()->format('d-m-Y H:i') . '</p>
            //     <p style="margin: 0; text-align: right; flex: 1;">Page {PAGENO} of {nb}</p>
            // </div>';

            $footerHtml = '
            <div style="border-top: 1px solid black; margin-top: 5px;"></div> <!-- Line above the footer -->
            <div style="width: 100%; text-align: center; padding-top: 5px;">
                <span>Printed on ' . \Carbon\Carbon::now()->format('d/m/Y h:i A') . '</span>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                 <span>Page {PAGENO} of {nb}</span>
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
                <p style="margin: 0; padding: 0; font-size:17px;">श्री गणेश मंदिर संस्थान - यू. पी. आय. जमा सारांश ' . $fromDateFormatted . ' ते ' . $toDateFormatted . '</p>
            </div>
            <p style="border: 1px solid black; width:100%; margin:0px; padding:0px; margin-bottom:5px;"></p>';
            
            // Set the header for each page
            $mpdf->SetHTMLHeader($headerHtml);
            
            // $footerHtml = '
            // <div style="border-top: 1px solid black; display: flex; justify-content: space-between; padding: 5px;">
            //     <p style="margin: 0; text-align: center; flex: 1;">Printed on ' . \Carbon\Carbon::now()->format('d-m-Y H:i') . '</p>
            //     <p style="margin: 0; text-align: right; flex: 1;">Page {PAGENO} of {nb}</p>
            // </div>';

            $footerHtml = '
            <div style="border-top: 1px solid black; margin-top: 5px;"></div> <!-- Line above the footer -->
            <div style="width: 100%; text-align: center; padding-top: 5px;">
                <span>Printed on ' . \Carbon\Carbon::now()->format('d/m/Y h:i A') . '</span>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                 <span>Page {PAGENO} of {nb}</span>
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
                <p style="margin: 0; padding: 0; font-size:17px;">श्री गणेश मंदिर संस्थान - नारळ विक्री पावत्या ' . $fromDateFormatted . ' ते ' . $toDateFormatted . '</p>
            </div>
            <p style="border: 1px solid black; width:100%; margin:0px; padding:0px; margin-bottom:5px;"></p>';
            
            // Set the header for each page
            $mpdf->SetHTMLHeader($headerHtml);
            
            // $footerHtml = '
            // <div style="border-top: 1px solid black; display: flex; justify-content: space-between; padding: 5px;">
            //     <p style="margin: 0; text-align: center; flex: 1;">Printed on ' . \Carbon\Carbon::now()->format('d-m-Y H:i') . '</p>
            //     <p style="margin: 0; text-align: right; flex: 1;">Page {PAGENO} of {nb}</p>
            // </div>';

            $footerHtml = '
            <div style="border-top: 1px solid black; margin-top: 5px;"></div> <!-- Line above the footer -->
            <div style="width: 100%; text-align: center; padding-top: 5px;">
                <span>Printed on ' . \Carbon\Carbon::now()->format('d/m/Y h:i A') . '</span>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                 <span>Page {PAGENO} of {nb}</span>
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
                <p style="margin: 0; padding: 0; font-size:17px;">श्री गणेश मंदिर संस्थान - रद्द पावत्या  ' . $receipt_head . ' ' . $fromDateFormatted . ' ते ' . $toDateFormatted . '</p>
            </div>
            <p style="border: 1px solid black; width:100%; margin:0px; padding:0px; margin-bottom:5px;"></p>';
            
            // Set the header for each page
            $mpdf->SetHTMLHeader($headerHtml);
            
            // $footerHtml = '
            // <div style="border-top: 1px solid black; display: flex; justify-content: space-between; padding: 5px;">
            //     <p style="margin: 0; text-align: center; flex: 1;">Printed on ' . \Carbon\Carbon::now()->format('d-m-Y H:i') . '</p>
            //     <p style="margin: 0; text-align: right; flex: 1;">Page {PAGENO} of {nb}</p>
            // </div>';

            $footerHtml = '
            <div style="border-top: 1px solid black; margin-top: 5px;"></div> <!-- Line above the footer -->
            <div style="width: 100%; text-align: center; padding-top: 5px;">
                <span>Printed on ' . \Carbon\Carbon::now()->format('d/m/Y h:i A') . '</span>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                 <span>Page {PAGENO} of {nb}</span>
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

    public function ReceiptReport(Request $request)
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
        $receipts = $receipts->get();


        $TOTAL = $receipts->sum("amount");

        // $receipts = $receipts->get()->groupBy('receipt_head');
          $receipts = $receipts->groupBy('receipt_head')->map(function($group) {
            // Further group each receipt_head group by receiptType
            return $group->groupBy('receiptType.receipt_type');
        });

    
        $receiptsWithTotal = $receipts->map(function($receiptHeadGroup) {
            return $receiptHeadGroup->map(function($group) {
                // For each receiptType group, calculate the totals by payment_mode
               
                $totalAmount = $group->sum('amount'); // Calculate the total amount for this group
    
                return [
                    'receipts' => $group,
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
            'TOTAL' => $TOTAL,
        ];

        // Render the Blade view to HTML
        $html = view('Reports.ReceiptReport.index', $data)->render();

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
                <p style="margin: 0; padding: 0; font-size:17px;">श्री गणेश मंदिर संस्थान - पावती तक्ता  ' . $receipt_head . ' ' . $fromDateFormatted . ' ते ' . $toDateFormatted . '</p>
            </div>
            <p style="border: 1px solid black; width:100%; margin:0px; padding:0px; margin-bottom:5px;"></p>';
            
            // Set the header for each page
            $mpdf->SetHTMLHeader($headerHtml);
            
            // $footerHtml = '
            // <div style="border-top: 1px solid black; display: flex; justify-content: space-between; padding: 5px;">
            //     <p style="margin: 0; text-align: center; flex: 1;">Printed on ' . \Carbon\Carbon::now()->format('d-m-Y H:i') . '</p>
            //     <p style="margin: 0; text-align: right; flex: 1;">Page {PAGENO} of {nb}</p>
            // </div>';

            $footerHtml = '
            <div style="border-top: 1px solid black; margin-top: 5px;"></div> <!-- Line above the footer -->
            <div style="width: 100%; text-align: center; padding-top: 5px;">
                <span>Printed on ' . \Carbon\Carbon::now()->format('d/m/Y h:i A') . '</span>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                 <span>Page {PAGENO} of {nb}</span>
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


    public function gotravaliSummaryReport(Request $request)
    {
        $date = $request->input('date');

        $receipts = Receipt::with(['poojas.poojaType', 'receiptType'])
            ->where("cancelled", false)
            ->whereHas('poojas', function ($query) use ($date) {
                // Apply the condition on pooja's date column
                if ($date) {
                    $query->where('date', $date); // Change 'date' to the actual column name in poojas table
                }
            });
        
        // $receipts = Receipt::with(['pooja.poojaType', 'receiptType']) // Eager load related poojas and receiptType
        //            ->whereHas('pooja');

        // if ($date) {
        //     $receipts->where('receipt_date', $date);
        // }
        
    
        $receipts = $receipts->get();
        // $poojaTypeCounts = $receipts->flatMap(function ($receipt) {
        //     return $receipt->pooja->map(function ($puja) {
        //         return $puja->poojaType->pooja_type;
        //     });
        // })->countBy(); 
        $poojaTypeCounts = $receipts->flatMap(function ($receipt) use ($date) {
            return $receipt->poojas->filter(function ($puja) use ($date) {
                return !$date || $puja->date == $date;
            })->map(function ($puja) {
                return $puja->poojaType->pooja_type;
            });
        })->countBy();
    
        $totalCount = $poojaTypeCounts->sum(); // Sum of all co


    
        if(!$receipts){
            return $this->sendError("receipts not found", ['error'=>['receipts not found']]);
        }
        
        $data = [
            'date' => $date,
            'totalCount' => $totalCount,
            'poojaTypeCounts' => $poojaTypeCounts,

            
        ];

        // Render the Blade view to HTML
        $html = view('Reports.GotravaliSummaryReport.index', $data)->render();

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
            
            $date = \Carbon\Carbon::parse($date)->format('d/m/Y');
            
            // Set header HTML with dynamic values
            $headerHtml = '
            <div style="text-align: center;">
                <p style="margin: 0; padding: 0; font-size:17px;">श्री गणेश मंदिर संस्थान - गोत्रावळी सारांश ' . $date .'.</p>
            </div>
            <p style="border: 1px solid black; width:100%; margin:0px; padding:0px; margin-bottom:5px;"></p>';
            
            // Set the header for each page
            $mpdf->SetHTMLHeader($headerHtml);
            
            // $footerHtml = '
            // <div style="border-top: 1px solid black; display: flex; justify-content: space-between; padding: 5px;">
            //     <p style="margin: 0; text-align: center; flex: 1;">Printed on ' . \Carbon\Carbon::now()->format('d-m-Y H:i') . '</p>
            //     <p style="margin: 0; text-align: right; flex: 1;">Page {PAGENO} of {nb}</p>
            // </div>';

            $footerHtml = '
            <div style="border-top: 1px solid black; margin-top: 5px;"></div> <!-- Line above the footer -->
            <div style="width: 100%; text-align: center; padding-top: 5px;">
                <span>Printed on ' . \Carbon\Carbon::now()->format('d/m/Y h:i A') . '</span>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                 <span>Page {PAGENO} of {nb}</span>
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

   


    public function AnteshteeReport(Request $request)
    {
        $date = $request->input('date');
         $anteshteeReceiptTypeId = 11;
        $receipts = Receipt::with('anteshteeReceipt')
        ->where("cancelled", false)
        ->where('receipt_type_id', $anteshteeReceiptTypeId)
        ->whereHas('anteshteeReceipt', function ($query) use ($date){
            $query->where(function ($subQuery) use ($date) {
            //     $subQuery->where('day_9_date', $date)
            //              ->orWhere('day_10_date', $date)
            //              ->orWhere('day_11_date', $date)
            //              ->orWhere('day_12_date', $date)
            //              ->orWhere('day_13_date', $date);
            // });
                        $subQuery->where('day_9_date', $date)->where('day_9', true)
                        ->orWhere('day_10_date', $date)->where('day_10', true)
                        ->orWhere('day_11_date', $date)->where('day_11', true)
                        ->orWhere('day_12_date', $date)->where('day_12', true)
                        ->orWhere('day_13_date', $date)->where('day_13', true);
            });
        })
        ->get();
      

        if(!$receipts){
            return $this->sendError("receipts not found", ['error'=>['receipts not found']]);
        }
        
        $data = [
            'receipts' => $receipts,
            'date' => $date,
        ];

        // Render the Blade view to HTML
        $html = view('Reports.AnteshteeReceiptReport.index', $data)->render();

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
            
            $DateFormatted = \Carbon\Carbon::parse($date)->format('d/m/Y');
            
            // Set header HTML with dynamic values
            $headerHtml = '
            <div style="text-align: center;">
                <p style="margin: 0; padding: 0; font-size:17px;">श्री गणेश मंदिर संस्थान - अंत्येष्टी कर्म पावत्या ' . $DateFormatted . '.'.'</p>
            </div>
            <p style="border: 1px solid black; width:100%; margin:0px; padding:0px; margin-bottom:5px;"></p>';
            
            // Set the header for each page
            $mpdf->SetHTMLHeader($headerHtml);
            
            // $footerHtml = '
            // <div style="border-top: 1px solid black; display: flex; justify-content: space-between; padding: 5px;">
            //     <p style="margin: 0; text-align: center; flex: 1;">Printed on ' . \Carbon\Carbon::now()->format('d-m-Y H:i') . '</p>
            //     <p style="margin: 0; text-align: right; flex: 1;">Page {PAGENO} of {nb}</p>
            // </div>';
            $footerHtml = '
            <div style="border-top: 1px solid black; margin-top: 5px;"></div> <!-- Line above the footer -->
            <div style="width: 100%; text-align: center; padding-top: 5px;">
                <span>Printed on ' . \Carbon\Carbon::now()->format('d/m/Y h:i A') . '</span>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                 <span>Page {PAGENO} of {nb}</span>
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

    public function ReceiptTotalSummaryReport(Request $request)
    {
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $receipt_head = $request->input('receipt_head');

        $receipts = Receipt::with('receiptType')->where('cancelled', false);
        
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
            'receiptsWithTotal' => $receiptsWithTotal,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'cashTotal' => $cashTotal,
            'upiTotal' => $upiTotal,
            'chequeTotal' => $chequeTotal,
            'cardTotal' => $cardTotal,
            'Total' => $total,
        ];

        // Render the Blade view to HTML
        $html = view('Reports.ReceiptTotalSummaryReport.index', $data)->render();

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
                <p style="margin: 0; padding: 0; font-size:17px;">श्री गणेश मंदिर संस्थान - पावती सारांश '. $receipt_head .' ' . $fromDateFormatted . ' ते ' . $toDateFormatted . '</p>
            </div>
            <p style="border: 1px solid black; width:100%; margin:0px; padding:0px; margin-bottom:5px;"></p>';
            
            // Set the header for each page
            $mpdf->SetHTMLHeader($headerHtml);
            
            // $footerHtml = '
            // <div style="border-top: 1px solid black; display: flex; justify-content: space-between; padding: 5px;">
            //     <p style="margin: 0; text-align: center; flex: 1;">Printed on ' . \Carbon\Carbon::now()->format('d-m-Y H:i') . '</p>
            //     <p style="margin: 0; text-align: right; flex: 1;">Page {PAGENO} of {nb}</p>
            // </div>';

            $footerHtml = '
            <div style="border-top: 1px solid black; margin-top: 5px;"></div> <!-- Line above the footer -->
            <div style="width: 100%; text-align: center; padding-top: 5px;">
                <span>Printed on ' . \Carbon\Carbon::now()->format('d/m/Y h:i A') . '</span>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                 <span>Page {PAGENO} of {nb}</span>
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


    public function gotravaliReport(Request $request)
    {
        $date = $request->input('date');

        $receipts = Receipt::with(['receiptType','poojas.poojaType']) // Eager load related poojas and receiptType
             ->where("cancelled", false)
             ->whereHas('receiptType', function ($query) use ($date) {
                    $query->where('is_pooja', true);
            })
            ->whereHas('poojas', function ($query) use ($date) {
                // Apply the condition on pooja's date column
                if ($date) {
                    $query->where('date', $date); // Change 'date' to the actual column name in poojas table
                }
            });
    
        $receipts = $receipts->get();
       
        $poojaTypeAndGotraCounts = $receipts->flatMap(function ($receipt) use ($date) {
            return $receipt->poojas->filter(function ($puja) use ($date) {
                return !$date || $puja->date == $date; // Apply date filter if provided
            })->map(function ($puja) use ($receipt) {
                return [
                    'receiptType' => $receipt->receiptType->receipt_type,  // Include the receiptType
                    'poojaType' => $puja->poojaType->pooja_type,   // Include poojaType
                    'gotra' => $receipt->gotra,                     // Include gotra from the receipt
                    'name' => $receipt->name,                       // Include the name of the person for this receipt
                ];
            });
        })->groupBy('receiptType') // Group by receiptType first
          ->map(function ($groupedByReceiptType) {
              return $groupedByReceiptType->groupBy('poojaType') // Then group by poojaType within each receiptType
                  ->map(function ($groupedByPoojaType) {
                      return $groupedByPoojaType->groupBy('gotra'); // Finally group by gotra within each poojaType
                  });
        });
    
        // Calculate the total count (number of records)
        $totalCount = $poojaTypeAndGotraCounts->flatten()->count();
    
        // uparane and saree start
        $sareeReceipts = Receipt::with('sareeReceipt')
        ->where("cancelled", false)
        ->whereHas('sareeReceipt', function($query) use ($date) {
            $query->where('saree_draping_date_morning', $date);
        })
        ->get();
    
    if ($sareeReceipts->isNotEmpty()) {
        $sareeDetails = $sareeReceipts->map(function ($sareeReceipt) {
            return [
                'saree_draping_date_morning' => $sareeReceipt->sareeReceipt->saree_draping_date_morning,
                'return_saree' => $sareeReceipt->sareeReceipt->return_saree,
                'name' => $sareeReceipt->name,
                'gotra' => $sareeReceipt->gotra,
            ];
        });
    } else {
        $sareeDetails = [];
    }

    // saree evening
    $sareeEveningReceipts = Receipt::with('sareeReceipt')
    ->where("cancelled", false)
    ->whereHas('sareeReceipt', function($query) use ($date) {
        $query->where('saree_draping_date_evening', $date);
    })
    ->get();

    if ($sareeEveningReceipts->isNotEmpty()) {
        $sareeEveningDetails = $sareeEveningReceipts->map(function ($sareeEveningReceipts) {
            return [
                'saree_draping_date_evening' => $sareeEveningReceipts->sareeReceipt->saree_draping_date_evening,
                'return_saree' => $sareeEveningReceipts->sareeReceipt->return_saree,
                'name' => $sareeEveningReceipts->name,
                'gotra' => $sareeEveningReceipts->gotra,
            ];
        });
    } else {
        $sareeEveningDetails = [];
    }
// saree evening end
    
    $uparaneReceipts = Receipt::with('uparaneReceipt')
    ->where("cancelled", false)
    ->whereHas('uparaneReceipt', function($query) use ($date) {
        $query->where('uparane_draping_date_morning', $date);
    })
    ->get();

    if ($uparaneReceipts->isNotEmpty()) {
    $uparaneDetails = $uparaneReceipts->map(function ($receipt) {
        return [
            'name' => $receipt->name,
            'gotra' => $receipt->gotra,
        ];
    });
    } else {
    $uparaneDetails = [];
    }

    // uparane evening start
    $uparaneEveningReceipts = Receipt::with('uparaneReceipt')
    ->where("cancelled", false)
    ->whereHas('uparaneReceipt', function($query) use ($date) {
        $query->where('uparane_draping_date_evening', $date);
    })
    ->get();

    if ($uparaneEveningReceipts->isNotEmpty()) {
    $uparaneEveningDetails = $uparaneEveningReceipts->map(function ($receipt) {
        return [
            'name' => $receipt->name,
            'gotra' => $receipt->gotra,
        ];
    });
    } else {
    $uparaneEveningDetails = [];
    }
    // uparane evening end
        // uparane and saree end
    
        if(!$receipts){
            return $this->sendError("receipts not found", ['error'=>['receipts not found']]);
        }
        
        $data = [
            'date' => $date,
            'totalCount' => $totalCount,
            'poojaTypeAndGotraCounts'=>$poojaTypeAndGotraCounts,
            'sareeDetails'=> $sareeDetails,
            'sareeEveningDetails'=> $sareeEveningDetails,
            'uparaneDetails'=> $uparaneDetails,
            'uparaneEveningDetails'=> $uparaneEveningDetails,
        ];

        // Render the Blade view to HTML
        $html = view('Reports.GotravaliReportNew.index', $data)->render();

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
            
            $date = \Carbon\Carbon::parse($date)->format('d/m/Y');
            
            // Set header HTML with dynamic values
            $headerHtml = '
            <div style="text-align: center;">
                <p style="margin: 0; padding: 0; font-size:17px;">श्री गणेश मंदिर संस्थान - गोत्रावळी ' . $date .' साठी.</p>
            </div>
            <p style="border: 1px solid black; width:100%; margin:0px; padding:0px; margin-bottom:5px;"></p>';
            
            // Set the header for each page
            $mpdf->SetHTMLHeader($headerHtml);
            
            // $footerHtml = '
            // <div style="border-top: 1px solid black; display: flex; justify-content: space-between; padding: 5px;">
            //     <p style="margin: 0; text-align: center; flex: 1;">Printed on ' . \Carbon\Carbon::now()->format('d-m-Y H:i') . '</p>
            //     <p style="margin: 0; text-align: right; flex: 1;">Page {PAGENO} of {nb}</p>
            // </div>';

            $footerHtml = '
            <div style="border-top: 1px solid black; margin-top: 5px;"></div> <!-- Line above the footer -->
            <div style="width: 100%; text-align: center; padding-top: 5px;">
                <span>Printed on ' . \Carbon\Carbon::now()->format('d/m/Y h:i A') . '</span>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                 <span>Page {PAGENO} of {nb}</span>
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


    public function gotravaliSummaryReportNew(Request $request)
    {
        $date = $request->input('date');

        $receipts = Receipt::with(['poojas.poojaType', 'receiptType'])
            ->where("cancelled", false)
            ->whereHas('receiptType', function ($query) use ($date) {
                $query->where("is_pooja", true);
            })
            ->whereHas('poojas', function ($query) use ($date) {
                // Apply the condition on pooja's date column
                if ($date) {
                    $query->where('date', $date); // Change 'date' to the actual column name in poojas table
                }
            });
        
        $receipts = $receipts->get();
      
        // $poojaTypeCounts = $receipts->flatMap(function ($receipt) use ($date) {
        //     return $receipt->poojas->filter(function ($puja) use ($date) {
        //         return !$date || $puja->date == $date;
        //     })->map(function ($puja) {
        //         return $puja->poojaType->pooja_type;
        //     });
        // })->countBy();
        $poojaTypeCountsByReceiptType = $receipts->flatMap(function ($receipt) use ($date) {
            return $receipt->poojas->filter(function ($puja) use ($date) {
                return !$date || $puja->date == $date;
            })->map(function ($puja) use ($receipt) {
                return [
                    'receiptType' => $receipt->receiptType->receipt_type, // Assuming 'receipt_type' is the field you want
                    'poojaType' => $puja->poojaType->pooja_type, // Extract the pooja type
                ];
            });
        })->groupBy('receiptType'); // Group by receiptType
        
    
        // $totalCount = $poojaTypeCounts->sum(); // Sum of all co

        if(!$receipts){
            return $this->sendError("receipts not found", ['error'=>['receipts not found']]);
        }
        
        $data = [
            'date' => $date,
            // 'totalCount' => $totalCount,
            'poojaTypeCountsByReceiptType' => $poojaTypeCountsByReceiptType,

        ];

        // Render the Blade view to HTML
        $html = view('Reports.GotravaliSummaryReportNew.index', $data)->render();

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
            
            $date = \Carbon\Carbon::parse($date)->format('d/m/Y');
            
            // Set header HTML with dynamic values
            $headerHtml = '
            <div style="text-align: center;">
                <p style="margin: 0; padding: 0; font-size:17px;">श्री गणेश मंदिर संस्थान - गोत्रावळी सारांश ' . $date .'.</p>
            </div>
            <p style="border: 1px solid black; width:100%; margin:0px; padding:0px; margin-bottom:5px;"></p>';
            
            // Set the header for each page
            $mpdf->SetHTMLHeader($headerHtml);
            
            // $footerHtml = '
            // <div style="border-top: 1px solid black; display: flex; justify-content: space-between; padding: 5px;">
            //     <p style="margin: 0; text-align: center; flex: 1;">Printed on ' . \Carbon\Carbon::now()->format('d-m-Y H:i') . '</p>
            //     <p style="margin: 0; text-align: right; flex: 1;">Page {PAGENO} of {nb}</p>
            // </div>';

            $footerHtml = '
            <div style="border-top: 1px solid black; margin-top: 5px;"></div> <!-- Line above the footer -->
            <div style="width: 100%; text-align: center; padding-top: 5px;">
                <span>Printed on ' . \Carbon\Carbon::now()->format('d/m/Y h:i A') . '</span>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                 <span>Page {PAGENO} of {nb}</span>
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

    
}