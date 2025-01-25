<?php

namespace App\Models;

use App\Models\Receipt;
use Illuminate\Database\Eloquent\Model;

class ReceiptType extends Model
{
    public function receipts()
{
    return $this->hasMany(Receipt::class);
}
}