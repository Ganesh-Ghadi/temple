<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Receipt;
use Illuminate\Console\Command;

class SendDailySareeWhatsAppMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:sareeWhatsAppMessage';
    

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send daily whatsApp message for Saree Receipt';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // today morning saree
        $date = Carbon::today()->toDateString(); // 'YYYY-MM-DD' format

        $sareeReceipts = Receipt::with('sareeReceipt')
            ->where("cancelled", false)
            ->whereHas('sareeReceipt', function($query) use ($date) {
                $query->whereDate('saree_draping_date_morning', $date);
            })
            ->get();

        if ($sareeReceipts->isEmpty()) {
            \Log::info("No saree receipts found for today ($date).");
            return;  // Exit early since no data to process
        } 
    
        foreach ($sareeReceipts as $receipt) {
            if($receipt->is_wa_no){
                Receipt::sendSareeWhatsAppMessageDaily($receipt);
            }
        }
        \Log::info("All today's saree whatsApp messages processed.");
    }
}