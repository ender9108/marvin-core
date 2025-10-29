<?php

namespace Marvin\Device\Application\Service\VirtualDevice\Time;

use DateTimeImmutable;

interface TimeServiceInterface
{
    public function getSunTimes(float $latitude, float $longitude, DateTimeImmutable $date): SunTimes;

    public function getCurrentTime(string $timezone): DateTimeImmutable;
}
