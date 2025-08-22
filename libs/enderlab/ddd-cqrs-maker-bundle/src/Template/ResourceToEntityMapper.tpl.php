<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Application\Query\Bus\QueryBus;
use EnderLab\DddBundle\Ddd\Exception\Missing<?= $model_class_name ?>Exception;
use App\<?= $domain ?>\Domain\Model\<?= $model_class_name ?>;
use App\<?= $domain ?>\Infrastructure\ApiPlatform\Resource\<?= $model_class_name ?>Resource;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: <?= $model_class_name ?>Resource::class, to: <?= $model_class_name ?>::class)]
readonly class <?= $model_class_name ?>ResourceTo<?= $model_class_name ?>Mapper implements MapperInterface
{
public function __construct(
private QueryBus $queryBus,
private MicroMapperInterface $microMapper,
) {
}

/**
* @throws MissingModelException
* @throws ExceptionInterface
*/
public function load(object $from, string $toClass, array $context): object
{
$dto = $from;
assert($dto instanceof <?= $model_class_name ?>Resource);

$entity = $dto->id ?
    $this->queryBus->ask(new FindItemQuery($dto->id, <?= $model_class_name ?>::class)) :
    new <?= $model_class_name ?>()
;

if (!$entity) {
throw new MissingModelException($dto->id, <?= $model_class_name ?>::class);
}

return $entity;
}

public function populate(object $from, object $to, array $context): object
{
$dto = $from;
$entity = $to;

assert($dto instanceof <?= $model_class_name ?>Resource);
assert($entity instanceof <?= $model_class_name ?>);

<?php if (!empty($fields)): ?>
<?php foreach ($fields as $field): ?>
<?php $Method = str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $field['name']))); ?>
$entity->set<?= $Method; ?>($dto-><?= $field['name']; ?>);
<?php endforeach; ?>
<?php endif ?>

return $entity;
}
}
