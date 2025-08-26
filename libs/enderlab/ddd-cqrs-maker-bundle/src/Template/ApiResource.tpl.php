<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Processor\ApiToEntityStateProcessor;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Provider\EntityToApiStateProvider;
use App\<?= $domain ?>\Domain\Model\<?= $model_class_name ?>;
<?php if ($is_timestampable): ?>
use EnderLab\TimestampableBundle\Trait\ApiPlatform\ResourceTimestampableTrait;
<?php endif ?>
<?php if ($is_blameable): ?>
use EnderLab\BlameableBundle\Trait\ApiPlatform\ResourceBlameableTrait;
<?php endif ?>

#[ApiResource(
shortName: '<?= $var_short_name ?>',
operations: [
new GetCollection(),
new Get(),
],
routePrefix: '<?= strtolower($domain) ?>',
normalizationContext: ['skip_null_values' => false],
provider: EntityToApiStateProvider::class,
processor: ApiToEntityStateProcessor::class,
stateOptions: new Options(entityClass: <?= $model_class_name ?>::class)
)]
final class <?= $model_class_name ?>Resource implements ApiResourceInterface
{
<?php if ($is_timestampable): ?>
    use ResourceTimestampableTrait;
<?php endif ?>
<?php if ($is_blameable): ?>
    use ResourceBlameableTrait;
<?php endif ?>

#[ApiProperty(readable: true, writable: false, identifier: true)]
public ?int $id = null;

<?php
$mapPhpType = function (string $dbalType): string {
    return match ($dbalType) {
        'string', 'text', 'ascii_string' => 'string',
        'boolean' => 'bool',
        'integer', 'smallint', 'bigint' => 'int',
        'float', 'decimal' => 'float',
        'json', 'array', 'simple_array' => 'array',
        'datetime_immutable', 'date_immutable', 'datetime', 'date', 'time', 'time_immutable' => '\\DateTimeInterface',
        default => 'mixed',
    };
};
?>
<?php if (!empty($fields)): ?>
<?php foreach ($fields as $field): ?>
<?php $phpType = $mapPhpType($field['type']); ?>
public ?<?= $phpType ?> $<?= $field['name']; ?> = null;

<?php endforeach; ?>
<?php endif; ?>
}
