<?php

namespace DummyNamespace;

use Werk365\IdentityDocuments\Interfaces\FaceDetection;
use Intervention\Image\Image;
use Werk365\IdentityDocuments\IdentityImage;

class DummyClass implements FaceDetection
{
    public function detect(IdentityImage $image): ?Image
    {
        // TODO: Add face detection and return image of face
        return new Image();
    }
}