<?php

namespace App\Http\Controllers\Api;

use App\Models\NPS;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\NPSResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreNPSRequest;
use App\Http\Requests\UpdateNPSRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\BaseController;

class NPSController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $nps = $user->profile->nps()->with('nominee', 'jointHolder')->get();
    
        return $this->sendResponse(['NPS'=>NPSResource::collection($nps)], "NPS details retrived successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNPSRequest $request): JsonResponse
    {
        if($request->hasFile('image')){
            $npsFileNameWithExtention = $request->file('image')->getClientOriginalName();
            $npsFilename = pathinfo($npsFileNameWithExtention, PATHINFO_FILENAME);
            $npsExtention = $request->file('image')->getClientOriginalExtension();
            $npsFileNameToStore = $npsFilename.'_'.time().'.'.$npsExtention;
            $npsPath = $request->file('image')->storeAs('public/NPS', $npsFileNameToStore);
         }

        $user = Auth::user();
        $nps = new NPS();
        $nps->profile_id = $user->profile->id;
        $nps->permanent_retirement_account_no = $request->input('PRAN');
        $nps->nature_of_holding = $request->input('natureOfHolding');
        $nps->joint_holder_name = $request->input('jointHolderName');
        $nps->joint_holder_pan = $request->input('jointHolderPan');
        $nps->additional_details = $request->input('additionalDetails');
        if($request->hasFile('image')){
            $nps->image = $npsFileNameToStore;
         }      
        $nps->name = $request->input('name');
        $nps->mobile = $request->input('mobile');
        $nps->email = $request->input('email');
        $nps->save();

        if($request->has('nominees')) {
            $nominee_id = $request->input('nominees');
            if(is_string($nominee_id)) {
                $nominee_id = explode(',', $nominee_id);
            }
            if(is_array($nominee_id)) {
                $nominee_id = array_map('intval', $nominee_id);
                $nps->nominee()->attach($nominee_id);
            }
        }

        if($request->has('jointHolders')) {
            $joint_holder_id = $request->input('jointHolders');
            if(is_string($joint_holder_id)) {
                $joint_holder_id = explode(',', $joint_holder_id);
            }
            if(is_array($joint_holder_id)) {
                $joint_holder_id = array_map('intval', $joint_holder_id);
                $nps->jointHolder()->attach($joint_holder_id);
            }
        }

        return $this->sendResponse(['NPS'=> new NPSResource($nps)], 'NPS details stored successfully');


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $nps = NPS::find($id);
        if(!$nps){
            return $this->sendError('NPS Not Found',['error'=>'NPS not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $nps->profile_id){
           return $this->sendError('Unauthorized', ['error'=>'You are not allowed to view this NPS']);
         }
         $nps->load('nominee');
        return $this->sendResponse(['NPS'=>new NPSResource($nps)], 'NPS retrived successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNPSRequest $request, string $id): JsonResponse
    {
        $nps = NPS::find($id);
        if(!$nps){
            return $this->sendError('NPS Not Found',['error'=>'NPS details not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $nps->profile_id){
           return $this->sendError('Unauthorized', ['error'=>'You are not allowed to view this NPS']);
         }
         
        if($request->hasFile('image')){
            if(!empty($nps->image) && Storage::exists('public/NPS/'.$nps->image)) {
                Storage::delete('public/NPS/'.$nps->image);
            }
            $npsFileNameWithExtention = $request->file('image')->getClientOriginalName();
            $npsFilename = pathinfo($npsFileNameWithExtention, PATHINFO_FILENAME);
            $npsExtention = $request->file('image')->getClientOriginalExtension();
            $npsFileNameToStore = $npsFilename.'_'.time().'.'.$npsExtention;
            $npsPath = $request->file('image')->storeAs('public/NPS', $npsFileNameToStore);
         }

          $nps->permanent_retirement_account_no = $request->input('PRAN');
          $nps->nature_of_holding = $request->input('natureOfHolding');
          $nps->joint_holder_name = $request->input('jointHolderName');   //DELETE IT
          $nps->joint_holder_pan = $request->input('jointHolderPan');
          $nps->additional_details = $request->input('additionalDetails');
          if($request->hasFile('image')){
              $nps->image = $npsFileNameToStore;
           }      
          $nps->name = $request->input('name');
          $nps->mobile = $request->input('mobile');
          $nps->email = $request->input('email');
          $nps->save();

          if($request->has('nominees')) {
            $nominee_id = is_string($request->input('nominees')) 
            ? explode(',', $request->input('nominees')) 
            : $request->input('nominees');
        $nominee_id = array_map('intval', $nominee_id);
            $nps->nominee()->sync($nominee_id);
        } else {
            $nps->nominee()->detach();
        }

        if($request->has('jointHolders')) {
            $joint_holder_id = is_string($request->input('jointHolders')) 
            ? explode(',', $request->input('jointHolders')) 
            : $request->input('jointHolders');
        $joint_holder_id = array_map('intval', $joint_holder_id);
            $nps->jointHolder()->sync($joint_holder_id);
        } else {
            $nps->jointHolder()->detach();
        }

         return $this->sendResponse(['NPS'=> new NPSResource($nps)], 'NPS details updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $nps = NPS::find($id);
        if(!$nps){
            return $this->sendError('NPS not found', ['error'=>'NPS not found']);
        }
        $user = Auth::user();
        if($user->profile->id !== $nps->profile_id){
            return $this->sendError('Unauthorized', ['error'=>'You are not allowed to access this NPS']);
        }

        if(!empty($nps->image) && Storage::exists('public/NPS/'.$nps->image)) {
            Storage::delete('public/NPS/'.$nps->image);
        }

        $nps->delete();

        return $this->sendResponse([], 'NPS deleted successfully');
    }
}