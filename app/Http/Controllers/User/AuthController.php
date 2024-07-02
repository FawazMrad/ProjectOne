<?php

namespace App\Http\Controllers\User;

use App\Helpers\QR_CodeHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\throwException;

class AuthController extends Controller
{
    public function signUp(Request $request)
{
    try {
        $validateUser = Validator::make($request->all(),
            [
                'firstName' => 'required|string|max:50',
                'lastName' => 'required|string|max:50',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'address' => 'nullable',
                'phoneNumber' => 'required',
                'age' => 'required',
                'profilePic' => 'nullable',
                'googleId' => 'nullable|unique:users,google_id'
            ]);
        if ($validateUser->fails()) {
            return response()->json([
                'message' => __('validation.validationError'), 'errors' => $validateUser->errors()], 401);
        }
        $user = User::create([
            'first_name' => $request->input('firstName'),
            'last_name' => $request->input('lastName'),
            'email' => $request->input('email'),
            'google_id' => $request->input('googleId'),
            'password' => Hash::make($request->input('password')),
            'address' => $request->input('address'),
            'phone_number' => $request->input('phoneNumber'),
            'age' => $request->input('age'),
            'profile_pic' => $request->input('profilePic'),
        ]);
        $user->save();
        $qrData=[
            'id'=>$user->id,
            'user name'=>" $user->first_name  $user->last_name",
            'phone number'=>$user->phone_number,
            'user email'=>$user->email
        ];

       QR_CodeHelper::generateAndSaveQrCode($qrData,'User');

       return response()->json(['message'=>__('auth.signUpSuccess'),'user'=>$user,'token'=>$user->createToken("API TOKEN")->plainTextToken],201);
    }
    catch (\Throwable $th)
    {
     $user->delete();
     return response()->json(['message'=>$th->getMessage()],500);
    }
}
    public function signIn(Request $request){
        try {
            $user = User::where('email', $request->input('email'))->first();
            if (!$user || !Hash::check($request->input('password'), $user->password)) {
                return response()->json([
                    'message' => __('auth.failed')
                ], 401);
            }
            return response()->json([
                'message' => __('auth.signInSuccess'),
                'user' => $user,
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }

    }
    public function googleSignIn(Request $request)
    {
        try {
            $user = User::where('email', $request->input('email'))
                ->orWhere('google_id', $request->input('googleId'))
                ->first();
            return response()->json([
                'message' => __('auth.signInSuccess'),
                'user' => $user,
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function signOut(Request $request){
    try {
         $user = $request->user();
         $user->currentAccessToken()->delete();
        return response()->json([
            'message' => __('auth.signOutSuccess')
        ], 200);
    } catch (\Throwable $th) {
        return response()->json(['message' => $th->getMessage()], 500);
    }
}
}
