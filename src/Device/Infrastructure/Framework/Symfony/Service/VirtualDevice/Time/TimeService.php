<?php

namespace Marvin\Device\Infrastructure\Framework\Symfony\Service\VirtualDevice\Time;

use DateInvalidTimeZoneException;
use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeZone;
use Marvin\Device\Application\Service\VirtualDevice\Time\SunTimes;
use Marvin\Device\Application\Service\VirtualDevice\Time\TimeServiceInterface;

final readonly class TimeService implements TimeServiceInterface
{
    public function getSunTimes(float $latitude, float $longitude, DateTimeImmutable $date): SunTimes
    {
        $timestamp = $date->getTimestamp();

        $sunInfo = $this->dateSunriseSunsetCalc(
            $timestamp,
            $latitude,
            $longitude
        );

        // Calcul du lever du soleil
        $sunriseTimestamp = $timestamp + $sunInfo['sunrise'] * 3600;
        $sunrise = new DateTimeImmutable()->setTimestamp((int) $sunriseTimestamp);

        // Calcul du coucher du soleil
        $sunsetTimestamp = $timestamp + $sunInfo['sunset'] * 3600;
        $sunset = new DateTimeImmutable()->setTimestamp((int) $sunsetTimestamp);

        // Midi solaire
        $solarNoonTimestamp = ($sunriseTimestamp + $sunsetTimestamp) / 2;
        $solarNoon = new DateTimeImmutable()->setTimestamp((int) $solarNoonTimestamp);

        // Durée du jour en secondes
        $dayLength = (int) ($sunsetTimestamp - $sunriseTimestamp);

        return new SunTimes(
            sunrise: $sunrise,
            sunset: $sunset,
            solarNoon: $solarNoon,
            dayLengthSeconds: $dayLength
        );
    }

    /**
     * @throws DateMalformedStringException
     * @throws DateInvalidTimeZoneException
     */
    public function getCurrentTime(string $timezone): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone($timezone));
    }

    private function dateSunriseSunsetCalc(int $timestamp, float $lat, float $lng): array
    {
        $julianDay = $timestamp / 86400 + 2440587.5;
        $n = $julianDay - 2451545.0;

        $meanAnomaly = fmod(357.5291 + 0.98560028 * $n, 360);
        $equationCenter = 1.9148 * sin(deg2rad($meanAnomaly)) +
            0.0200 * sin(deg2rad(2 * $meanAnomaly)) +
            0.0003 * sin(deg2rad(3 * $meanAnomaly));

        $eclipticLong = fmod($meanAnomaly + $equationCenter + 180 + 102.9372, 360);

        $solarTransit = 2451545.0 + $n + 0.0053 * sin(deg2rad($meanAnomaly)) -
            0.0069 * sin(deg2rad(2 * $eclipticLong));

        $declination = asin(sin(deg2rad($eclipticLong)) * sin(deg2rad(23.44)));

        $hourAngle = acos(
            (sin(deg2rad(-0.83)) - sin(deg2rad($lat)) * sin($declination)) /
            (cos(deg2rad($lat)) * cos($declination))
        );

        $sunrise = ($solarTransit - rad2deg($hourAngle) / 360 - 2440587.5) * 86400 - $timestamp;
        $sunset = ($solarTransit + rad2deg($hourAngle) / 360 - 2440587.5) * 86400 - $timestamp;

        return [
            'sunrise' => $sunrise / 3600, // en heures depuis minuit
            'sunset' => $sunset / 3600,
        ];
    }
}
