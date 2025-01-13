<?php

namespace App\Models;

use App\Models\PoojaType;
use Illuminate\Database\Eloquent\Model;

class PoojaDate extends Model
{
    public function poojaType(){
        return $this->belongsTo(PoojaType::class, 'pooja_type_id');
    }
}