<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

final class NonSerializableFixture
{
    public function __construct(
        public readonly int $id,
    ) {
    }

    public function __serialize(): array
    {
        throw new \Exception('This object cannot be serialized');
    }

    public function __unserialize(array $data): void
    {
        throw new \Exception('This object cannot be unserialized');
    }
}
