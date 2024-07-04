<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

class PreferenceController
{
public function getPreferences(Request $request){
    $user=$request->user();
    $userPreferences=$user->preference;
    return response()->json(['Preferences'=>$userPreferences],200);
}
public function adjustPreferences(Request $request){
    $user=$request->user();
    $attributeName=$request->input('attribute');
    $attributeValue=$request->input('attributeValue');
    $userPreferences=$user->preference;
    $userPreferences->$attributeName=$attributeValue;
    $userPreferences->save();
    return response()->json(['message'=>__('auth.preferencesAdjustSuccessfully')],200);
}
}
