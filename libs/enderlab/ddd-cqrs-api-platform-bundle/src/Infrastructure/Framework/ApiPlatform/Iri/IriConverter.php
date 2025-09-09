<?php
namespace EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\Iri;

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

    public function modelToIri(object $model): Iri
    {
        if (
            false === method_exists($model, 'getId') &&
            false === property_exists($model, 'id')
        ) {
            throw new InvalidArgumentException(sprintf('This object "%s" is not an entity', get_class($entity)));
        }

        $className = get_class($model);
        $classNameParts = $this->getNamespacePart($className);
        $domainName = u($classNameParts[0])->snake()->toString();
        $modelName = u(
            $this->pluralizeModelName($classNameParts[count($classNameParts) - 1])
        )->snake()->toString();

        return new Iri(self::BASE_IRI.$domainName.'/'.$modelName.'/'.$model->getId());
    }

    public function iriToModel(Iri|string $iri): object
    {
        if (is_string($iri)) {
            $iri = new Iri($iri);
        }

        $domain = ucfirst(u($iri->getDomain())->camel()->toString());
        $modelName = ucfirst($this->singularizeModelName(
            u($iri->getModel())->camel()->toString()
        ));
        $id = $iri->getIdentifier();
        $modelClass = 'App\\'.$domain.'\\Domain\\Model\\'.$modelName;

        if (false === class_exists($modelClass)) {
            throw new InvalidArgumentException(sprintf('This object "%s" is not an model', $modelClass));
        }

        $repository = $this->em->getRepository($modelClass);

        return $repository->find($id);

    }

    private function getNamespacePart(string $className): array
    {
        $classNameParts = explode('\\', $className);

        return array_values(array_filter($classNameParts, function ($part) {
            return !in_array($part, self::EXCLUDES);
        }));
    }

    private function pluralizeModelName(string $name): string
    {
        return self::getInflector()->pluralize($name);
    }

    private function singularizeModelName(string $name): string
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
