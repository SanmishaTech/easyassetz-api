<?php

namespace App\Models;

use App\Models\Profile;
use App\Models\LifeInsurance;
use App\Models\MotorInsurance;
use App\Models\OtherInsurance;
use App\Models\HealthInsurance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Beneficiary extends Model
{
    use HasFactory;

    public $table = 'beneficiaries';
    public $primaryKey = 'id';


    public function motorInsurance(){
        return $this->belongsToMany(MotorInsurance::class, 'motor_insurance_nominee');
    }
    
    public function lifeInsurance(){
        return $this->belongsToMany(LifeInsurance::class, 'life_insurance_nominee');
    }

    public function otherInsurance(){
        return $this->belongsToMany(OtherInsurance::class, 'other_insurance_nominee');
    }

    public function healthInsurance(){
        return $this->belongsToMany(HealthInsurance::class, 'health_insurance_nominee');
    }

    public function healthInsuranceMember(){
        return $this->belongsToMany(HealthInsurance::class, 'health_insurance_member');
    }

}