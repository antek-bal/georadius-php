<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\DistanceCalculator;
use PHPUnit\Framework\TestCase;

class DistanceCalculatorTest extends TestCase
{
    private DistanceCalculator $distCalc;

    protected function setUp(): void
    {
        parent::setUp();
        $this->distCalc = new DistanceCalculator();
    }

    public function testCalculateDistanceBetweenDifferentCities(): void
    {
        $lat1 = 52.22977;
        $lon1 = 21.01178;

        $lat2 = 41.38879;
        $lon2 = 2.15899;

        $res = $this->distCalc->calculate($lat1, $lon1, $lat2, $lon2);

        $this->assertEqualsWithDelta(1863.0, $res, 2);
    }

    public function testCalculateDistanceSameCoordsReturnsZero(): void
    {
        $lat1 = 52.22977;
        $lon1 = 21.01178;

        $res = $this->distCalc->calculate($lat1, $lon1, $lat1, $lon1);

        $this->assertEquals(0.0, $res);
    }
}
