<?php

namespace Enderlab\DddCqrsMakerBundle\Maker;

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
    name: 'make:value-object',
    description: 'Crée un ValueObject (Enum ou Classe)',
)]
class MakeValueObjectCommand extends Command
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
            ->addArgument('name', InputArgument::OPTIONAL, 'Nom du ValueObject')
            ->addArgument('type', InputArgument::OPTIONAL, 'Type : enum ou class');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Générateur de ValueObject');

        $context = $this->askContext($io, $input);
        if (!$context) {
            return Command::FAILURE;
        }

        $name = $this->askName($io, $input);
        if (!$name) {
            return Command::FAILURE;
        }

        $type = $this->askType($io, $input);
        $isIdentity = $io->confirm('Est-ce un ValueObject Identity ?', false);

        if ($isIdentity) {
            $this->generateIdentityValueObject($context, $name, $io);
            return Command::SUCCESS;
        }

        if ($type === 'enum') {
            $this->generateEnumValueObject($context, $name, $io);
        } else {
            $this->generateClassValueObject($context, $name, $io);
        }

        return Command::SUCCESS;
    }

    private function askContext(SymfonyStyle $io, InputInterface $input): ?string
    {
        $context = $input->getArgument('context');

        if (!$context) {
            $existingContexts = $this->getExistingContexts();
            $existingContexts[] = 'Shared';

            if (!empty($existingContexts)) {
                $question = new ChoiceQuestion('Choisir un bounded context', $existingContexts);
                $context = $io->askQuestion($question);
            } else {
                $context = $io->ask('Nom du bounded context (ou "Shared")', 'Shared');
            }
        }

        return $context;
    }

    private function askName(SymfonyStyle $io, InputInterface $input): ?string
    {
        $name = $input->getArgument('name');

        if (!$name) {
            $name = $io->ask('Nom du ValueObject (ex: Status, Email)');
        }

        return $name;
    }

    private function askType(SymfonyStyle $io, InputInterface $input): string
    {
        $type = $input->getArgument('type');

        if (!$type) {
            $question = new ChoiceQuestion('Type de ValueObject', ['enum', 'class'], 'class');
            $type = $io->askQuestion($question);
        }

        return strtolower($type);
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

    private function generateIdentityValueObject(string $context, string $name, SymfonyStyle $io): void
    {
        $dir = $this->projectDir . "/src/{$context}/Domain/ValueObject/Identity";
        $this->filesystem->mkdir($dir);

        $content = <<<PHP
<?php

namespace Marvin\\{$context}\\Domain\\ValueObject\\Identity;

use Ramsey\Uuid\UuidV7;

final readonly class {$name}Id extends UuidV7
{
    public static function generate(): self
    {
        return new self(parent::uuid7()->getBytes());
    }

    public static function fromString(string \$uuid): self
    {
        return new self(parent::fromString(\$uuid)->getBytes());
    }
}

PHP;

        $file = $dir . "/{$name}Id.php";
        $this->filesystem->dumpFile($file, $content);

        $io->success("ValueObject Identity généré : Marvin\\{$context}\\Domain\\ValueObject\\Identity\\{$name}Id");
    }

    private function generateEnumValueObject(string $context, string $name, SymfonyStyle $io): void
    {
        $dir = $this->projectDir . "/src/{$context}/Domain/ValueObject";
        $this->filesystem->mkdir($dir);

        $values = $this->askEnumValues($io);

        if (empty($values)) {
            $io->error('Aucune valeur définie pour l\'enum');
            return;
        }

        $casesBlock = $this->generateEnumCases($values);

        $content = <<<PHP
<?php

namespace Marvin\\{$context}\\Domain\\ValueObject;

use Enderlab\DddCqrs\Domain\ValueObject\ValueObjectInterface;

enum {$name}: string implements ValueObjectInterface
{
{$casesBlock}

    public function equals(ValueObjectInterface \$other): bool
    {
        return \$other instanceof self && \$this->value === \$other->value;
    }

    public function toString(): string
    {
        return \$this->value;
    }
}

PHP;

        $file = $dir . "/{$name}.php";
        $this->filesystem->dumpFile($file, $content);

        $io->success("ValueObject Enum généré : Marvin\\{$context}\\Domain\\ValueObject\\{$name}");
    }

    private function generateClassValueObject(string $context, string $name, SymfonyStyle $io): void
    {
        $dir = $this->projectDir . "/src/{$context}/Domain/ValueObject";
        $this->filesystem->mkdir($dir);

        $backingType = $io->choice('Type de donnée principale', ['string', 'int', 'float'], 'string');

        $content = <<<PHP
<?php

namespace Marvin\\{$context}\\Domain\\ValueObject;

use Enderlab\DddCqrs\Domain\Assert\Assert;
use Enderlab\DddCqrs\Domain\ValueObject\ValueObjectInterface;

final readonly class {$name} implements ValueObjectInterface
{
    private {$backingType} \$value;

    private function __construct({$backingType} \$value)
    {
        Assert::notEmpty(\$value);
        \$this->value = \$value;
    }

    public static function fromString({$backingType} \$value): self
    {
        return new self(\$value);
    }

    public function toString(): string
    {
        return (string) \$this->value;
    }

    public function getValue(): {$backingType}
    {
        return \$this->value;
    }

    public function equals(ValueObjectInterface \$other): bool
    {
        return \$other instanceof self && \$this->value === \$other->value;
    }
}

PHP;

        $file = $dir . "/{$name}.php";
        $this->filesystem->dumpFile($file, $content);

        $io->success("ValueObject Classe généré : Marvin\\{$context}\\Domain\\ValueObject\\{$name}");
    }

    private function askEnumValues(SymfonyStyle $io): array
    {
        $values = [];
        $io->writeln('Définir les valeurs de l\'enum :');

        while (true) {
            $name = $io->ask('Nom de la constante (ou "stop")', 'stop');
            if (strtolower($name) === 'stop') {
                break;
            }

            $value = $io->ask('Valeur');
            $values[$name] = $value;
        }

        return $values;
    }

    private function generateEnumCases(array $values): string
    {
        $cases = [];
        foreach ($values as $name => $value) {
            $cases[] = "    case {$name} = '{$value}';";
        }

        return implode("\n", $cases);
    }
}
