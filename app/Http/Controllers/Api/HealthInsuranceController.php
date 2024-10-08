<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\HealthInsurance;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\HealthInsuranceResource;
use App\Http\Requests\StoreHealthInsurnaceRequest;
use App\Http\Requests\UpdateHealthInsurnaceRequest;

class HealthInsuranceController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $healthInsurance = $user->profile->healthInsurance()->with('nominee','familyMember')->get();
        return $this->sendResponse(['HealthInsurance'=>HealthInsuranceResource::collection($healthInsurance)],'Health Insurance retrived Successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHealthInsurnaceRequest $request): JsonResponse
    {
        if($request->hasFile('image')){
            $healthFileNameWithExtention = $request->file('image')->getClientOriginalName();
            $healthFilename = pathinfo($healthFileNameWithExtention, PATHINFO_FILENAME);
            $healthExtention = $request->file('image')->getClientOriginalExtension();
            $healthFileNameToStore = $healthFilename.'_'.time().'.'.$healthExtention;
            $healthPath = $request->file('image')->storeAs('public/HealthInsurance', $healthFileNameToStore);
         }

        $user = Auth::user();
        $healthInsurance = new HealthInsurance();
        $healthInsurance->profile_id = $user->profile->id;
        $healthInsurance->company_name = $request->input('companyName');
        $healthInsurance->insurance_type = $request->input('insuranceType');
        $healthInsurance->policy_number = $request->input('policyNumber');
        $healthInsurance->maturity_date = $request->input('maturityDate');
        $healthInsurance->premium = $request->input('premium');
        $healthInsurance->sum_insured = $request->input('sumInsured');
        $healthInsurance->policy_holder_name = $request->input('policyHolderName');
        $healthInsurance->additional_details = $request->input('additionalDetails');
        $healthInsurance->mode_of_purchase = $request->input('modeOfPurchase');
        $healthInsurance->broker_name = $request->input('brokerName');
        $healthInsurance->contact_person = $request->input('contactPerson');
        $healthInsurance->contact_number = $request->input('contactNumber');
        $healthInsurance->email = $request->input('email');
        $healthInsurance->registered_mobile = $request->input('registeredMobile');
        $healthInsurance->registered_email = $request->input('registeredEmail');
        if($request->hasFile('image')){
            $healthInsurance->image = $healthFileNameToStore;
         }
        $healthInsurance->save();

        if($request->has('nominees')) {
            $nominee_id = $request->input('nominees');
            if(is_string($nominee_id)) {
                $nominee_id = explode(',', $nominee_id);
            }
            if(is_array($nominee_id)) {
                $nominee_id = array_map('intval', $nominee_id);
                $healthInsurance->nominee()->attach($nominee_id);
            }
        }

        if($request->has('familyMembers')) {
            $family_members_id = $request->input('familyMembers');
            if(is_string($family_members_id)) {
                $family_members_id = explode(',', $family_members_id);
            }
            if(is_array($family_members_id)) {
                $family_members_id = array_map('intval', $family_members_id);
                $healthInsurance->familyMember()->attach($family_members_id);
            }
        }

        // if($request->has('familyMembers')){
        //     $family_members_id = $request->input('familyMembers');
        //     $healthInsurance->familyMember()->attach($family_members_id);
        // }

        return $this->sendResponse(['HealthInsurance'=> new HealthInsuranceResource($healthInsurance)], 'Health Insurance details stored successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $healthInsurance = HealthInsurance::find($id);
        if(!$healthInsurance){
            return $this->sendError('Health Insurance Not Found',['error'=>'Health Insurance not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $healthInsurance->profile_id){
           return $this->sendError('Unauthorized', ['error'=>'You are not allowed to view this Health Insurance']);
         }
         $healthInsurance->load('nominee','familyMember');

        return $this->sendResponse(['HealthInsurance'=>new HealthInsuranceResource($healthInsurance)], 'Health Insurance retrived successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHealthInsurnaceRequest $request, string $id): JsonResponse
    {
        $healthInsurance = HealthInsurance::find($id);
        if(!$healthInsurance){
            return $this->sendError('Health Insurance Not Found',['error'=>'Health Insurance not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $healthInsurance->profile_id){
           return $this->sendError('Unauthorized', ['error'=>'You are not allowed to view this Health Insurance']);
         }
         
        if($request->hasFile('image')){
            if(!empty($healthInsurance->image) && Storage::exists('public/HealthInsurance/'.$healthInsurance->image)) {
                Storage::delete('public/HealthInsurance/'.$healthInsurance->image);
            }
            $healthFileNameWithExtention = $request->file('image')->getClientOriginalName();
            $healthFilename = pathinfo($healthFileNameWithExtention, PATHINFO_FILENAME);
            $healthExtention = $request->file('image')->getClientOriginalExtension();
            $healthFileNameToStore = $healthFilename.'_'.time().'.'.$healthExtention;
            $healthPath = $request->file('image')->storeAs('public/HealthInsurance', $healthFileNameToStore);
         }

         $healthInsurance->company_name = $request->input('companyName');
         $healthInsurance->insurance_type = $request->input('insuranceType');
         $healthInsurance->policy_number = $request->input('policyNumber');
         $healthInsurance->maturity_date = $request->input('maturityDate');
         $healthInsurance->premium = $request->input('premium');
         $healthInsurance->sum_insured = $request->input('sumInsured');
         $healthInsurance->policy_holder_name = $request->input('policyHolderName');
         $healthInsurance->additional_details = $request->input('additionalDetails');
         $healthInsurance->mode_of_purchase = $request->input('modeOfPurchase');
         $healthInsurance->broker_name = $request->input('brokerName');
         $healthInsurance->contact_person = $request->input('contactPerson');
         $healthInsurance->contact_number = $request->input('contactNumber');
         $healthInsurance->email = $request->input('email');
         $healthInsurance->registered_mobile = $request->input('registeredMobile');
         $healthInsurance->registered_email = $request->input('registeredEmail');
         if($request->hasFile('image')){
            $healthInsurance->image = $healthFileNameToStore;
         }
         $healthInsurance->save();
          
         if($request->has('nominees')) {
            $nominee_id = is_string($request->input('nominees')) 
            ? explode(',', $request->input('nominees')) 
            : $request->input('nominees');
            $nominee_id = array_map('intval', $nominee_id);
            $healthInsurance->nominee()->sync($nominee_id);
        } else {
            $healthInsurance->nominee()->detach();
        }


        // if($request->has('familyMembers')) {
        //     $family_members_id = $request->input('familyMembers');
        //     $healthInsurance->familyMember()->sync($family_members_id);
        // }else {
        //     $healthInsurance->familyMember()->detach();
        // }

        if($request->has('familyMembers')) {
            $family_members_id = is_string($request->input('familyMembers')) 
            ? explode(',', $request->input('familyMembers')) 
            : $request->input('familyMembers');
            $family_members_id = array_map('intval', $family_members_id);
            $healthInsurance->familyMember()->sync($family_members_id);
        } else {
            $healthInsurance->familyMember()->detach();
        }

        return $this->sendResponse(['HealthInsurance'=>new HealthInsuranceResource($healthInsurance)], 'Health Insurance updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $healthInsurance = HealthInsurance::find($id);
        if(!$healthInsurance){
            return $this->sendError('Health Insurance not found', ['error'=>'Health Insurance not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $healthInsurance->profile_id){
            return $this->sendError('Unauthorized', ['error'=>'You are not allowed to access this Health Insurance']);
        }

        if(!empty($healthInsurance->image) && Storage::exists('public/HealthInsurance/'.$healthInsurance->image)) {
            Storage::delete('public/HealthInsurance/'.$healthInsurance->image);
        }
        
        $healthInsurance->delete();

        return $this->sendResponse([], 'Health Insurance deleted successfully');
    }
}