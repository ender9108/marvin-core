<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;
<?= $use_statements; ?>

interface <?= $class_name; ?> extends RepositoryInterface
{
    public function add(<?= $model_class_name; ?> $<?= $var_model_class_name; ?>): void;

    public function remove(<?= $model_class_name; ?> $<?= $var_model_class_name; ?>): void;

    public function byId(string|int $id): ?<?= $model_class_name; ?>;
}
