<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class AllEqualTest extends \PHPUnit\Framework\TestCase
{
    /** @dataProvider equalIterableProvider */
    public function testEmptySingleAndRepeatedValues(iterable $data): void
    {
        // When / Then
        $this->assertTrue(Summary::allEqual($data));
    }

    public static function equalIterableProvider(): array
    {
        return [
            'empty array' => [[]],
            'single generator' => [GeneratorFixture::getGenerator([null])],
            'iterator' => [new ArrayIteratorFixture([[1], [1]])],
            'iterator aggregate' => [new IteratorAggregateFixture(['x', 'x'])],
        ];
    }

    public function testStrictAndCoerciveEquivalence(): void
    {
        // Given
        $data = [null, '', 0, 0.0, false];

        // When / Then
        $this->assertFalse(Summary::allEqual($data));
        $this->assertTrue(Summary::allEqual($data, false));
    }

    public function testProjectionIsCalledOncePerConsumedValue(): void
    {
        // Given
        $calls = 0;

        // When
        $result = Summary::allEqualBy([['id' => 1], ['id' => 1]], static function (array $item) use (&$calls): int {
            ++$calls;
            return $item['id'];
        });

        // Then
        $this->assertTrue($result);
        $this->assertSame(2, $calls);
    }

    public function testObjectsAndNanFollowUniqueExtractorSemantics(): void
    {
        // Given
        $object = (object) ['id' => 1];

        // When / Then
        $this->assertTrue(Summary::allEqual([$object, $object]));
        $this->assertFalse(Summary::allEqual([(object) ['id' => 1], (object) ['id' => 1]]));
        $this->assertTrue(Summary::allEqual([(object) ['id' => 1], (object) ['id' => 1]], false));
        $this->assertTrue(Summary::allEqual([\NAN, \NAN]));
    }

    public function testShortCircuitsAtFirstMismatch(): void
    {
        // Given
        $source = (static function (): \Generator {
            yield 1;
            yield 2;
            throw new \RuntimeException('must not be pulled');
        })();

        // When / Then
        $this->assertFalse(Summary::allEqual($source));
    }

    public function testCoerciveSingleNonSerializableObjectThrows(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Summary::allEqual([(object) ['callback' => static fn (): null => null]], false);
    }
}
