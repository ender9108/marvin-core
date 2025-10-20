<?php

namespace EnderLab\DddCqrsMakerBundle\Maker;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

#[AsCommand(
    name: 'make:fixture',
    description: 'Crée une DataFixture avec Foundry Factory',
)]
class MakeFixtureCommand extends Command
{
    private Filesystem $filesystem;

    public function __construct(private readonly string $projectDir)
    {
        parent::__construct();
        $this->filesystem = new Filesystem();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('context', InputArgument::OPTIONAL, 'Nom du bounded context')
            ->addArgument('model', InputArgument::OPTIONAL, 'Nom du model');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Générateur de Fixture & Factory Foundry');

        $context = $this->askContext($io, $input);
        if (!$context) {
            return Command::FAILURE;
        }

        $modelName = $this->askModelName($io, $input);
        if (!$modelName) {
            return Command::FAILURE;
        }

        $io->section('Configuration de la Factory');

        $namedStates = $io->confirm('Créer des états nommés (ex: zigbee(), postgres()) ?', true);
        $states = [];

        if ($namedStates) {
            $states = $this->askNamedStates($io);
        }

        if (!$io->confirm('Générer la Factory et la Fixture ?', true)) {
            $io->warning('Génération annulée');
            return Command::SUCCESS;
        }

        try {
            $this->generateFactory($context, $modelName, $states);
            $this->generateFixture($context, $modelName);

            $factoryClass = "App\\Tests\\Factory\\{$context}\\{$modelName}Factory";
            $fixtureClass = "Marvin\\{$context}\\Infrastructure\\Framework\\Symfony\\DataFixtures\\{$modelName}Fixtures";

            $io->success([
                'Factory et Fixture générées avec succès !',
                '',
                'Fichiers créés :',
                "- {$factoryClass}",
                "- {$fixtureClass}",
                '',
                'Usage :',
                "// Dans un test",
                "{$modelName}Factory::createOne();",
                "{$modelName}Factory::new()->zigbee()->create(); // Si état nommé",
                "",
                "// Charger les fixtures",
                "php bin/console doctrine:fixtures:load",
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $io->error('Erreur lors de la génération : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function askContext(SymfonyStyle $io, InputInterface $input): ?string
    {
        $context = $input->getArgument('context');

        if (!$context) {
            $existingContexts = $this->getExistingContexts();

            if (!empty($existingContexts)) {
                $question = new ChoiceQuestion('Choisir un bounded context', $existingContexts);
                $context = $io->askQuestion($question);
            } else {
                $context = $io->ask('Nom du bounded context');
            }
        }

        return $context;
    }

    private function askModelName(SymfonyStyle $io, InputInterface $input): ?string
    {
        $modelName = $input->getArgument('model');

        if (!$modelName) {
            $modelName = $io->ask('Nom du model (ex: Container, Worker)');
        }

        return $modelName;
    }

    private function askNamedStates(SymfonyStyle $io): array
    {
        $states = [];

        $io->writeln('Définis les états nommés (méthodes helper sur la factory) :');
        $io->writeln('Ex: zigbee() -> crée un container Zigbee2MQTT');
        $io->writeln('Ex: postgres() -> crée un container PostgreSQL');

        while (true) {
            $stateName = $io->ask('Nom de l\'état (ou "stop")', 'stop');

            if (strtolower($stateName) === 'stop') {
                break;
            }

            $io->writeln("Définis les valeurs par défaut pour l'état '{$stateName}' :");

            $overrides = [];
            while (true) {
                $fieldName = $io->ask('Champ à override (ou "stop")', 'stop');

                if (strtolower($fieldName) === 'stop') {
                    break;
                }

                $fieldValue = $io->ask('Valeur (raw PHP)');
                $overrides[$fieldName] = $fieldValue;
            }

            $states[$stateName] = $overrides;
            $io->success("État '{$stateName}' ajouté !");
        }

        return $states;
    }

    private function getExistingContexts(): array
    {
        $srcDir = $this->projectDir . '/src';

        if (!is_dir($srcDir)) {
            return [];
        }

        $finder = new Finder();
        $finder->directories()->in($srcDir)->depth('== 0');

        $contexts = [];
        foreach ($finder as $dir) {
            $contexts[] = $dir->getFilename();
        }

        return $contexts;
    }

    private function generateFactory(string $context, string $modelName, array $states): void
    {
        $dir = $this->projectDir . "/tests/Factory/{$context}";
        $this->filesystem->mkdir($dir);

        $statesMethods = $this->generateStatesMethods($states);

        $content = <<<PHP
<?php

namespace App\Tests\Factory\\{$context};

use Marvin\\{$context}\\Domain\\Model\\{$modelName};
use Marvin\\{$context}\\Domain\\ValueObject\\Identity\\{$modelName}Id;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<{$modelName}>
 */
final class {$modelName}Factory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return {$modelName}::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'id' => {$modelName}Id::generate(),
            // TODO: Ajouter les champs par défaut
            // 'name' => self::faker()->unique()->slug(),
            // 'status' => self::faker()->randomElement(['active', 'inactive']),
        ];
    }
{$statesMethods}
}

PHP;

        $file = $dir . "/{$modelName}Factory.php";
        $this->filesystem->dumpFile($file, $content);
    }

    private function generateStatesMethods(array $states): string
    {
        if (empty($states)) {
            return '';
        }

        $methods = [];

        foreach ($states as $stateName => $overrides) {
            $overridesArray = [];
            foreach ($overrides as $field => $value) {
                $overridesArray[] = "            '{$field}' => {$value},";
            }

            $overridesBlock = implode("\n", $overridesArray);

            $methods[] = <<<PHP

    public function {$stateName}(): self
    {
        return \$this->with([
{$overridesBlock}
        ]);
    }
PHP;
        }

        return implode("\n", $methods);
    }

    private function generateFixture(string $context, string $modelName): void
    {
        $dir = $this->projectDir . "/src/{$context}/Infrastructure/Framework/Symfony/DataFixtures";
        $this->filesystem->mkdir($dir);

        $content = <<<PHP
<?php

namespace Marvin\\{$context}\\Infrastructure\\Framework\\Symfony\\DataFixtures;

use App\Tests\Factory\\{$context}\\{$modelName}Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class {$modelName}Fixtures extends Fixture
{
    public function load(ObjectManager \$manager): void
    {
        // Créer plusieurs {$modelName}s avec données aléatoires
        {$modelName}Factory::createMany(10);

        // Ou créer des {$modelName}s spécifiques
        // {$modelName}Factory::createOne([
        //     'name' => 'Example {$modelName}',
        // ]);

        \$manager->flush();
    }
}

PHP;

        $file = $dir . "/{$modelName}Fixtures.php";
        $this->filesystem->dumpFile($file, $content);
    }
}
