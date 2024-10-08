<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\PublicProvidentFund;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\PublicProvidentFundResource;

class PublicProvidentFundController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $pubilcProvidentFund = $user->profile->publicProvidentFund()->with('nominee')->get();
        return $this->sendResponse(['PublicProvidentFund'=>PublicProvidentFundResource::collection($pubilcProvidentFund)],'Public Provident fund details retrived Successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request):JsonResponse
    {
        if($request->hasFile('image')){
            $ppfFileNameWithExtention = $request->file('image')->getClientOriginalName();
            $ppfFilename = pathinfo($ppfFileNameWithExtention, PATHINFO_FILENAME);
            $ppfExtention = $request->file('image')->getClientOriginalExtension();
            $ppfFileNameToStore = $ppfFilename.'_'.time().'.'.$ppfExtention;
            $ppfPath = $request->file('image')->storeAs('public/PublicProvidentFund', $ppfFileNameToStore);
         }
         $user = Auth::user();
        $pubilcProvidentFund = new PublicProvidentFund();
        $pubilcProvidentFund->profile_id = $user->profile->id;
        $pubilcProvidentFund->bank_name = $request->input('bankName');
        $pubilcProvidentFund->ppf_account_no = $request->input('ppfAccountNo');
        $pubilcProvidentFund->branch = $request->input('branch');
        $pubilcProvidentFund->nature_of_holding = $request->input('natureOfHolding');
        $pubilcProvidentFund->joint_holder_name = $request->input('jointHolderName');
        $pubilcProvidentFund->joint_holder_pan = $request->input('jointHolderPan');
        $pubilcProvidentFund->additional_details = $request->input('additionalDetails');
        if($request->hasFile('image')){
            $pubilcProvidentFund->image = $ppfFileNameToStore;
         }  
        $pubilcProvidentFund->name = $request->input('name');
        $pubilcProvidentFund->mobile = $request->input('mobile');
        $pubilcProvidentFund->email = $request->input('email');

        $pubilcProvidentFund->save();

        if($request->has('nominees')) {
            $nominee_id = $request->input('nominees');
            if(is_string($nominee_id)) {
                $nominee_id = explode(',', $nominee_id);
            }
            if(is_array($nominee_id)) {
                $nominee_id = array_map('intval', $nominee_id);
                $pubilcProvidentFund->nominee()->attach($nominee_id);
            }
        }

        if($request->has('jointHolders')) {
            $joint_holder_id = $request->input('jointHolders');
            if(is_string($joint_holder_id)) {
                $joint_holder_id = explode(',', $joint_holder_id);
            }
            if(is_array($joint_holder_id)) {
                $joint_holder_id = array_map('intval', $joint_holder_id);
                $pubilcProvidentFund->jointHolder()->attach($joint_holder_id);
            }
        }

        // if($request->has('jointHolders')){
        //     $joint_holder_id = $request->input('jointHolders');
        //     $pubilcProvidentFund->jointHolder()->attach($joint_holder_id);
        // }

        return $this->sendResponse(['PublicProvidentFund'=> new PublicProvidentFundResource($pubilcProvidentFund)], 'Public Provident Fund details stored successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $pubilcProvidentFund = PublicProvidentFund::find($id);
        if(!$pubilcProvidentFund){
            return $this->sendError('Public Provident fund Not Found',['error'=>'Public Provident fund not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $pubilcProvidentFund->profile_id){
           return $this->sendError('Unauthorized', ['error'=>'You are not allowed to view this Public Provident fund']);
         }
         $pubilcProvidentFund->load('nominee');
        return $this->sendResponse(['PublicProvidentFund'=>new PublicProvidentFundResource($pubilcProvidentFund)], 'Public Provident Fund retrived successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $pubilcProvidentFund = PublicProvidentFund::find($id);
        if(!$pubilcProvidentFund){
            return $this->sendError('Public Provident fund Not Found',['error'=>'Public Provident fund not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $pubilcProvidentFund->profile_id){
           return $this->sendError('Unauthorized', ['error'=>'You are not allowed to view this Public Provident fund']);
         }
         
        if($request->hasFile('image')){
            if(!empty($pubilcProvidentFund->image) && Storage::exists('public/PublicProvidentFund/'.$pubilcProvidentFund->image)) {
                Storage::delete('public/PublicProvidentFund/'.$pubilcProvidentFund->image);
            }
            $ppfFileNameWithExtention = $request->file('image')->getClientOriginalName();
            $ppfFilename = pathinfo($ppfFileNameWithExtention, PATHINFO_FILENAME);
            $ppfExtention = $request->file('image')->getClientOriginalExtension();
            $ppfFileNameToStore = $ppfFilename.'_'.time().'.'.$ppfExtention;
            $ppfPath = $request->file('image')->storeAs('public/PublicProvidentFund', $ppfFileNameToStore);
         }

         $pubilcProvidentFund->profile_id = $user->profile->id;
         $pubilcProvidentFund->bank_name = $request->input('bankName');
         $pubilcProvidentFund->ppf_account_no = $request->input('ppfAccountNo');
         $pubilcProvidentFund->branch = $request->input('branch');
         $pubilcProvidentFund->nature_of_holding = $request->input('natureOfHolding');
         $pubilcProvidentFund->joint_holder_name = $request->input('jointHolderName');
         $pubilcProvidentFund->joint_holder_pan = $request->input('jointHolderPan');
         $pubilcProvidentFund->additional_details = $request->input('additionalDetails');
         if($request->hasFile('image')){
             $pubilcProvidentFund->image = $ppfFileNameToStore;
          }  
         $pubilcProvidentFund->name = $request->input('name');
         $pubilcProvidentFund->mobile = $request->input('mobile');
         $pubilcProvidentFund->email = $request->input('email');
 
         $pubilcProvidentFund->save();

         if($request->has('nominees')) {
            $nominee_id = is_string($request->input('nominees')) 
            ? explode(',', $request->input('nominees')) 
            : $request->input('nominees');
        $nominee_id = array_map('intval', $nominee_id);
            $pubilcProvidentFund->nominee()->sync($nominee_id);
        } else {
            $pubilcProvidentFund->nominee()->detach();
        }

        if($request->has('jointHolders')) {
            $joint_holder_ids = is_string($request->input('jointHolders')) 
            ? explode(',', $request->input('jointHolders')) 
            : $request->input('jointHolders');
        $joint_holder_ids = array_map('intval', $joint_holder_ids);
            $pubilcProvidentFund->jointHolder()->sync($joint_holder_ids);
        } else {
            $pubilcProvidentFund->jointHolder()->detach();
        }

        // if($request->has('jointHolders')) {
        //     $joint_holder_ids = $request->input('jointHolders');
        //     $pubilcProvidentFund->jointHolder()->sync($joint_holder_ids);
        // }else {
        //     $pubilcProvidentFund->jointHolder()->detach();
        // }

         return $this->sendResponse(['PublicProvidentFund'=> new PublicProvidentFundResource($pubilcProvidentFund)], 'Public Provident Fund details updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $pubilcProvidentFund = PublicProvidentFund::find($id);
        if(!$pubilcProvidentFund){
            return $this->sendError('Public Provident fund not found', ['error'=>'Public Provident fund not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $pubilcProvidentFund->profile_id){
            return $this->sendError('Unauthorized', ['error'=>'You are not allowed to access this Public Provident fund']);
        }

        if(!empty($pubilcProvidentFund->image) && Storage::exists('public/PublicProvidentFund/'.$pubilcProvidentFund->image)) {
            Storage::delete('public/PublicProvidentFund/'.$pubilcProvidentFund->image);
        }
        
        $pubilcProvidentFund->delete();

        return $this->sendResponse([], 'Public Provident fund deleted successfully');
    }
}