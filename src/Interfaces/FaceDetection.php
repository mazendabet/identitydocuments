<?php

namespace werk365\IdentityDocuments\Interfaces;

use Intervention\Image\Image;
use werk365\IdentityDocuments\IdentityImage;

interface FaceDetection
{
    public function detect(IdentityImage $image): ?Image;
}
