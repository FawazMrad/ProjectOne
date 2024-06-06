<?php

namespace App\Http\Controllers;
use LanguageDetection\Language;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Helpers\TranslationHelper;
use Stichoza\GoogleTranslate\GoogleTranslate;


class EventController extends Controller
{
    public function storeStep1(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:100',
            'description' => 'required|string',
            'min_age' => 'required|integer|min:0',
            'is_paid' => 'required|boolean',
            'is_private' => 'required|boolean',
            'attendance_type' => 'required|in:invitation,ticket',
            'image' => 'nullable|string',
        ]);
        //translate the description
$validatedData=TranslationHelper::descriptionAndTranslatedDescription($validatedData);

        // Create the event
        $event = Event::create($validatedData);

        return response()->json(['message'=>__('event.completeStepOne'),'event_id' => $event->id,], 201);
    }
}
