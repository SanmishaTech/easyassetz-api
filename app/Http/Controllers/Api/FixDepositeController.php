<?php

namespace App\Http\Controllers\Api;

use App\Models\FixDeposite;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\FixDepositeResource;
use App\Http\Controllers\Api\BaseController;

class FixDepositeController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $fixDeposite = $user->profile->fixDeposite()->with('nominee','jointHolder')->get();
        return $this->sendResponse(['FixDeposite'=>FixDepositeResource::collection($fixDeposite)],'Fix deposite details retrived Successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        if($request->hasFile('image')){
            $fdFileNameWithExtention = $request->file('image')->getClientOriginalName();
            $fdFilename = pathinfo($fdFileNameWithExtention, PATHINFO_FILENAME);
            $fdExtention = $request->file('image')->getClientOriginalExtension();
            $fdFileNameToStore = $fdFilename.'_'.time().'.'.$fdExtention;
            $fdPath = $request->file('image')->storeAs('public/FixDeposite', $fdFileNameToStore);
         }

        $user = Auth::user();
        $fixDeposite = new FixDeposite();
        $fixDeposite->profile_id = $user->profile->id;
        $fixDeposit->fix_deposite_number = $request->input('fixDepositeNumber');
        $fixDeposit->bank_name = $request->input('bankName');
        $fixDeposit->branch_name = $request->input('branchName');
        $fixDeposit->maturity_date = $request->input('maturityDate');
        $fixDeposit->maturity_ammount = $request->input('maturityAmmount');
        $fixDeposit->holding_type = $request->input('holdingType');
        $fixDeposit->joint_holders_pan = $request->input('jointHoldersPan');
        $fixDeposit->additional_details = $request->input('additionalDetails');
        if($request->hasFile('image')){
            $fixDeposit->image = $fdFileNameToStore;
         } 
        $fixDeposit->save();
    
        if($request->has('nominees')){
            $nominee_id = $request->input('nominees');
            $fixDeposit->nominee()->attach($nominee_id);
        }

        if($request->has('jointHolders')){
            $joint_holder_id = $request->input('jointHolders');
            $fixDeposit->jointHolder()->attach($joint_holder_id);
        }

        return $this->sendResponse(['FixDeposite'=> new FixDepositeResource($fixDeposit)], 'Fix deposite details stored successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $fixDeposit = FixDeposite::find($id);
        if(!$fixDeposit){
            return $this->sendError('Fix Deposite Not Found',['error'=>'fix Deposite not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $fixDeposit->profile_id){
           return $this->sendError('Unauthorized', ['error'=>'You are not allowed to view this Fix Deposite']);
         }
         $fixDeposit->load('nominee','jointHolder');
        return $this->sendResponse(['FixDeposite'=>new FixDepositeResource($fixDeposit)], 'Fix Deposite retrived successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        if($request->hasFile('image')){
            $fdFileNameWithExtention = $request->file('image')->getClientOriginalName();
            $fdFilename = pathinfo($fdFileNameWithExtention, PATHINFO_FILENAME);
            $fdExtention = $request->file('image')->getClientOriginalExtension();
            $fdFileNameToStore = $fdFilename.'_'.time().'.'.$fdExtention;
            $fdPath = $request->file('image')->storeAs('public/FixDeposite', $fdFileNameToStore);
         }

         $fixDeposit = FixDeposite::find($id);
        if(!$fixDeposit){
            return $this->sendError('Fix deposite Not Found',['error'=>'fix deposite not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $fixDeposit->profile_id){
           return $this->sendError('Unauthorized', ['error'=>'You are not allowed to view this Fix Deposite']);
         }
       
         $fixDeposit->fix_deposite_number = $request->input('fixDepositeNumber');
         $fixDeposit->bank_name = $request->input('bankName');
         $fixDeposit->branch_name = $request->input('branchName');
         $fixDeposit->maturity_date = $request->input('maturityDate');
         $fixDeposit->maturity_ammount = $request->input('maturityAmmount');
         $fixDeposit->holding_type = $request->input('holdingType');
         $fixDeposit->joint_holders_pan = $request->input('jointHoldersPan');
         $fixDeposit->additional_details = $request->input('additionalDetails');
         if($request->hasFile('image')){
             $fixDeposit->image = $fdFileNameToStore;
          } 
         $fixDeposit->save();

         if($request->has('nominees')) {
            $nominee_ids = $request->input('nominees');
            $fixDeposit->nominee()->sync($nominee_ids);
        }else {
            $fixDeposit->nominee()->detach();
        }

        if($request->has('jointHolder')) {
            $joint_holder_id = $request->input('jointHolder');
            $fixDeposit->jointHolder()->sync($joint_holder_id);
        }else {
            $fixDeposit->jointHolder()->detach();
        }

         return $this->sendResponse(['FixDeposite'=> new FixDepositeResource($fixDeposit)], 'Fix deposite details updated successfully');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $fixDeposit = FixDeposite::find($id);
        if(!$fixDeposit){
            return $this->sendError('Fix deposite not found', ['error'=>'Fix deposite not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $fixDeposit->profile_id){
            return $this->sendError('Unauthorized', ['error'=>'You are not allowed to access this Fix deposite']);
        }
        $fixDeposit->delete();

        return $this->sendResponse([], 'Fix deposite deleted successfully');
    }
}

