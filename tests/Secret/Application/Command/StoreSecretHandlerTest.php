<?php

namespace MarvinTests\Secret\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Secret\Application\Command\StoreSecret;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Marvin\Secret\Domain\Service\EncryptionServiceInterface;
use Marvin\Secret\Domain\ValueObject\SecretCategory;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Secret\Domain\ValueObject\SecretScope;
use Marvin\Secret\Domain\ValueObject\SecretValue;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

final class StoreSecretHandlerTest extends KernelTestCase
{
    use ResetDatabase;

    public function test_store_secret_persists_entity(): void
    {
        self::bootKernel();
        $c = static::getContainer();

        /** @var SyncCommandBusInterface $bus */
        $bus = $c->get(SyncCommandBusInterface::class);
        /** @var SecretRepositoryInterface $repo */
        $repo = $c->get(SecretRepositoryInterface::class);
        /** @var EncryptionServiceInterface $crypto */
        $crypto = $c->get(EncryptionServiceInterface::class);

        $key = new SecretKey('mqtt.password');
        $value = SecretValue::fromPlainText('super-secret', $crypto);

        $bus->handle(new StoreSecret(
            key: $key,
            value: $value,
            scope: SecretScope::GLOBAL,
            category: SecretCategory::INFRASTRUCTURE,
        ));

        self::assertTrue($repo->exists($key));
        $secret = $repo->byKey($key);
        self::assertNotNull($secret);
        self::assertSame('mqtt.password', $secret->key->value);
    }
}
