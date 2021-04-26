<?php

namespace Werk365\IdentityDocuments\Services;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Intervention\Image\Image;
use Werk365\IdentityDocuments\Interfaces\OCR;
use Werk365\IdentityDocuments\Responses\OcrResponse;
use thiagoalessio\TesseractOCR\TesseractOCR;

class Tesseract implements OCR
{

    public function __construct()
    {
    }

    public function ocr(Image $image): OcrResponse
    {
        $imagePath = sys_get_temp_dir().md5(microtime().random_bytes(5)).".jpg";
        $image->save($imagePath);
        return new OcrResponse((new TesseractOCR($imagePath))
            ->run());
    }
}
