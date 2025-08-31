<?php

namespace EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\Mapper;

use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\Mapper\Attribute\AsTranslatableApiProperty;
use Exception;
use Psr\Cache\InvalidArgumentException;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractMapper
{
    public function __construct(
       protected readonly TranslatorInterface $translator,
       protected readonly CacheInterface $cache,
       protected readonly ParameterBagInterface $parameters
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    protected function translateDto(ApiResourceInterface $dto): ApiResourceInterface
    {
        $translatableProperties = $this->getTranslatableProperties($dto);

        foreach ($translatableProperties as $translatableProperty) {
            $dto->{$translatableProperty['property']} = match ($translatableProperty['type']) {
                AsTranslatableApiProperty::TYPE_TRANSLATION_FILE => $this->translator->trans(
                    $dto->{$translatableProperty['property']},
                    [],
                    $translatableProperty['domain']
                ),
                default => throw new Exception('Unknown translation type'),
            };
        }

        return $dto;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getTranslatableProperties(ApiResourceInterface $dto): array
    {
        $cacheKey = 'translatable_properties_' . strtolower(strtr(get_class($dto), ['\\' => '_']));

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($dto): array {
            $item->expiresAfter($this->parameters->get('ddd_cqrs_api_platform_cache_timeout'));

            $reflectionClass = new ReflectionClass($dto);
            $reflectionProperties = $reflectionClass->getProperties();
            $translatableProperties = [];

            foreach ($reflectionProperties as $reflectionProperty) {
                $attributes = $reflectionProperty->getAttributes(AsTranslatableApiProperty::class);

                if (count($attributes) > 0) {
                    /** @var AsTranslatableApiProperty $translatableAttribute */
                    $translatableAttribute = $attributes[0]->newInstance();

                    $translatableProperties[] = [
                        'property' => $reflectionProperty->getName(),
                        'type' => $translatableAttribute->type,
                        'domain' => $translatableAttribute->domain,
                    ];
                }
            }

            return $translatableProperties;
        });
    }
}
