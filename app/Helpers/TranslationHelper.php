<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationHelper
{
    public static function translateDescription($description)
    {
        $tr = new GoogleTranslate();
        $targetLanguage = app()->getLocale()=== 'ar' ? 'en' : 'ar';
        $tr->setSource(app()->getLocale());
        $tr->setTarget($targetLanguage);
        return  $tr->translate($description);

    }
    public static function descriptionAndTranslatedDescription($validatedData)
    {

        $description = $validatedData['description'];
        $translatedDescription = TranslationHelper::translateDescription($description);

        $tr = new GoogleTranslate();

        if (app()->getLocale() === 'ar') {
            $validatedData['description_ar'] = $description;
            $validatedData['description_en'] = $translatedDescription;
        } else {
            $validatedData['description_ar'] = $translatedDescription;
            $validatedData['description_en'] = $description;
        }

        // Remove the original description
        unset($validatedData['description']);
        return $validatedData;
    }

}
