<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

<?php if ($is_blameable): ?>
use EnderLab\BlameableBundle\Infrastructure\Interface\BlameableInterface;
use EnderLab\BlameableBundle\Domain\Trait\BlameableTrait;
<?php endif ?>
<?php if ($is_timestampable): ?>
use EnderLab\TimestampableBundle\Interface\TimestampableInterface;
use EnderLab\TimestampableBundle\Trait\TimestampableTrait;
<?php endif ?>
<?php if ($is_aggregate_root): ?>
use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Symfony\Component\Uid\UuidV4;
<?php else: ?>
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
<?php endif ?>
<?= $use_statements; ?>

class <?= $class_name; ?><?php if ($is_aggregate_root): ?> extends AggregateRoot<?php endif ?>
{
<?php if ($is_aggregate_root): ?>
private ?string $id = null;
<?php else: ?>
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
