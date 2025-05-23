<?php
// app/Services/BarcodeService.php

namespace App\Services;

use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;

class BarcodeService
{
    private $generator;
    private $svgGenerator;

    public function __construct()
    {
        $this->generator = new BarcodeGeneratorPNG();
        $this->svgGenerator = new BarcodeGeneratorSVG();
    }

    public function generateBarcodePNG($code, $type = BarcodeGeneratorPNG::TYPE_CODE_128, $widthFactor = 2, $height = 60)
    {
        return base64_encode($this->generator->getBarcode($code, $type, $widthFactor, $height));
    }

    public function generateBarcodeSVG($code, $type = BarcodeGeneratorSVG::TYPE_CODE_128, $widthFactor = 2, $height = 60)
    {
        return $this->svgGenerator->getBarcode($code, $type, $widthFactor, $height);
    }
}