<?= "<?php\n"; ?>

namespace App\<?= $domain; ?>\Infrastructure\Doctrine\Repository;

use App\<?= $domain; ?>\Domain\Model\<?= $model_class_name; ?>;
use App\<?= $domain; ?>\Domain\Repository\<?= $model_class_name; ?>RepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class Doctrine<?= $model_class_name; ?>Repository extends ServiceEntityRepository implements <?= $model_class_name; ?>RepositoryInterface
{
    private const string ENTITY_CLASS = <?= $model_class_name; ?>::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    public function add(<?= $model_class_name; ?> $<?= $var_model_class_name; ?>): void
    {
        $this->getEntityManager()->persist($<?= $var_model_class_name; ?>);
    }

    public function remove(<?= $model_class_name; ?> $<?= $var_model_class_name; ?>): void
    {
        $this->getEntityManager()->remove($<?= $var_model_class_name; ?>);
    }

    public function byId(string|int $id): ?<?= $model_class_name; ?>
    {
        return $this->find($id);
    }
}
