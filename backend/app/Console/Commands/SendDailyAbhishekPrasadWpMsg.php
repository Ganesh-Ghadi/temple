<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Receipt;
use Illuminate\Console\Command;

class SendDailyAbhishekPrasadWpMsg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:SendDailyAbhishekPrasadWpMsg';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send daily whatsApp message for Abhishek Prasad';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // today morning saree
        $date = Carbon::today()->toDateString(); // 'YYYY-MM-DD' format

        $poojaDates = Receipt::with('pooja.poojaType.devta','receiptType')
            ->where("cancelled", false)
            ->whereHas('pooja', function($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->get();

        if ($poojaDates->isEmpty()) {
            \Log::info("No pooja receipts found for today ($date).");
            return;  // Exit early since no data to process
        } 
    
        foreach ($poojaDates as $receipt) {
            if($receipt->is_wa_no){
                Receipt::sendPrasadWhatsAppMessageDaily($receipt);
            }
            \Log::info('Receipt name:', ['name' => $receipt->name]);

        }
        \Log::info("All today's Prasad whatsApp messages processed.");
    }
}