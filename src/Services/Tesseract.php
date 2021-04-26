<?php

namespace Werk365\IdentityDocuments\Services;

use Intervention\Image\Image;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Werk365\IdentityDocuments\Interfaces\OCR;
use Werk365\IdentityDocuments\Responses\OcrResponse;

class Tesseract implements OCR
{
    public function __construct()
    {
    }

    public function ocr(Image $image): OcrResponse
    {
        $imagePath = sys_get_temp_dir().md5(microtime().random_bytes(5)).'.jpg';
        $image->save($imagePath);

        return new OcrResponse((new TesseractOCR($imagePath))
            ->run());
    }
}
