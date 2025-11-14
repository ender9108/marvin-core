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

namespace Marvin\System\Application\CommandHandler;

use EnderLab\MarvinManagerBundle\Messenger\Response\ManagerResponseCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ManagerResponseHandler
{
    public function __invoke(ManagerResponseCommand $response): void
    {
        dump($response->correlationId, $response->action);
    }
}
