<?php

namespace App\System\Infrastructure\Symfony\Security\Voter;

use App\System\Infrastructure\ApiPlatform\Resource\UserResource;
use App\System\Infrastructure\Symfony\Security\SecurityUser;
use EnderLab\ToolsBundle\Service\ListTrait;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserPropertyVoter extends Voter
{
    use ListTrait;
    public const string CAN_UPDATE = 'CAN_PROPERTY_UPDATE';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, self::constantsToArray())) {
            return false;
        }

        if (!$subject instanceof UserResource) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof SecurityUser) {
            return false;
        }

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        return match ($attribute) {
            self::CAN_VIEW => $this->canRead($subject, $user),
            self::CAN_UPDATE => $this->canUpdate($subject, $user),
            default => false
        };
    }

    private function canRead(UserResource $subject, SecurityUser $user): bool
    {
        if (
            $this->security->isGranted('ROLE_USER') &&
            $subject->id === $user->getId()
        ) {
            return true;
        }

        return false;
    }

    private function canUpdate(UserResource $subject, SecurityUser $user): bool
    {
        if (
            $this->security->isGranted('ROLE_USER') &&
            $subject->id === $user->getId()
        ) {
            return true;
        }

        return false;
    }
}
