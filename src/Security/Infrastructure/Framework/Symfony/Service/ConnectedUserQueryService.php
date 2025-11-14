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

namespace Marvin\Security\Infrastructure\Framework\Symfony\Service;

use Marvin\Security\Infrastructure\Framework\Symfony\Security\SecurityUser;
use Marvin\Shared\Application\Service\Acl\ConnectedUserQueryServiceInterface;
use Marvin\Shared\Application\Service\Acl\Dto\ConnectedUserDto;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class ConnectedUserQueryService implements ConnectedUserQueryServiceInterface
{
    private SecurityUser|null $securityUser;

    public function __construct(
        Security $security
    ) {
        $this->securityUser = $security->getUser();
    }

    public function getConnectedUser(): ConnectedUserDto
    {
        return new ConnectedUserDto(
            $this->securityUser?->id,
            $this->securityUser?->getFullname(),
            $this->securityUser?->locale,
            $this->securityUser?->timezone,
            $this->securityUser?->theme,
            $this->securityUser?->roles
        );
    }
}
