<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

<?php if ($is_blameable): ?>
use EnderLab\BlameableBundle\Interface\BlameableInterface;
use EnderLab\BlameableBundle\Trait\BlameableTrait;
<?php endif ?>
<?php if ($is_timestampable): ?>
use EnderLab\TimestampableBundle\Interface\TimestampableInterface;
use EnderLab\TimestampableBundle\Trait\TimestampableTrait;
<?php endif ?>
<?php if ($is_aggregate_root): ?>
use EnderLab\DddCqrsBundle\Domain\Aggregate\AggregateRoot;
use Symfony\Component\Uid\UuidV4;
<?php else: ?>
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
<?php endif ?>
use Doctrine\ORM\Mapping as ORM;
<?= $use_statements; ?>

#[ORM\Entity]
class <?= $class_name; ?><?php if ($is_aggregate_root): ?> extends AggregateRoot<?php endif ?> <?php if ($is_blameable || $is_timestampable): ?>implements<?php endif ?> <?php if ($is_timestampable): ?>TimestampableInterface<?php endif ?><?php if ($is_blameable && $is_timestampable): ?>,<?php endif ?> <?php if ($is_blameable): ?>BlameableInterface<?php endif ?>
{
<?php if ($is_timestampable): ?>
use TimestampableTrait;
<?php endif ?>
<?php if ($is_blameable): ?>
use BlameableTrait;
<?php endif ?>

#[ORM\Id]
<?php if ($is_aggregate_root): ?>
#[ORM\Column(type: 'string', unique: true)]
private ?string $id = null;
<?php else: ?>
#[ORM\Id]
#[ORM\Column(type: 'uuid')]
#[Orm\GeneratedValue(strategy: 'CUSTOM')]
#[Orm\CustomIdGenerator(class: UuidGenerator::class)]
private ?string $id = null;
<?php endif ?>

<?php if ($is_aggregate_root): ?>
public function __construct()
{
$this->id = (string) new UuidV4();
}
<?php endif ?>

public function getId(): ?string
{
return $this->id;
}
}
