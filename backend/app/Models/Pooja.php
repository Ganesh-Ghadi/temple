<?php

namespace App\Models;

use App\Models\Receipt;
use App\Models\PoojaType;
use Illuminate\Database\Eloquent\Model;

class Pooja extends Model
{
    public function receipt(){
        return $this->belongsTo(Receipt::class);
    }

    public function poojaType()
    {
        return $this->belongsTo(PoojaType::class, 'pooja_type_id');
    }
}