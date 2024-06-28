<?php

namespace App\Helpers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QR_CodeHelper
{
    public static function generateAndSaveQrCode($data, $model)
    {
        $modelClass = "App\\Models\\$model";
        $findModel = $modelClass::find($data['id']);
        if (!$findModel) {
            return false;
        }
        $dataString = is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : (string)$data;
        $qrCode = QrCode::format('svg')->encoding('UTF-8')->size(400)->generate($dataString);
        $base64QrCode = base64_encode($qrCode);
        $findModel->qr_code = $base64QrCode;
        $findModel->save();
        return true;
    }
}
