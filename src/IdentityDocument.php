<?php

namespace werk365\IdentityDocuments;

use Intervention\Image\Image;
use Intervention\Image\Facades\Image as Img;

class IdentityDocument
{
    public string $mrz;
    public array $parsedMrz;
    private IdentityImage $frontImage;
    private IdentityImage $backImage;
    private IdentityImage $mergedImage;
    private array $images;
    private MrzSearcher $mrzSearcher;

    public function __construct($frontImage = null, $backImage = null){
        if($frontImage){
            $this->addFrontImage($frontImage);
        }
        if($backImage){
            $this->addBackImage($backImage);
        }

        $this->mrzSearcher = new MrzSearcher();
    }

    public function addFrontImage($image): void
    {
        $image = $this->createImage($image);
        $this->frontImage = new IdentityImage($image);
        $this->images[] = &$this->frontImage;
    }

    public function addBackImage($image): void
    {
        $image = $this->createImage($image);
        $this->backImage = new IdentityImage($image);
        $this->images[] = &$this->backImage;
    }

    private function createImage($file): ?Image
    {
        if(!is_file($file)){
            return null;
        }
        file_get_contents($file->getRealPath());
        return Img::make($file);
    }

    private function mergeBackAndFrontImages(){
        if(!$this->frontImage || !$this->backImage){
            return false;
        }
        if(!$this->mergedImage = $this->frontImage->merge($this->backImage)){
            return false;
        }
        $this->images = [&$this->mergedImage];
        return true;
    }

    private function mrz(): string
    {
        $this->mrz = "";
        foreach($this->images as $image){
            $image->ocr();
            if($mrz = $this->mrzSearcher->search($image->text)){
                $this->mrz = $mrz??"";
                break;
            }
        }

        return $this->mrz;
    }

    public function getMrz(): string
    {
        if(!isset($this->mrz)){
            $this->mrz();
        }
        return $this->mrz;
    }

    public function getParsedMrz(): array
    {
        if(!isset($this->parsedMrz) && $this->mrz){
            $this->parseMrz();
        }
        return $this->parsedMrz??[];
    }

    public function setMrz($mrz): IdentityDocument
    {
        $this->mrz = $mrz;
        return $this;
    }

    private function parseMrz(): void
    {
        $this->parsedMrz = $this->mrzSearcher->parse($this->getMrz());
    }
}
