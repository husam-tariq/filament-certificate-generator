<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;

Route::get('certificate-generator/qr/{url}/{color}/{logo}', function (string $url,string $color,string $logo) {
    list($r, $g, $b) = sscanf($color, "%02x%02x%02x");

    $result = Builder::create()
        ->writer(new PngWriter())
        ->writerOptions([])
        ->data($url)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(ErrorCorrectionLevel::High)
        ->size(500)
        ->foregroundColor(new Color($r??0,$g??0, $b??0))
        ->margin(10)
        ->roundBlockSizeMode(RoundBlockSizeMode::Margin)



        ->validateResult(false);
    if($logo!="n"){
        $result= $result->logoPath(base64_decode($logo))
            ->logoResizeToWidth(150);
    }
    $result=$result->build();
    return response($result->getString(), 200)
        ->header('Content-Type',$result->getMimeType());
})->name('certificate-generator-qr')->where('url', '.*');
