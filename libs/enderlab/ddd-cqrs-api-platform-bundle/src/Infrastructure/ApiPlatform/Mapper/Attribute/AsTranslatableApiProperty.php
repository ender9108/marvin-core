<?php

namespace EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\Mapper\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class AsTranslatableApiProperty
{
    public const string TYPE_TRANSLATION_FILE = 'translation_file';

    public function __construct(
        public string $type = self::TYPE_TRANSLATION_FILE,
        public string $domain = 'messages'
    ) {
    }
}
