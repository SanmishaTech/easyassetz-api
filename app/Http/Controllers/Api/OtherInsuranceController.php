<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\OtherInsurance;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\OtherInsuranceResource;
use App\Http\Requests\StoreOtherInsuranceRequest;
use App\Http\Requests\UpdateOtherInsuranceRequest;

class OtherInsuranceController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $otherInsurance = $user->profile->otherInsurance()->with('nominee')->get();
    
        return $this->sendResponse(['OtherInsurance'=>OtherInsuranceResource::collection($otherInsurance)], "Other Insurances retrived successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOtherInsuranceRequest $request): JsonResponse
    {

        if($request->hasFile('image')){
            $otherFileNameWithExtention = $request->file('image')->getClientOriginalName();
            $otherFilename = pathinfo($otherFileNameWithExtention, PATHINFO_FILENAME);
            $otherExtention = $request->file('image')->getClientOriginalExtension();
            $otherFileNameToStore = $otherFilename.'_'.time().'.'.$otherExtention;
            $otherPath = $request->file('image')->storeAs('public/OtherInsurance', $otherFileNameToStore);
         }

        $user = Auth::user();
        $otherInsurance = new OtherInsurance();
        $otherInsurance->profile_id = $user->profile->id;
        $otherInsurance->company_name = $request->input('companyName');
        $otherInsurance->insurance_type = $request->input('insuranceType');
        $otherInsurance->policy_number = $request->input('policyNumber');
        $otherInsurance->maturity_date = $request->input('maturityDate');
        $otherInsurance->premium = $request->input('premium');
        $otherInsurance->sum_insured = $request->input('sumInsured');
        $otherInsurance->policy_holder_name = $request->input('policyHolderName');
        $otherInsurance->additional_details = $request->input('additionalDetails');
        $otherInsurance->mode_of_purchase = $request->input('modeOfPurchase');
        $otherInsurance->broker_name = $request->input('brokerName');
        $otherInsurance->contact_person = $request->input('contactPerson');
        $otherInsurance->contact_number = $request->input('contactNumber');
        $otherInsurance->email = $request->input('email');
        $otherInsurance->registered_mobile = $request->input('registeredMobile');
        $otherInsurance->registered_email = $request->input('registeredEmail');
        if($request->hasFile('image')){
            $otherInsurance->image = $otherFileNameToStore;
         }
        $otherInsurance->save();

        if($request->has('nominees')) {
            $nominee_id = $request->input('nominees');
            if(is_string($nominee_id)) {
                $nominee_id = explode(',', $nominee_id);
            }
            if(is_array($nominee_id)) {
                $nominee_id = array_map('intval', $nominee_id);
                $otherInsurance->nominee()->attach($nominee_id);
            }
        }

        return $this->sendResponse(['OtherInsurance'=> new OtherInsuranceResource($otherInsurance)], 'other Insurance details stored successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $otherInsurance = OtherInsurance::find($id);
        if(!$otherInsurance){
            return $this->sendError('Other Insurance Not Found',['error'=>'Other Insurance not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $otherInsurance->profile_id){
           return $this->sendError('Unauthorized', ['error'=>'You are not allowed to view this Other Insurance']);
         }
         $otherInsurance->load('nominee');

        return $this->sendResponse(['OtherInsurance'=>new OtherInsuranceResource($otherInsurance)], 'Other Insurance retrived successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOtherInsuranceRequest $request, string $id): JsonResponse
    {

        $otherInsurance = OtherInsurance::find($id);
        if(!$otherInsurance){
            return $this->sendError('Other Insurance Not Found',['error'=>'Other Insurance not found']);
        }
        
        $user = Auth::user();
        if($user->profile->id !== $otherInsurance->profile_id){
           return $this->sendError('Unauthorized', ['error'=>'You are not allowed to view this Other Insurance']);
         }
         
        if($request->hasFile('image')){
            if (!empty($otherInsurance->image) && Storage::exists('public/OtherInsurance/'.$otherInsurance->image)) {
                Storage::delete('public/OtherInsurance/'.$otherInsurance->image);
               }
            $otherFileNameWithExtention = $request->file('image')->getClientOriginalName();
            $otherFilename = pathinfo($otherFileNameWithExtention, PATHINFO_FILENAME);
            $otherExtention = $request->file('image')->getClientOriginalExtension();
            $otherFileNameToStore = $otherFilename.'_'.time().'.'.$otherExtention;
            $otherPath = $request->file('image')->storeAs('public/OtherInsurance', $otherFileNameToStore);
         }

         $otherInsurance->company_name = $request->input('companyName');
         $otherInsurance->insurance_type = $request->input('insuranceType');
         $otherInsurance->policy_number = $request->input('policyNumber');
         $otherInsurance->maturity_date = $request->input('maturityDate');
         $otherInsurance->premium = $request->input('premium');
         $otherInsurance->sum_insured = $request->input('sumInsured');
         $otherInsurance->policy_holder_name = $request->input('policyHolderName');
         $otherInsurance->additional_details = $request->input('additionalDetails');
         $otherInsurance->mode_of_purchase = $request->input('modeOfPurchase');
         $otherInsurance->broker_name = $request->input('brokerName');
         $otherInsurance->contact_person = $request->input('contactPerson');
         $otherInsurance->contact_number = $request->input('contactNumber');
         $otherInsurance->email = $request->input('email');
         $otherInsurance->registered_mobile = $request->input('registeredMobile');
         $otherInsurance->registered_email = $request->input('registeredEmail');
         if($request->hasFile('image')){
            $otherInsurance->image = $otherFileNameToStore;
         }
         $otherInsurance->save();

         if($request->has('nominees')) {
            $nominee_id = is_string($request->input('nominees')) 
            ? explode(',', $request->input('nominees')) 
            : $request->input('nominees');
        $nominee_id = array_map('intval', $nominee_id);
            $otherInsurance->nominee()->sync($nominee_id);
        } else {
            $otherInsurance->nominee()->detach();
        }
  
          return $this->sendResponse(['OtherInsurance'=> new OtherInsuranceResource($otherInsurance)], 'Other Insurance details Updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $otherInsurance = OtherInsurance::find($id);
        if(!$otherInsurance){
            return $this->sendError('Other Insurance not found', ['error'=>'Other Insurance not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $otherInsurance->profile_id){
            return $this->sendError('Unauthorized', ['error'=>'You are not allowed to access this Other Insurance']);
        }

        if (!empty($otherInsurance->image) && Storage::exists('public/OtherInsurance/'.$otherInsurance->image)) {
            Storage::delete('public/OtherInsurance/'.$otherInsurance->image);
           }

        $otherInsurance->delete();

        return $this->sendResponse([], 'Other Insurance deleted successfully');
    }
}