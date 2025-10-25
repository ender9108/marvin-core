<?php

namespace MarvinTests\Secret\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Secret\Application\Command\StoreSecret;
use Marvin\Secret\Application\Command\UpdateSecret;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Marvin\Secret\Domain\Service\EncryptionServiceInterface;
use Marvin\Secret\Domain\ValueObject\SecretCategory;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Secret\Domain\ValueObject\SecretScope;
use Marvin\Secret\Domain\ValueObject\SecretValue;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

final class UpdateSecretHandlerTest extends KernelTestCase
{
    use ResetDatabase;

    public function test_update_secret_changes_updated_at(): void
    {
        self::bootKernel();
        $c = static::getContainer();

        /** @var SyncCommandBusInterface $bus */
        $bus = $c->get(SyncCommandBusInterface::class);
        /** @var SecretRepositoryInterface $repo */
        $repo = $c->get(SecretRepositoryInterface::class);
        /** @var EncryptionServiceInterface $crypto */
        $crypto = $c->get(EncryptionServiceInterface::class);

        $key = new SecretKey('db.password');
        $bus->handle(new StoreSecret(
            key: $key,
            value: SecretValue::fromPlainText('init-pass', $crypto),
            scope: SecretScope::GLOBAL,
            category: SecretCategory::INFRASTRUCTURE,
        ));

        $before = $repo->byKey($key);
        self::assertNotNull($before);
        $beforeUpdatedAt = $before->updatedAt;

        $bus->handle(new UpdateSecret(
            key: $key,
            newValue: 'new-P@ss-123',
        ));

        $after = $repo->byKey($key);
        self::assertNotNull($after);
        self::assertNotEquals($beforeUpdatedAt?->getTimestamp(), $after->updatedAt?->getTimestamp());
    }
}
