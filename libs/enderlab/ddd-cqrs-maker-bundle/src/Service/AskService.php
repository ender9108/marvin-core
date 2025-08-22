<?php

namespace EnderLab\DddCqrsMakerBundle\Service;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Doctrine\EntityRelation;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class AskService
{
    public function __construct(
        private readonly DomainService $domainService,
        private ?ConsoleStyle $io = null,
    ) {
    }

    public function setConsoleStyle(ConsoleStyle $io): void
    {
        $this->io = $io;
    }

    public function domainNameQuestion(bool $autocomplete = true, bool $validate = true): string
    {
        $question = new Question('Domain name: ');

        if ($autocomplete) {
            $domains = $this->domainService->getDomains();
            if ($validate) {
                $question->setValidator($this->validateDomainChoice(...));
            }

            $question->setAutocompleterValues($domains);
        } else {
            $question->setValidator($this->validateNewDomain(...));
        }

        return Str::asClassName($this->io->askQuestion($question));
    }

    public function createDomainQuestion(): bool
    {
        $question = new ConfirmationQuestion('The domain doesn\'t exist. Would you like to create it?');
        return $this->io->askQuestion($question);
    }

    public function modelNameQuestion(string $domainName, bool $autocomplete = true): string
    {
        $question = new Question('Model name: ');

        if ($autocomplete) {
            $question->setAutocompleterValues($this->domainService->getModels($domainName));
            $question->setValidator($this->validateModelChoice(...));
        } else {
            $question->setValidator(Validator::notBlank(...));
        }

        return Str::asClassName($this->io->askQuestion($question));
    }

    public function addFieldQuestion(bool $isFirstField, array $currentFields): ?string
    {
        $this->io->writeln('');
        $questionText = $isFirstField
            ? 'New property name (press <return> to stop adding fields)'
            : 'Add another property? Enter the property name (or press <return> to stop adding fields)';

        return $this->io->ask($questionText, null, function ($name) use ($currentFields) {
            if (!$name) {
                return $name;
            }

            if (in_array($name, $currentFields, true)) {
                throw new \InvalidArgumentException(sprintf('The "%s" property already exists.', $name));
            }

            return $name;
        });
    }

    public function fieldTypeQuestion(string $defaultType, array $allValidTypes, array $relationTypes): string
    {
        $typeQuestion = new Question('Field type (enter ? to see main types)', $defaultType);
        $typeQuestion->setAutocompleterValues(array_merge($allValidTypes, $relationTypes));

        return $this->io->askQuestion($typeQuestion);
    }

    public function relationTypeQuestion(): string
    {
        $question = new Question(sprintf('Relation type? [%s]', implode(', ', EntityRelation::getValidRelationTypes())));
        $question->setAutocompleterValues(EntityRelation::getValidRelationTypes());

        return $this->io->askQuestion($question) ?? EntityRelation::MANY_TO_ONE;
    }

    public function relationModelQuestion(array $allFqcns, array $shortNames): ?string
    {
        $targetQuestion = new Question('What class should this model be related to?');
        $targetQuestion->setAutocompleterValues(array_values(array_unique(array_merge($allFqcns, $shortNames))));

        return $this->io->askQuestion($targetQuestion);
    }

    public function isAggregateRootQuestion(): bool
    {
        $question = new ConfirmationQuestion('Model is an aggregate root ?', false);
        return $this->io->askQuestion($question);
    }

    public function isTimestampableQuestion(): bool
    {
        $question = new ConfirmationQuestion('Would you implement Timestampable ?', true);
        return $this->io->askQuestion($question);
    }

    public function isBlameableQuestion(): bool
    {
        $question = new ConfirmationQuestion('Would you implement Blameable ?', true);
        return $this->io->askQuestion($question);
    }

    public function isApiResourceQuestion(string $modelName): bool
    {
        $question = new ConfirmationQuestion(sprintf('Generate ApiResource for model %s ?', $modelName), false);
        return $this->io->askQuestion($question);
    }

    private function validateDomainChoice(string $domainName): string
    {
        $domains = $this->domainService->getDomains();

        if (!in_array($domainName, $domains)) {
            throw new RuntimeCommandException(
                'Unknown domain (possible values: '.implode(', ', $domains).')'
            );
        }

        return $domainName;
    }

    private function validateNewDomain(string $domainName): string
    {
        if (in_array($domainName, $this->domainService->getDomains())) {
            throw new RuntimeCommandException(sprintf('The domain %s already exists', $domainName));
        }

        return $domainName;
    }

    private function validateModelChoice(string $model): string
    {
        $models = $this->domainService->getModels();

        if (!in_array($model, $models)) {
            throw new RuntimeCommandException('Unknown model (possible values: '.implode(', ', $models).')');
        }

        return $model;
    }
}
