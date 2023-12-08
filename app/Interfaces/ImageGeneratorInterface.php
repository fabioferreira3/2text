<?php

namespace App\Interfaces;

interface ImageGeneratorInterface
{
    public function textToImage(array $params);
    public function imageToImage(array $params);
}
