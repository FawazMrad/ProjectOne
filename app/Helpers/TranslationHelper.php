<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationHelper
{
    public static function descriptionAndTranslatedDescription($data)
    {
        $description = $data['description'];

        // Initialize GoogleTranslate
        $tr = new GoogleTranslate();

        // Translate to English
        $tr->setSource('auto'); // Automatically detect the source language
        $tr->setTarget('en');
        $description_en = $tr->translate($description);

        // Translate to Arabic
        $tr->setTarget('ar');
        $description_ar = $tr->translate($description);

        // Add translated descriptions to validatedData
        $data['description_en'] = $description_en;
        $data['description_ar'] = $description_ar;

        // Remove the original description
        unset($data['description']);

        return $data;
    }


}
