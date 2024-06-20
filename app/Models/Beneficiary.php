<?php

namespace App\Models;

use App\Models\Crypto;
use App\Models\Profile;
use App\Models\Membership;
use App\Models\ShareDetail;
use App\Models\LifeInsurance;
use App\Models\MotorInsurance;
use App\Models\OtherInsurance;
use App\Models\HealthInsurance;
use App\Models\GeneralInsurance;
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

  
    public function generalInsurance(){
        return $this->belongsToMany(GeneralInsurance::class, 'general_insurance_nominee');
    }
    
    public function membership(){
        return $this->belongsToMany(Membership::class, 'membership_nominee');
    }

    public function crypto(){
        return $this->belongsToMany(Crypto::class, 'crypto_nominee');
    }

    public function cryptoJointHolder(){
        return $this->belongsToMany(Crypto::class, 'crypto_joint_holder');
    }

    public function shareDetail(){
        return $this->belongsToMany(ShareDetail::class, 'share_detail_nominee');
    }

    public function shareDetailJointHolder(){
        return $this->belongsToMany(ShareDetail::class, 'share_detail_joint_holder');
    }

}
