<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\BeneficiaryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OtherFinancialAssetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $nominees = BeneficiaryResource::collection($this->nominee);

        return [
            'id' => $this->id,
            'profileId' => $this->profile_id,
            'bankServiceProvider' => $this->bank_service_provider,
            'folioNumber' => $this->folio_number,
            'branchName' => $this->branch_name,
            'natureOfHolding' => $this->nature_of_holding,
            'jointHolderName' => $this->joint_holder_name,
            'jointHolderPan' => $this->joint_holder_pan,
            'additionalDetails' => $this->additional_details,
            'image' => $this->image,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'nominees' => $nominees,
        ]; 
    
    }
}
