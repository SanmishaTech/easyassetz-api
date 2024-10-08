<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\LifeInsurance;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\LifeInsuranceResource;
use App\Http\Requests\StoreLifeInsuranceRequest;
use App\Http\Requests\UpdateLifeInsuranceRequest;

class LifeInsuranceController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $lifeInsurance = $user->profile->lifeInsurance()->with('nominee')->get();
       
        return $this->sendResponse(['LifeInsurances'=>LifeInsuranceResource::collection($lifeInsurance)], "Life Insurances retrived successfully");

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLifeInsuranceRequest $request): JsonResponse
    {

        if($request->hasFile('image')){
            $lifeFileNameWithExtention = $request->file('image')->getClientOriginalName();
            $lifeFilename = pathinfo($lifeFileNameWithExtention, PATHINFO_FILENAME);
            $lifeExtention = $request->file('image')->getClientOriginalExtension();
            $lifeFileNameToStore = $lifeFilename.'_'.time().'.'.$lifeExtention;
            $lifePath = $request->file('image')->storeAs('public/LifeInsurance', $lifeFileNameToStore);
         }

        $user = Auth::user();
        $lifeInsurance = new LifeInsurance();
        $lifeInsurance->profile_id = $user->profile->id;
        $lifeInsurance->company_name = $request->input('companyName');
        $lifeInsurance->insurance_type = $request->input('insuranceType');
        $lifeInsurance->policy_number = $request->input('policyNumber');
        $lifeInsurance->maturity_date = $request->input('maturityDate');
        $lifeInsurance->premium = $request->input('premium');
        $lifeInsurance->sum_insured = $request->input('sumInsured');
        $lifeInsurance->policy_holder_name = $request->input('policyHolderName');
        $lifeInsurance->relationship = $request->input('relationship');
        $lifeInsurance->previous_policy_number = $request->input('previousPolicyNumber');
        $lifeInsurance->additional_details = $request->input('additionalDetails');
        $lifeInsurance->mode_of_purchase = $request->input('modeOfPurchase');
        $lifeInsurance->broker_name = $request->input('brokerName');
        $lifeInsurance->contact_person = $request->input('contactPerson');
        $lifeInsurance->contact_number = $request->input('contactNumber');
        $lifeInsurance->email = $request->input('email');
        $lifeInsurance->registered_mobile = $request->input('registeredMobile');
        $lifeInsurance->registered_email = $request->input('registeredEmail');
        if($request->hasFile('image')){
            $lifeInsurance->image = $lifeFileNameToStore;
         }
        $lifeInsurance->save();

        if($request->has('nominees')){
            $nominee_id = is_string($request->input('nominees')) 
            ? explode(',', $request->input('nominees')) 
            : $request->input('nominees');

            $nominee_id = array_map('intval', $nominee_id);
            $lifeInsurance->nominee()->attach($nominee_id);
        }

        return $this->sendResponse(['LifeInsurance'=> new LifeInsuranceResource($lifeInsurance)], 'Life Insurance details stored successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $lifeInsurance = LifeInsurance::find($id);
        if(!$lifeInsurance){
            return $this->sendError('LifeInsurance Not Found',['error'=>'LifeInsurance not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $lifeInsurance->profile_id){
           return $this->sendError('Unauthorized', ['error'=>'You are not allowed to view this Life Insurance']);
         }
         $lifeInsurance->load('nominee');

        return $this->sendResponse(['LifeInsurance'=>new LifeInsuranceResource($lifeInsurance)], 'Life Insurance retrived successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLifeInsuranceRequest $request, string $id): JsonResponse
    {
       

        $lifeInsurance = LifeInsurance::find($id);
        if(!$lifeInsurance){
            return $this->sendError('lifeInsurance Not Found', ['error'=>'Life Insurance not found']);
        }

         $user = Auth::user();
         if($user->profile->id !== $lifeInsurance->profile_id){
            return $this->sendError('Unauthorized', ['error'=>'You are not allowed to update this Life Insurance']);
         }

         if($request->hasFile('image')){

            if (!empty($lifeInsurance->image) && Storage::exists('public/LifeInsurance/'.$lifeInsurance->image)) {
                Storage::delete('public/LifeInsurance/'.$lifeInsurance->image);
            }
            
            $lifeFileNameWithExtention = $request->file('image')->getClientOriginalName();
            $lifeFilename = pathinfo($lifeFileNameWithExtention, PATHINFO_FILENAME);
            $lifeExtention = $request->file('image')->getClientOriginalExtension();
            $lifeFileNameToStore = $lifeFilename.'_'.time().'.'.$lifeExtention;
            $lifePath = $request->file('image')->storeAs('public/LifeInsurance', $lifeFileNameToStore);
         }
         
          $lifeInsurance->company_name = $request->input('companyName');
          $lifeInsurance->insurance_type = $request->input('insuranceType');
          $lifeInsurance->policy_number = $request->input('policyNumber');
          $lifeInsurance->maturity_date = $request->input('maturityDate');
          $lifeInsurance->premium = $request->input('premium');
          $lifeInsurance->sum_insured = $request->input('sumInsured');
          $lifeInsurance->policy_holder_name = $request->input('policyHolderName');
          $lifeInsurance->relationship = $request->input('relationship');
          $lifeInsurance->previous_policy_number = $request->input('previousPolicyNumber');
          $lifeInsurance->additional_details = $request->input('additionalDetails');
          $lifeInsurance->mode_of_purchase = $request->input('modeOfPurchase');
          $lifeInsurance->broker_name = $request->input('brokerName');
          $lifeInsurance->contact_person = $request->input('contactPerson');
          $lifeInsurance->contact_number = $request->input('contactNumber');
          $lifeInsurance->email = $request->input('email');
          $lifeInsurance->registered_mobile = $request->input('registeredMobile');
          $lifeInsurance->registered_email = $request->input('registeredEmail');
          if($request->hasFile('image')){
            $lifeInsurance->image = $lifeFileNameToStore;
         }
          $lifeInsurance->save();

          if($request->has('nominees')) {
            $nominee_id = is_string($request->input('nominees')) 
            ? explode(',', $request->input('nominees')) 
            : $request->input('nominees');
             $nominee_id = array_map('intval', $nominee_id);
            $lifeInsurance->nominee()->sync($nominee_id);
        } else {
            $lifeInsurance->nominee()->detach();
        }
  
          return $this->sendResponse(['LifeInsurance'=> new LifeInsuranceResource($lifeInsurance)], 'Life Insurance details Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $lifeInsurance = LifeInsurance::find($id);
        if(!$lifeInsurance){
            return $this->sendError('Life Insurance not found', ['error'=>'Life Insurance not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $lifeInsurance->profile_id){
            return $this->sendError('Unauthorized', ['error'=>'You are not allowed to access this Life Insurance']);
        }

        if (!empty($lifeInsurance->image) && Storage::exists('public/LifeInsurance/'.$lifeInsurance->image)) {
            Storage::delete('public/LifeInsurance/'.$lifeInsurance->image);
        }
        $lifeInsurance->delete();

        return $this->sendResponse([], 'Life Insurance deleted successfully');
    }
}