<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture;

class ContainsTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /**
     * @test         contains - empty iterable returns false
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterable(iterable $data): void
    {
        // When
        $result = Summary::contains($data, 1);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         contains - true
     * @dataProvider dataProviderForTrueArray
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testTrueArray(array $data, mixed $needle): void
    {
        // When
        $result = Summary::contains($data, $needle);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         contains - true
     * @dataProvider dataProviderForTrueArray
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testTrueGenerator(array $data, mixed $needle): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Summary::contains($data, $needle);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         contains - true
     * @dataProvider dataProviderForTrueArray
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testTrueIterator(array $data, mixed $needle): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Summary::contains($data, $needle);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         contains - true
     * @dataProvider dataProviderForTrueArray
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testTrueIteratorAggregate(array $data, mixed $needle): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Summary::contains($data, $needle);

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

            // arrays (strict ===: equal arrays match)
            [[[1, 2], [3, 4]], [1, 2]],

            // mixed types
            [[1, '2', 3.0, true, null], '2'],
        ];
    }

    /**
     * @test         contains - false
     * @dataProvider dataProviderForFalseArray
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testFalseArray(array $data, mixed $needle): void
    {
        // When
        $result = Summary::contains($data, $needle);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         contains - false
     * @dataProvider dataProviderForFalseArray
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testFalseGenerator(array $data, mixed $needle): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Summary::contains($data, $needle);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         contains - false
     * @dataProvider dataProviderForFalseArray
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testFalseIterator(array $data, mixed $needle): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Summary::contains($data, $needle);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         contains - false
     * @dataProvider dataProviderForFalseArray
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testFalseIteratorAggregate(array $data, mixed $needle): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Summary::contains($data, $needle);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForFalseArray(): array
    {
        return [
            // absent
            [[1, 2, 3], 4],
            [['a', 'b', 'c'], 'd'],

            // strict type mismatch: 1 vs '1'
            [[1, 2, 3], '1'],
            [['1', '2', '3'], 1],

            // strict type mismatch: 0 vs false
            [[0, 1, 2], false],
            [[false, true], 0],

            // strict type mismatch: 0 vs '0'
            [[0, 1, 2], '0'],
            [['0', '1'], 0],

            // strict type mismatch: null vs false
            [[null], false],
            [[false], null],

            // strict type mismatch: null vs 0
            [[null], 0],
            [[0], null],

            // strict type mismatch: 1 vs 1.0 (contains uses ===, ints and floats differ)
            [[1, 2, 3], 1.0],

            // arrays: distinct content
            [[[1, 2], [3, 4]], [5, 6]],
        ];
    }

    /**
     * @test NaN never matches NaN in strict mode
     */
    public function testNaNNeverMatchesNaN(): void
    {
        // Given
        $data = [1.0, \NAN, 2.0];

        // When
        $result = Summary::contains($data, \NAN);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test same-instance object matches
     */
    public function testSameInstanceObjectMatches(): void
    {
        // Given
        $obj = new \stdClass();
        $data = [new \stdClass(), $obj, new \stdClass()];

        // When
        $result = Summary::contains($data, $obj);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test equal-looking distinct objects do not match in strict mode
     */
    public function testEqualLookingDistinctObjectsDoNotMatch(): void
    {
        // Given
        $needle = new \stdClass();
        $needle->value = 42;

        $datum = new \stdClass();
        $datum->value = 42;

        $data = [$datum];

        // When
        $result = Summary::contains($data, $needle);

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
            $found = Summary::contains($data, $fh1);
            $notFound = Summary::contains($data, $fh2);

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
        $result = Summary::contains($generator, 2);

        // Then
        $this->assertTrue($result);
        $this->assertTrue($generator->valid(), 'Generator should not be exhausted after a match');
        $this->assertSame(2, $generator->current(), 'Generator cursor rests at the matched element');
    }
}
