<?php

namespace App\Models;

use App\Models\PoojaType;
use Illuminate\Database\Eloquent\Model;

class Devta extends Model
{
    public function poojaTypes(){
        return $this->hasMany(PoojaType::class, 'devta_id');
    }
}