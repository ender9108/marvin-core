<?php

namespace Marvin\Device\Application\Service\VirtualDevice\Weather;

interface WeatherServiceInterface
{
    public function getCurrentWeather(string $location, string $apiProvider, string $apiKey): WeatherData;

    public function getForecast(string $location, string $apiProvider, string $apiKey, int $days = 3): array;
}
