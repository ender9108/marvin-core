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
#[ORM\GeneratedValue]
#[ORM\Column(type: 'integer', options: ['unsigned' => true])]
private ?int $id = null;
<?php endif ?>

public function getId(): ?<?php if ($is_aggregate_root): ?>string<?php else: ?>int<?php endif ?>
{
return $this->id;
}
}
