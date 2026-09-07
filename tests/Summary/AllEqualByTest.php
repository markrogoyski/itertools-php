<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture\GeneratorFixture;

class AllEqualByTest extends \PHPUnit\Framework\TestCase
{
    public function testDistinctValuesCanProjectToSameIdentity(): void
    {
        // Given
        $items = [(object) ['group' => 1], (object) ['group' => 1]];

        // When
        $result = Summary::allEqualBy($items, static fn (object $item): int => $item->group);

        // Then
        $this->assertTrue($result);
    }

    public function testProjectsExactlyOnceAndShortCircuits(): void
    {
        // Given
        $calls = [];
        $source = (static function (): \Generator {
            yield 1;
            yield 2;
            throw new \RuntimeException('must not be pulled');
        })();

        // When
        $result = Summary::allEqualBy($source, static function (int $value) use (&$calls): int {
            $calls[] = $value;
            return $value;
        });

        // Then
        $this->assertFalse($result);
        $this->assertSame([1, 2], $calls);
    }

    public function testSingleProjectedNonSerializableObjectIsStillClassified(): void
    {
        // Given
        $calls = 0;
        $nonSerializable = (object) ['callback' => static fn (): null => null];

        // When
        try {
            Summary::allEqualBy(
                GeneratorFixture::getGenerator([1]),
                static function () use (&$calls, $nonSerializable): object {
                    ++$calls;
                    return $nonSerializable;
                },
                false,
            );
            $this->fail('Expected non-serializable projected key to throw');
        } catch (\InvalidArgumentException) {
        }

        // Then
        $this->assertSame(1, $calls);
    }

    public function testDistinguishesFreshlyProjectedObjects(): void
    {
        // When
        $result = Summary::allEqualBy([1, 2, 3], static fn (int $value): object => (object) ['value' => $value]);

        // Then
        $this->assertFalse($result);
    }

    public function testEquatesProjectionsReturningTheSameObjectInstance(): void
    {
        // Given
        $shared = (object) ['value' => 1];

        // When
        $result = Summary::allEqualBy([1, 2, 3], static fn (): object => $shared);

        // Then
        $this->assertTrue($result);
    }
}
