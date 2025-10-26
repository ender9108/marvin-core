<?php

namespace Marvin\Secret\Infrastructure\Scheduler;

use Marvin\Secret\Infrastructure\Scheduler\Message\RotateSecrets;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('secrets')]
final class SecretRotationSchedule implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return new Schedule()
            ->add(
            // Tous les jours à 3h du matin
                RecurringMessage::cron('0 3 * * *', new RotateSecrets())
            );
    }
}
