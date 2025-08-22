<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

use App\<?= $domain ?>\Domain\Model\<?= $model_class_name ?>;
use App\<?= $domain ?>\Infrastructure\ApiPlatform\Resource\<?= $model_class_name ?>Resource;
use EnderLab\DddCqrsApiPlatformBundle\Mapper\AbstractMapper;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsMapper(from: <?= $model_class_name ?>::class, to: <?= $model_class_name ?>Resource::class)]
class <?= $model_class_name ?>To<?= $model_class_name ?>ResourceMapper extends AbstractMapper implements MapperInterface
{
public function __construct(
private readonly MicroMapperInterface $microMapper,
TranslatorInterface $translator,
CacheInterface $cache,
) {
parent::__construct($translator, $cache);
}

public function load(object $from, string $toClass, array $context): object
{
$entity = $from;
assert($entity instanceof <?= $model_class_name ?>);
$dto = new <?= $model_class_name ?>Resource();
$dto->id = $entity->getId();

return $dto;
}

public function populate(object $from, object $to, array $context): object
{
$entity = $from;
$dto = $to;

assert($entity instanceof <?= $model_class_name ?>);
assert($dto instanceof <?= $model_class_name ?>Resource);

<?php if (!empty($fields)): ?>
<?php foreach ($fields as $field): ?>
<?php $Method = str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $field['name']))); ?>
$dto-><?= $field['name']; ?> = $entity->get<?= $Method; ?>();
<?php endforeach; ?>
<?php endif ?>
<?php if ($is_timestampable): ?>
$dto->createdAt = $entity->getCreatedAt();
$dto->updatedAt = $entity->getUpdatedAt();
<?php endif ?>
<?php if ($is_blameable): ?>
$dto->createdBy = $entity->getCreatedBy();
$dto->updatedBy = $entity->getUpdatedBy();
<?php endif ?>

return $this->translateDto($dto);
}
}
