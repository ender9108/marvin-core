<?php

namespace EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Symfony\Security\Voter;

use ApiPlatform\Validator\Exception\ValidationException;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Symfony\Security\Attribute\FieldUpdatableBy;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use ReflectionClass;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\Cache\CacheInterface;

class FieldUpdatableByVoter extends Voter
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly ParameterBagInterface $parameters,
        private readonly Security $security
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === 'FIELDS_UPDATE'
            && is_array($subject)
            && count($subject) === 2
            && is_object($subject[0])
            && is_object($subject[1]);
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        [$newResource, $oldResource] = $subject;
        $violations = new ConstraintViolationList();
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            $violations->add(new ConstraintViolation(
                'The user is not logged in.',
                null,
                [],
                UserInterface::class,
                'user',
                $user
            ));
            throw new ValidationException($violations);
        }

        $rules = $this->getRules(get_class($newResource), $newResource);

        foreach ($rules as $propertyName => $fieldRules) {
            $newValue = $newResource->$propertyName ?? null;
            $oldValue = $oldResource ? ($oldResource->$propertyName ?? null) : null;

            if (
                $newValue instanceof ApiResourceInterface &&
                $oldValue instanceof ApiResourceInterface
            ) {
                $changed = $newValue->id !== $oldValue->id;
            } else {
                $changed = $newValue != $oldValue;
            }

            foreach ($fieldRules as $rule) {
                if ($changed && !$this->hasOneRole($rule->roles)) {
                    $violations->add(new ConstraintViolation(
                        sprintf($rule->message, $propertyName),
                        null,
                        [],
                        $newResource,
                        $propertyName,
                        $newValue
                    ));
                }
            }
        }

        if (count($violations) > 0) {
            throw new ValidationException($violations);
        }

        return true;
    }

    /**
     * @return array<string, FieldUpdatableBy[]>
     * @throws InvalidArgumentException
     */
    private function getRules(string $className, object $newEntity): array
    {
        $key = 'field_rules.'.strtr($className, ['\\' => '']);

        return $this->cache->get($key, function (CacheItemInterface $item) use ($newEntity) {
            $item->expiresAfter($this->parameters->get('ddd_cqrs_api_platform_cache_timeout'));

            $rules = [];
            $refClass = new ReflectionClass($newEntity);

            foreach ($refClass->getProperties() as $property) {
                $attrs = $property->getAttributes(FieldUpdatableBy::class);

                if ($attrs) {
                    $rules[$property->getName()] = array_map(
                        fn($attr) => $attr->newInstance(),
                        $attrs
                    );
                }
            }

            return $rules;
        });
    }

    private function hasOneRole(array $requiredRoles): bool
    {
        return array_any($requiredRoles, fn($role) => $this->security->isGranted($role));
    }
}
