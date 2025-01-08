<?php

namespace App\Models;

use App\Models\Devta;
use Illuminate\Database\Eloquent\Model;

class PoojaType extends Model
{
    public function devta(){
        return $this->belongsTo(Devta::class, 'devta_id');
    }
    
}