<?php
/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Shared\Presentation\Exception\EventListener;

use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(KernelEvents::EXCEPTION, priority: -1)]
final readonly class ExceptionListener
{
    public function __construct(
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        /*$exception = $event->getThrowable();
        $response = $this->exceptionMessageManager->jsonResponseFormat($exception);
        $event->setResponse($response);*/
    }
}
