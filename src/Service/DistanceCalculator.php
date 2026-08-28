<?php

declare(strict_types=1);

namespace App\Service;

class DistanceCalculator
{
    private const EARTH_RADIUS_KM = 6371.0;

    public function calculate(float $lat_x, float $lon_x, float $lat_y, float $lon_y): float
    {
        $latFrom = deg2rad($lat_x);
        $lonFrom = deg2rad($lon_x);
        $latTo = deg2rad($lat_y);
        $lonTo = deg2rad($lon_y);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2);
        $centralAngle = 2 * atan2(sqrt($angle), sqrt(1 - $angle));

        $result = self::EARTH_RADIUS_KM * $centralAngle;

        return round($result, 2);
    }
}
