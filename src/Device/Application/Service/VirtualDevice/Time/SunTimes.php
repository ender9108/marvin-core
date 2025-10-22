<?php

namespace Marvin\Device\Application\Service\VirtualDevice\Time;

use DateTimeImmutable;
use DateTimeInterface;

final readonly class SunTimes
{
    public function __construct(
        public DateTimeImmutable $sunrise,
        public DateTimeImmutable $sunset,
        public DateTimeImmutable $solarNoon,
        public int $dayLengthSeconds
    ) {}

    public function isDay(DateTimeImmutable $time = null): bool
    {
        $time = $time ?? new DateTimeImmutable();
        return $time >= $this->sunrise && $time <= $this->sunset;
    }

    public function isNight(DateTimeImmutable $time): bool
    {
        return !$this->isDay($time);
    }

    public function toArray(): array
    {
        return [
            'sunrise' => $this->sunrise->format(DateTimeInterface::ATOM),
            'sunset' => $this->sunset->format(DateTimeInterface::ATOM),
            'solar_noon' => $this->solarNoon->format(DateTimeInterface::ATOM),
            'day_length' => $this->dayLengthSeconds,
            'is_day' => $this->isDay(),
        ];
    }
}

