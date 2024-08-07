<?php

namespace App\Models;

use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BrokingAccount extends Model
{
    use HasFactory;

    public function nominee(){
        return $this->belongsToMany(Beneficiary::class,'broking_account_nominee');
     }
}
