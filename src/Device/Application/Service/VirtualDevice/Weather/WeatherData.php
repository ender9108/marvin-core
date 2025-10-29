<?php

namespace Marvin\Device\Application\Service\VirtualDevice\Weather;

use DateTimeImmutable;
use DateTimeInterface;

final readonly class WeatherData
{
    public function __construct(
        public float $temperature,
        public float $humidity,
        public float $pressure,
        public float $windSpeed,
        public int $windDirection,
        /** sunny, cloudy, rainy, snowy, etc. */
        public string $condition,
        public ?float $precipitation = null,
        public ?int $cloudCover = null,
        public ?float $uvIndex = null,
        public ?DateTimeImmutable $timestamp = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'temperature' => $this->temperature,
            'humidity' => $this->humidity,
            'pressure' => $this->pressure,
            'wind_speed' => $this->windSpeed,
            'wind_direction' => $this->windDirection,
            'condition' => $this->condition,
            'precipitation' => $this->precipitation,
            'cloud_cover' => $this->cloudCover,
            'uv_index' => $this->uvIndex,
            'timestamp' => $this->timestamp?->format(DateTimeInterface::ATOM),
        ];
    }
}
