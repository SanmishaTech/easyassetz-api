<?php

namespace App\Http\Controllers\Api;

use App\Models\MutualFund;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\MutualFundResource;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StoreMutualFundRequest;
use App\Http\Requests\UpdateMutualFundRequest;

class MutualFundController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $mutualFund = $user->profile->mutualFund()->with('nominee')->get();
        return $this->sendResponse(['MutualFund'=>MutualFundResource::collection($mutualFund)],'Mutual Fund retrived Successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMutualFundRequest $request): JsonResponse
    {
        if($request->hasFile('image')){
            $mfFileNameWithExtention = $request->file('image')->getClientOriginalName();
            $mfFilename = pathinfo($mfFileNameWithExtention, PATHINFO_FILENAME);
            $mfExtention = $request->file('image')->getClientOriginalExtension();
            $mfFileNameToStore = $mfilename.'_'.time().'.'.$mfExtention;
            $mfPath = $request->file('image')->storeAs('public/MutualFund', $mfFileNameToStore);
         }

        $user = Auth::user();
        $mutualFund = new MutualFund();
        $mutualFund->profile_id = $user->profile->id;
        $mutualFund->fund_name = $request->input('fundName');
        $mutualFund->folio_number = $request->input('folioNumber');
        $mutualFund->number_of_units = $request->input('numberOfUnits');
        $mutualFund->nature_of_holding = $request->input('natureOfHolding');
        $mutualFund->joint_holder_name = $request->input('jointHolderName');
        $mutualFund->joint_holder_pan = $request->input('jointHolderPan');
        $mutualFund->additional_details = $request->input('additionalDetails');
        if($request->hasFile('image')){
            $mutualFund->image = $mfFileNameToStore;
         }
        $mutualFund->name = $request->input('name');
        $mutualFund->mobile = $request->input('mobile');
        $mutualFund->email = $request->input('email');
        $mutualFund->save();

        // if($request->has('nominees')){
        //     $nominee_id = $request->input('nominees');
        //     $mutualFund->nominee()->attach($nominee_id);
        // }

        if ($request->has('nominees')) {
            $nominee_id = $request->input('nominees');
            if (is_string($nominee_id)) {
                $nominee_id = explode(',', $nominee_id);
            }
            if (is_array($nominee_id)) {
                $nominee_id = array_map('intval', $nominee_id);
                $mutualFund->nominee()->attach($nominee_id);
            }
        }

        return $this->sendResponse(['MutualFund'=> new MutualFundResource($mutualFund)], 'Mutual Fund details stored successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $mutualFund = MutualFund::find($id);
        if(!$mutualFund){
            return $this->sendError('Mutual fund Not Found',['error'=>'Mutual Fund not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $mutualFund->profile_id){
           return $this->sendError('Unauthorized', ['error'=>'You are not allowed to view this Mutual Fund Detail']);
         }
         $mutualFund->load('nominee');
        return $this->sendResponse(['MutualFund'=>new MutualFundResource($mutualFund)], 'Mutual Fund Details retrived successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMutualFundRequest $request, string $id): JsonResponse
    {
        $mutualFund = MutualFund::find($id);
        if(!$mutualFund){
            return $this->sendError('Mutual Fund Detail Not Found',['error'=>'Mutual Fund Detail not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $mutualFund->profile_id){
           return $this->sendError('Unauthorized', ['error'=>'You are not allowed to view this Mutual Fund Detail']);
         }
         
        if($request->hasFile('image')){
            if(!empty($mutualFund->image) && Storage::exists('public/MutualFund/' . $mutualFund->image)) {
                Storage::delete('public/MutualFund/' . $mutualFund->image);
            }
            $mfFileNameWithExtention = $request->file('image')->getClientOriginalName();
            $mfFilename = pathinfo($mfFileNameWithExtention, PATHINFO_FILENAME);
            $mfExtention = $request->file('image')->getClientOriginalExtension();
            $mfFileNameToStore = $mfilename.'_'.time().'.'.$mfExtention;
            $mfPath = $request->file('image')->storeAs('public/MutualFund', $mfFileNameToStore);
         }

         $mutualFund->fund_name = $request->input('fundName');
         $mutualFund->folio_number = $request->input('folioNumber');
         $mutualFund->number_of_units = $request->input('numberOfUnits');
         $mutualFund->nature_of_holding = $request->input('natureOfHolding');
         $mutualFund->joint_holder_name = $request->input('jointHolderName');
         $mutualFund->joint_holder_pan = $request->input('jointHolderPan');
         $mutualFund->additional_details = $request->input('additionalDetails');
         if($request->hasFile('image')){
            $mutualFund->image = $mfFileNameToStore;
         }
         $mutualFund->name = $request->input('name');
         $mutualFund->mobile = $request->input('mobile');
         $mutualFund->email = $request->input('email');
         $mutualFund->save();

        //  if($request->has('nominees')) {
        //     $nominee_ids = $request->input('nominees');
        //     $mutualFund->nominee()->sync($nominee_ids);
        // }else {
        //     $mutualFund->nominee()->detach();
        // }

        if ($request->has('nominees')) {
            $nominee_id = is_string($request->input('nominees')) 
            ? explode(',', $request->input('nominees')) 
            : $request->input('nominees');
            $nominee_id = array_map('intval', $nominee_id);
            $mutualFund->nominee()->sync($nominee_id);
         } else {
             $mutualFund->nominee()->detach();
        }

         return $this->sendResponse(['MutualFund'=> new MutualFundResource($mutualFund)], 'Mutual Fund details updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $mutualFund = MutualFund::find($id);
        if(!$mutualFund){
            return $this->sendError('Mutual Fund not found', ['error'=>'Mutual Fund Details not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $mutualFund->profile_id){
            return $this->sendError('Unauthorized', ['error'=>'You are not allowed to access this Mutual Fund Details']);
        }

        if(!empty($mutualFund->image) && Storage::exists('public/MutualFund/' . $mutualFund->image)) {
            Storage::delete('public/MutualFund/' . $mutualFund->image);
        }
        $mutualFund->delete();

        return $this->sendResponse([], 'Mutual Fund Details deleted successfully');
    }
}