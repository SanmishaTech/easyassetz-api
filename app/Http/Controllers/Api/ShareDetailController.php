<?php

namespace App\Http\Controllers\Api;

use App\Models\ShareDetail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ShareDetailResource;
use App\Http\Controllers\Api\BaseController;

class ShareDetailController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $shareDetail = $user->profile->shareDetail()->with('nominee','jointHolder')->get();
        return sendResponse(['ShareDetail'=>ShareDetailResource::collection($shareDetail)],'Share Detail retrived Successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        $shareDetail = new ShareDetail();
        $shareDetail->profile_id = $user->profile->id;
        $shareDetail->company_name = $request->input('companyName');
        $shareDetail->folio_number = $request->input('folioNumber');
        $shareDetail->no_of_shares = $request->input('noOfShares');
        $shareDetail->certificate_number = $request->input('certificateNumber');
        $shareDetail->distinguish_no_from = $request->input('distinguishNoFrom');
        $shareDetail->distinguish_no_to = $request->input('distinguishNoTo');
        $shareDetail->face_value = $request->input('faceValue');
        $shareDetail->nature_of_holding = $request->input('natureOfHolding');
        $shareDetail->additional_details = $request->input('additionalDetails');
        $shareDetail->image = $request->input('image');
        $shareDetail->name = $request->input('name');
        $shareDetail->mobile = $request->input('mobile');
        $shareDetail->email = $request->input('email');
        $shareDetail->save();

        if($request->has('nominees')){
            $nominee_id = $request->input('nominees');
            $shareDetail->nominee()->attach($nominee_id);
        }

        if($request->has('jointHolders')){
            $joint_holder_id = $request->input('jointHolders');
            $shareDetail->jointHolder()->attach($joint_holder_id);
        }

        return $this->sendResponse(['ShareDetail'=> new ShareDetailResource($shareDetail)], 'Share details stored successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $shareDetail = ShareDetail::find($id);
        if(!$shareDetail){
            return $this->sendError('Share Not Found',['error'=>'Share not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $shareDetail->profile_id){
           return $this->sendError('Unauthorized', ['error'=>'You are not allowed to view this Share Detail']);
         }
         $shareDetail->load('nominee','jointHolder');
        return $this->sendResponse(['ShareDetail'=>new LitigationResource($shareDetail)], 'Share Details retrived successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $shareDetail = ShareDetail::find($id);
        if(!$shareDetail){
            return $this->sendError('Share Detail Not Found',['error'=>'Share Detail not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $shareDetail->profile_id){
           return $this->sendError('Unauthorized', ['error'=>'You are not allowed to view this Share Detail']);
         }

         $shareDetail->company_name = $request->input('companyName');
         $shareDetail->folio_number = $request->input('folioNumber');
         $shareDetail->no_of_shares = $request->input('noOfShares');
         $shareDetail->certificate_number = $request->input('certificateNumber');
         $shareDetail->distinguish_no_from = $request->input('distinguishNoFrom');
         $shareDetail->distinguish_no_to = $request->input('distinguishNoTo');
         $shareDetail->face_value = $request->input('faceValue');
         $shareDetail->nature_of_holding = $request->input('natureOfHolding');
         $shareDetail->additional_details = $request->input('additionalDetails');
         $shareDetail->image = $request->input('image');
         $shareDetail->name = $request->input('name');
         $shareDetail->mobile = $request->input('mobile');
         $shareDetail->email = $request->input('email');
         $shareDetail->save();

         if($request->has('nominees')) {
            $nominee_ids = $request->input('nominees');
            $shareDetail->nominee()->sync($nominee_ids);
        }else {
            $shareDetail->nominee()->detach();
        }

        if($request->has('jointHolders')) {
            $joint_holder_id = $request->input('jointHolder');
            $shareDetail->jointHolder()->sync($joint_holder_id);
        }else {
            $shareDetail->jointHolder()->detach();
        }

         return $this->sendResponse(['ShareDetail'=> new ShareDetailResource($shareDetail)], 'Share details updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $shareDetail = ShareDetail::find($id);
        if(!$shareDetail){
            return $this->sendError('ShareDetail not found', ['error'=>'Share Details not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $shareDetail->profile_id){
            return $this->sendError('Unauthorized', ['error'=>'You are not allowed to access this Share Details']);
        }
        $shareDetail->delete();

        return $this->sendResponse([], 'Share Details deleted successfully');
    }
}
