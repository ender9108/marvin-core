<?php

namespace Marvin\Shared\Application\Email;

use Marvin\Shared\Domain\ValueObject\Email;

interface EmailDefinition
{
    public function recipient(): Email;

    public function subject(): string;

    public function subjectVariables(): array;

    public function template(): string;

    public function templateVariables(): array;

    public function locale(): string;

    public function getDomain(): string;
}
