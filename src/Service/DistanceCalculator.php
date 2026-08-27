<?php

declare(strict_types=1);

namespace App\Service;

class DistanceCalculator
{
    public function calculate(float $lat_x, float $lon_x, float $lat_y, float $lon_y): float
    {
        return  sqrt(pow($lat_x - $lat_y, 2) + pow($lon_x - $lon_y, 2));
    }
}