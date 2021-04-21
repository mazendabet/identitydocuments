<?php

namespace werk365\IdentityDocuments;

use Intervention\Image\Facades\Image as Img;
use Intervention\Image\Image;
use werk365\IdentityDocuments\Mrz\MrzParser;
use werk365\IdentityDocuments\Mrz\MrzSearcher;
use werk365\IdentityDocuments\Viz\VizParser;

class IdentityDocument
{
    public string $mrz;
    public ?Image $face;
    public array $parsedMrz;
    private IdentityImage $frontImage;
    private IdentityImage $backImage;
    private IdentityImage $mergedImage;
    private array $images;
    private VizParser $resolver;
    private MrzSearcher $searcher;
    private MrzParser $parser;
    private string $text = '';

    public function __construct($frontImage = null, $backImage = null)
    {
        if ($frontImage) {
            $this->addFrontImage($frontImage);
        }
        if ($backImage) {
            $this->addBackImage($backImage);
        }

        $this->searcher = new MrzSearcher();
        $this->parser = new MrzParser();
        $this->resolver = new VizParser();
    }

    public static function all($frontImage = null, $backImage = null)
    {
        $id = new IdentityDocument($frontImage, $backImage);
        $mrz = $id->getMrz();
        $parsed = $id->getParsedMrz();
        $face = $id->getFace();
        $faceB64 = 'data:image/jpg;base64,'.
            base64_encode(
                $face
                ->resize(null, 200, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->encode()
                ->encoded
            );
        $viz = $id->getViz();

        return [
            'mrz' => $mrz,
            'parsed' => $parsed,
            'viz' => $viz,
            'face' => $faceB64,
        ];
    }

    public function addFrontImage($image): IdentityDocument
    {
        $image = $this->createImage($image);
        $this->frontImage = new IdentityImage($image);
        $this->images[] = &$this->frontImage;

        return $this;
    }

    public function addBackImage($image): IdentityDocument
    {
        $image = $this->createImage($image);
        $this->backImage = new IdentityImage($image);
        $this->images[] = &$this->backImage;

        return $this;
    }

    private function createImage($file): ?Image
    {
        if (! is_file($file)) {
            return null;
        }
        file_get_contents($file->getRealPath());

        return Img::make($file);
    }

    private function mergeBackAndFrontImages()
    {
        if (! $this->frontImage || ! $this->backImage) {
            return false;
        }
        if (! $this->mergedImage = $this->frontImage->merge($this->backImage)) {
            return false;
        }
        $this->images = [&$this->mergedImage];

        return true;
    }

    private function mrz(): string
    {
        $this->mrz = '';
        foreach ($this->images as $image) {
            $this->text .= $image->ocr();
            if ($mrz = $this->searcher->search($image->text)) {
                $this->mrz = $mrz ?? '';
                break;
            }
        }

        return $this->mrz;
    }

    public function viz()
    {
        if (! $this->text) {
            return null;
        }

        return $this->viz = $this->resolver->match($this->parsedMrz, $this->mrz, $this->text);
    }

    public function getViz(): array
    {
        if (! isset($this->viz)) {
            $this->viz();
        }

        return $this->viz;
    }

    public function getMrz(): string
    {
        if (! isset($this->mrz)) {
            $this->mrz();
        }

        return $this->mrz;
    }

    public function getFace(): ?Image
    {
        if (! isset($this->face) || ! $this->face) {
            $this->face();
        }

        return $this->face;
    }

    private function face(): string
    {
        $this->face = null;
        foreach ($this->images as $image) {
            if ($face = $image->face()) {
                $this->face = $face ?? null;
                break;
            }
        }

        return $this->face;
    }

    public function getParsedMrz(): array
    {
        if (! isset($this->parsedMrz) || ! $this->mrz) {
            $this->parseMrz();
        }

        return $this->parsedMrz ?? [];
    }

    public function setMrz($mrz): IdentityDocument
    {
        $this->mrz = $mrz;

        return $this;
    }

    private function parseMrz(): void
    {
        $this->parsedMrz = $this->parser->parse($this->getMrz(), $this->searcher->type);
    }
}
