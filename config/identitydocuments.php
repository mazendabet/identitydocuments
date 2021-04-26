<?php

use Werk365\IdentityDocuments\Services\Google;
use Werk365\IdentityDocuments\Services\Tesseract;

return [
    'ocrService' => Google::class,
    'faceDetectionService' => Google::class,
    'mergeImages' => false, // bool
];
