<?php

namespace App\Models;

use App\Models\Receipt;
use Illuminate\Database\Eloquent\Model;

class HallReceipt extends Model
{
    
    public function receipt(){
        return $this->belongsTo(Receipt::class, 'receipt_id');
    }
    
}