<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture;

class ContainsCoerciveTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /**
     * @test         containsCoercive - empty iterable returns false
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterable(iterable $data): void
    {
        // When
        $result = Summary::containsCoercive($data, 1);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         containsCoercive - true
     * @dataProvider dataProviderForTrueArray
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testTrueArray(array $data, mixed $needle): void
    {
        // When
        $result = Summary::containsCoercive($data, $needle);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         containsCoercive - true
     * @dataProvider dataProviderForTrueArray
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testTrueGenerator(array $data, mixed $needle): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Summary::containsCoercive($data, $needle);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         containsCoercive - true
     * @dataProvider dataProviderForTrueArray
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testTrueIterator(array $data, mixed $needle): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Summary::containsCoercive($data, $needle);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         containsCoercive - true
     * @dataProvider dataProviderForTrueArray
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testTrueIteratorAggregate(array $data, mixed $needle): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Summary::containsCoercive($data, $needle);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForTrueArray(): array
    {
        return [
            // single element
            [[1], 1],
            [['a'], 'a'],
            [[null], null],
            [[false], false],

            // needle at different positions
            [[1, 2, 3, 4, 5], 1],          // first
            [[1, 2, 3, 4, 5], 5],          // last
            [[1, 2, 3, 4, 5], 3],          // middle

            // strings
            [['foo', 'bar', 'baz'], 'bar'],

            // floats
            [[1.1, 2.2, 3.3], 2.2],

            // null as legitimate value
            [[1, null, 2], null],

            // false as legitimate value
            [[true, false, true], false],

            // arrays
            [[[1, 2], [3, 4]], [1, 2]],

            // coercive scalar matches
            [[1, 2, 3], '1'],
            [['1', '2', '3'], 1],
            [[0, 1, 2], false],
            [[false, true], 0],
            [[0, 1, 2], '0'],
            [['0', '1'], 0],
            [[1, 2, 3], 1.0],
            [[1.0, 2.0, 3.0], 1],
            [[100, 200, 300], '1e2'],
            [['1e2', 200, 300], 100],

            // null/false/0/'0' all hash to falsy-equivalent under coercive
            [[null], false],
            [[null], 0],
            [[null], '0'],
            [[false], null],
            [[0], null],
        ];
    }

    /**
     * @test         containsCoercive - false
     * @dataProvider dataProviderForFalseArray
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testFalseArray(array $data, mixed $needle): void
    {
        // When
        $result = Summary::containsCoercive($data, $needle);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         containsCoercive - false
     * @dataProvider dataProviderForFalseArray
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testFalseGenerator(array $data, mixed $needle): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Summary::containsCoercive($data, $needle);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         containsCoercive - false
     * @dataProvider dataProviderForFalseArray
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testFalseIterator(array $data, mixed $needle): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Summary::containsCoercive($data, $needle);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         containsCoercive - false
     * @dataProvider dataProviderForFalseArray
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testFalseIteratorAggregate(array $data, mixed $needle): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Summary::containsCoercive($data, $needle);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForFalseArray(): array
    {
        return [
            // absent
            [[1, 2, 3], 4],
            [['a', 'b', 'c'], 'd'],

            // arrays: distinct content
            [[[1, 2], [3, 4]], [5, 6]],

            // distinct numeric values
            [[1, 2, 3], 99],
            [[1.1, 2.2, 3.3], 4.4],
        ];
    }

    /**
     * @test NaN matches NaN under coercive (UniqueExtractor hashes both to double_NAN)
     */
    public function testNaNMatchesNaN(): void
    {
        // Given
        $data = [1.0, \NAN, 2.0];

        // When
        $result = Summary::containsCoercive($data, \NAN);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test same-instance object matches under coercive
     */
    public function testSameInstanceObjectMatches(): void
    {
        // Given
        $obj = new \stdClass();
        $obj->value = 1;
        $data = [new \stdClass(), $obj, new \stdClass()];

        // When
        $result = Summary::containsCoercive($data, $obj);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test equal-looking distinct objects match via serialization under coercive
     */
    public function testEqualLookingDistinctObjectsMatch(): void
    {
        // Given
        $needle = new \stdClass();
        $needle->value = 42;

        $datum = new \stdClass();
        $datum->value = 42;

        $data = [$datum];

        // When
        $result = Summary::containsCoercive($data, $needle);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test distinct objects with different state do not match under coercive
     */
    public function testDistinctObjectsDifferentStateDoNotMatch(): void
    {
        // Given
        $needle = new \stdClass();
        $needle->value = 42;

        $datum = new \stdClass();
        $datum->value = 99;

        $data = [$datum];

        // When
        $result = Summary::containsCoercive($data, $needle);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test same resource matches; distinct resource does not
     */
    public function testResourceMatching(): void
    {
        // Given
        $fh1 = \fopen('php://memory', 'r');
        $fh2 = \fopen('php://memory', 'r');
        $data = [$fh1];

        try {
            // When
            $found = Summary::containsCoercive($data, $fh1);
            $notFound = Summary::containsCoercive($data, $fh2);

            // Then
            $this->assertTrue($found);
            $this->assertFalse($notFound);
        } finally {
            \fclose($fh1);
            \fclose($fh2);
        }
    }

    /**
     * @test short-circuits on first match (does not exhaust iterator)
     */
    public function testShortCircuitsOnFirstMatch(): void
    {
        // Given
        $generator = (function () {
            yield 1;
            yield 2;
            yield 3;
            yield 4;
            yield 5;
        })();

        // When
        $result = Summary::containsCoercive($generator, 2);

        // Then
        $this->assertTrue($result);
        $this->assertTrue($generator->valid(), 'Generator should not be exhausted after a match');
        $this->assertSame(2, $generator->current(), 'Generator cursor rests at the matched element');
    }

    /**
     * @test non-serializable needle throws immediately (before iterating data)
     */
    public function testNonSerializableNeedleThrowsImmediately(): void
    {
        // Given
        $needle = new Fixture\NonSerializableFixture(1);
        $consumed = false;
        $data = (function () use (&$consumed) {
            $consumed = true;
            yield 1;
        })();

        // Then
        $this->expectException(\InvalidArgumentException::class);

        try {
            // When
            Summary::containsCoercive($data, $needle);
        } finally {
            $this->assertFalse($consumed, 'Data iteration must not begin if needle is non-serializable');
        }
    }

    /**
     * @test match before non-serializable datum returns true without throwing
     */
    public function testMatchBeforeNonSerializableDatumReturnsTrue(): void
    {
        // Given
        $needle = 2;
        $data = [1, 2, new Fixture\NonSerializableFixture(3)];

        // When
        $result = Summary::containsCoercive($data, $needle);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test non-serializable datum reached before any match throws
     */
    public function testNonSerializableDatumBeforeMatchThrows(): void
    {
        // Given
        $needle = 99;
        $data = [1, new Fixture\NonSerializableFixture(2), 99];

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Summary::containsCoercive($data, $needle);
    }
}
