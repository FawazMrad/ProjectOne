<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Exception;

class TranslationHelper
{
    public static function descriptionAndTranslatedDescription($data)
    {
        try {
            $description = $data['description'];
          $tr = new GoogleTranslate();

            $tr->setSource('auto');
            $tr->setTarget('en');
            $description_en = $tr->translate($description);


            $tr->setTarget('ar');
            $description_ar = $tr->translate($description);

            $data['description_en'] = $description_en;
            $data['description_ar'] = $description_ar;


            unset($data['description']);

            return $data;
        } catch (\Exception $e) {
            throw new Exception('Translation failed: ' . $e->getMessage());
        }


        }



}
