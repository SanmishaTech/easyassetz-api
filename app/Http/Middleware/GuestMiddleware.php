<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Controllers\Api\BaseController;
use Symfony\Component\HttpFoundation\Response;

class GuestMiddleware extends BaseController
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       
        $user = Auth::user();
         if($user){
        //     $token = PersonalAccessToken::where('tokenable_id', $user->id)
        //     ->where('tokenable_type', get_class($user))
        //     ->first();    
        // return $this->sendResponse(['user'=>new UserResource($user), 'token'=>$token->plainTextToken], 'User login successfully.');           

          return $this->sendError('You are already logged-in.', ['message'=>'You are already logged-in']);
        }
        return $next($request);
    }
}
