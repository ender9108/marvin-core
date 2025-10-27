<?php

namespace MarvinTests\Secret\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Secret\Application\Command\DeleteSecret;
use Marvin\Secret\Application\Command\StoreSecret;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Marvin\Secret\Domain\Service\EncryptionServiceInterface;
use Marvin\Secret\Domain\ValueObject\SecretCategory;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Secret\Domain\ValueObject\SecretScope;
use Marvin\Secret\Domain\ValueObject\SecretValue;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

final class DeleteSecretHandlerTest extends KernelTestCase
{
    use ResetDatabase;

    public function test_delete_secret_removes_it(): void
    {
        self::bootKernel();
        $c = static::getContainer();

        /** @var SyncCommandBusInterface $bus */
        $bus = $c->get(SyncCommandBusInterface::class);
        /** @var SecretRepositoryInterface $repo */
        $repo = $c->get(SecretRepositoryInterface::class);
        /** @var EncryptionServiceInterface $crypto */
        $crypto = $c->get(EncryptionServiceInterface::class);

        $key = new SecretKey('api.token');
        $bus->handle(new StoreSecret(
            key: $key,
            plainTextValue: SecretValue::fromPlainText('init-token', $crypto),
            scope: SecretScope::GLOBAL,
            category: SecretCategory::INFRASTRUCTURE,
        ));

        self::assertNotNull($repo->byKey($key));

        $bus->handle(new DeleteSecret($key));

        $deleted = $repo->byKey($key);
        self::assertNull($deleted);
    }
}
