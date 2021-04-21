<?php

namespace werk365\IdentityDocuments;

use Exception;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Intervention\Image\Image;
use ReflectionClass;
use werk365\IdentityDocuments\Filters\MergeFilter;
use werk365\IdentityDocuments\Interfaces\OCR;
use werk365\IdentityDocuments\Interfaces\FaceDetection;
use werk365\IdentityDocuments\Services\Google;

class IdentityImage
{
    public Image $image;
    public Exception $error;
    public string $text;
    public Image $face;
    private string $ocrService;
    private string $faceDetectionService;

    public function __construct(Image $image)
    {
        $this->setOcrService(Google::class);
        $this->setFaceDetectionService(Google::class);
        $this->setImage($image);
    }

    public function setOcrService(string $service){
        $class = new ReflectionClass($service);
        if (!$class->implementsInterface(OCR::class))
        {
            dd("not ocr");
        }
        $this->ocrService = $service;
    }

    public function setFaceDetectionService(string $service){
        $class = new ReflectionClass($service);
        if (!$class->implementsInterface(FaceDetection::class))
        {
            dd("not fd");
        }
        $this->faceDetectionService = $service;
    }

    public function setImage(Image $image)
    {
        $this->image = $image;
    }

    public function merge(IdentityImage $image): IdentityImage
    {
        return new IdentityImage($this->image->filter(new MergeFilter($image->image)));
    }

    public function ocr(): string
    {
        $service = new $this->ocrService();
        return $this->text = $service->ocr($this->image)->text;
    }

    public function face(): ?Image
    {
        $service = new $this->faceDetectionService();
        return $this->face = $service->detect($this);
    }
}
