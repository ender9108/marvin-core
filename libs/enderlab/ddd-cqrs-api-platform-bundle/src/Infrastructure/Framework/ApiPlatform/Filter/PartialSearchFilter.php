<?php

declare(strict_types=1);

namespace EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\Filter;

use ApiPlatform\Doctrine\Common\Filter\OpenApiFilterTrait;
use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\OpenApiParameterFilterInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use ReflectionClass;
use ReflectionException;

final class PartialSearchFilter implements FilterInterface, OpenApiParameterFilterInterface
{
    use OpenApiFilterTrait;
    /**
     * @throws ReflectionException
     */
    public function apply(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $parameter = $context['parameter'];
        $property = $parameter->getProperty();
        $reflectionClass = new ReflectionClass($resourceClass);
        $reflectionProperty = $reflectionClass->getProperty($property);
        $alias = $queryBuilder->getRootAliases()[0];
        if (is_subclass_of($reflectionProperty->getType()->getName(), ValueObjectInterface::class)) {
            $field = $alias.'.'.$property.'.value';
        } else {
            $field = $alias.'.'.$property;
        }
        $parameterName = $queryNameGenerator->generateParameterName($property);
        $values = $parameter->getValue();
        if (!is_iterable($values)) {
            $queryBuilder->setParameter($parameterName, '%'.strtolower($values).'%');
            $queryBuilder->{$context['whereClause'] ?? 'andWhere'}($queryBuilder->expr()->like(
                'LOWER('.$field.')',
                ':'.$parameterName
            ));
            return;
        }
        $likeExpressions = [];
        foreach ($values as $val) {
            $parameterName = $queryNameGenerator->generateParameterName($property);
            $likeExpressions[] = $queryBuilder->expr()->like(
                'LOWER('.$field.')',
                ':'.$parameterName
            );
            $queryBuilder->setParameter($parameterName, '%'.strtolower($val).'%');
        }
        $queryBuilder->{$context['whereClause'] ?? 'andWhere'}(
            $queryBuilder->expr()->orX(...$likeExpressions)
        );
    }
    public function getDescription(string $resourceClass): array
    {
        return [];
    }
}
