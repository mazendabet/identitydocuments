<?php

namespace werk365\IdentityDocuments\Interfaces;

use Intervention\Image\Image;
use werk365\IdentityDocuments\Responses\OcrResponse;

interface OCR
{
    public function ocr(Image $image): OcrResponse;
}
