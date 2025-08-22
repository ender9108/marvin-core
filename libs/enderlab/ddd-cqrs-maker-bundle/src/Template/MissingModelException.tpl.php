<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

use EnderLab\DddBundle\Ddd\Exception\MissingExceptionInterface;
use Throwable;

final class Missing<?= $model_class_name ?>Exception extends \RuntimeException implements MissingExceptionInterface
{
public function __construct(int $id, int $code = 0, ?Throwable $previous = null)
{
parent::__construct(sprintf('Cannot find <?= $model_class_name ?> with id %s', $id), $code, $previous);
}
}
