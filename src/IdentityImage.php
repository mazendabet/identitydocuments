<?php

namespace werk365\IdentityDocuments;

use Exception;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Intervention\Image\Image;

class IdentityImage
{
    public Image $image;
    public Exception $error;
    public string $text;
    private ImageAnnotatorClient $annotator;

    public function __construct(Image $image)
    {
        $this->setImage($image);
        $this->annotator = new ImageAnnotatorClient(
            ['credentials' => config('google_key')]
        );
    }

    public function setImage(Image $image)
    {
        $this->image = $image;
    }

    public function resize()
    {
    }

    public function rotate()
    {
    }

    public function merge(IdentityImage $image): IdentityImage
    {
        return $image;
    }

    public function ocr(): string
    {
        $response = $this->annotator->textDetection((string) $this->image->encode());
        $text = $response->getTextAnnotations();
        $this->text = $text[0]->getDescription();

        return $this->text;
    }
}
