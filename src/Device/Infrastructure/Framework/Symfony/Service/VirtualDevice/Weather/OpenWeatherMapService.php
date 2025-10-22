<?php

namespace Marvin\Device\Infrastructure\Framework\Symfony\Service\VirtualDevice\Weather;

use Marvin\Device\Application\Service\VirtualDevice\Weather\WeatherData;
use Marvin\Device\Application\Service\VirtualDevice\Weather\WeatherServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class OpenWeatherMapService implements WeatherServiceInterface
{
    private const string BASE_URL = 'https://api.openweathermap.org/data/2.5';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger
    ) {}

    public function getCurrentWeather(string $location, string $apiProvider, string $apiKey): WeatherData
    {
        $this->logger->debug('Fetching current weather from OpenWeatherMap', [
            'location' => $location,
        ]);

        try {
            $response = $this->httpClient->request('GET', self::BASE_URL . '/weather', [
                'query' => [
                    'q' => $location,
                    'appid' => $apiKey,
                    'units' => 'metric',
                ],
            ]);

            $data = $response->toArray();

            return new WeatherData(
                temperature: $data['main']['temp'],
                humidity: $data['main']['humidity'],
                pressure: $data['main']['pressure'],
                windSpeed: $data['wind']['speed'] ?? 0,
                windDirection: $data['wind']['deg'] ?? 0,
                condition: $this->mapCondition($data['weather'][0]['main'] ?? 'unknown'),
                precipitation: $data['rain']['1h'] ?? null,
                cloudCover: $data['clouds']['all'] ?? null,
                uvIndex: null, // Nécessite un appel séparé à l'API UV
                timestamp: new \DateTimeImmutable('@' . $data['dt'])
            );

        } catch (\Throwable $e) {
            $this->logger->error('Failed to fetch weather data', [
                'location' => $location,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException("Failed to fetch weather data: {$e->getMessage()}", 0, $e);
        }
    }

    public function getForecast(string $location, string $apiProvider, string $apiKey, int $days = 3): array
    {
        $this->logger->debug('Fetching weather forecast from OpenWeatherMap', [
            'location' => $location,
            'days' => $days,
        ]);

        try {
            $response = $this->httpClient->request('GET', self::BASE_URL . '/forecast', [
                'query' => [
                    'q' => $location,
                    'appid' => $apiKey,
                    'units' => 'metric',
                    'cnt' => $days * 8, // 8 prévisions par jour (toutes les 3h)
                ],
            ]);

            $data = $response->toArray();
            $forecast = [];

            foreach ($data['list'] as $item) {
                $forecast[] = new WeatherData(
                    temperature: $item['main']['temp'],
                    humidity: $item['main']['humidity'],
                    pressure: $item['main']['pressure'],
                    windSpeed: $item['wind']['speed'] ?? 0,
                    windDirection: $item['wind']['deg'] ?? 0,
                    condition: $this->mapCondition($item['weather'][0]['main'] ?? 'unknown'),
                    precipitation: $item['rain']['3h'] ?? null,
                    cloudCover: $item['clouds']['all'] ?? null,
                    timestamp: new \DateTimeImmutable('@' . $item['dt'])
                );
            }

            return $forecast;

        } catch (\Throwable $e) {
            $this->logger->error('Failed to fetch weather forecast', [
                'location' => $location,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException("Failed to fetch weather forecast: {$e->getMessage()}", 0, $e);
        }
    }

    private function mapCondition(string $owmCondition): string
    {
        return match ($owmCondition) {
            'Clear' => 'sunny',
            'Clouds' => 'cloudy',
            'Rain', 'Drizzle' => 'rainy',
            'Snow' => 'snowy',
            'Thunderstorm' => 'stormy',
            'Mist', 'Fog' => 'foggy',
            default => 'unknown',
        };
    }
}
