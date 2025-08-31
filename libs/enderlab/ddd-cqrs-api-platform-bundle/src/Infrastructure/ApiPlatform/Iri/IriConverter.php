<?php
namespace EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\Iri;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use function Symfony\Component\String\u;

class IriConverter
{
    public const string BASE_IRI = '/api/';

    private const array EXCLUDES = [
        'Proxies',
        '__CG__',
        'App',
    ];

    private static ?Inflector $inflector = null;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function entityToIri(object $entity): Iri
    {
        if (
            false === method_exists($entity, 'getId') &&
            false === property_exists($entity, 'id')
        ) {
            throw new InvalidArgumentException(sprintf('This object "%s" is not an entity', get_class($entity)));
        }

        $className = get_class($entity);
        $classNameParts = $this->getNamespacePart($className);
        $domainName = u($classNameParts[0])->snake()->toString();
        $entityName = u(
            $this->pluralizeEntityName($classNameParts[count($classNameParts) - 1])
        )->snake()->toString();

        return new Iri(self::BASE_IRI.$domainName.'/'.$entityName.'/'.$entity->getId());
    }

    public function iriToEntity(Iri|string $iri): object
    {
        if (is_string($iri)) {
            $iri = new Iri($iri);
        }

        $domain = ucfirst(u($iri->getDomain())->camel()->toString());
        $entityName = ucfirst($this->singularizeEntityName(
            u($iri->getEntity())->camel()->toString()
        ));
        $id = $iri->getIdentifier();
        $entityClass = 'App\\'.$domain.'\\Domain\\Model\\'.$entityName;

        if (false === class_exists($entityClass)) {
            throw new InvalidArgumentException(sprintf('This object "%s" is not an entity', $entityClass));
        }

        $repository = $this->em->getRepository($entityClass);

        return $repository->find($id);

    }

    private function getNamespacePart(string $className): array
    {
        $classNameParts = explode('\\', $className);

        return array_values(array_filter($classNameParts, function ($part) {
            return !in_array($part, self::EXCLUDES);
        }));
    }

    private function pluralizeEntityName(string $name): string
    {
        return self::getInflector()->pluralize($name);
    }

    private function singularizeEntityName(string $name): string
    {
        return self::getInflector()->singularize($name);
    }

    private static function getInflector(): Inflector
    {
        if (null === static::$inflector) {
            static::$inflector = InflectorFactory::create()->build();
        }

        return static::$inflector;
    }
}
