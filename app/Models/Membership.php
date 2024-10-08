<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Membership;
use App\Models\Beneficiary;
use App\Models\AssetAllocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Membership extends Model
{
    use HasFactory;

    public function setMembershipPaymentDateAttribute($value)
    {
        if($value){
            $this->attributes['membership_payment_date'] = Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
        }
    }

    public function getMembershipPaymentDateAttribute($value)
    {
        if($value){       
            return Carbon::parse($value)->format('m/d/Y');
        }
    }    


    
    public function nominee(){
    return $this->belongsToMany(Beneficiary::class,'membership_nominee');
    }
    

    public function assets(){
        return $this->hasMany(AssetAllocation::class, 'asset_id');
    }
}