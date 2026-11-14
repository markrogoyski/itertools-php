<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Stream;
use IterTools\Summary;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;
use IterTools\Tests\Fixture\RecordingIteratorFixture;

class SameTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testEmpty(): void
    {
        // When
        $result = Summary::same();

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test A single iterable is the same as itself without being read.
     */
    public function testSingleIterableIsNotRead(): void
    {
        // Given
        $pulled = [];
        $iterator = new RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = Summary::same($iterator);

        // Then
        $this->assertTrue($result);
        $this->assertSame([], $pulled);
    }

    /**
     * @test A single throwing iterable is the same as itself without its exception being raised.
     */
    public function testSingleThrowingIterable(): void
    {
        // Given
        $throwing = (static function (): \Generator {
            throw new \RuntimeException('must not be pulled');
            yield; // unreachable
        })();

        // When
        $result = Summary::same($throwing);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test The same iterator passed twice is the same as itself without being read.
     */
    public function testRepeatedIteratorIsNotRead(): void
    {
        // Given
        $pulled = [];
        $iterator = new RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = Summary::same($iterator, $iterator);

        // Then
        $this->assertTrue($result);
        $this->assertSame([], $pulled);
    }

    /**
     * @test A stream of an iterator passed twice is the same as itself without the iterator being read.
     */
    public function testRepeatedStreamIsNotRead(): void
    {
        // Given
        $pulled = [];
        $stream = Stream::of(new RecordingIteratorFixture([1, 2, 3], $pulled));

        // When
        $result = Summary::same($stream, $stream);

        // Then
        $this->assertTrue($result);
        $this->assertSame([], $pulled);
    }

    /**
     * @test A stream of an iterator and that iterator are the same without the iterator being read.
     */
    public function testStreamAndItsIteratorAreNotRead(): void
    {
        // Given
        $pulled   = [];
        $iterator = new RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = Summary::same(Stream::of($iterator), $iterator);

        // Then
        $this->assertTrue($result);
        $this->assertSame([], $pulled);
    }

    /**
     * @test The same throwing generator passed twice is the same as itself without its exception being raised.
     */
    public function testRepeatedThrowingIterable(): void
    {
        // Given
        $throwing = (static function (): \Generator {
            throw new \RuntimeException('must not be pulled');
            yield; // unreachable
        })();

        // When
        $result = Summary::same($throwing, $throwing);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         Same array true
     * @dataProvider dataProviderForArrayTrue
     * @param        iterable ...$iterables
     */
    public function testArrayTrue(iterable ...$iterables)
    {
        // When
        $result = Summary::same(...$iterables);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForArrayTrue(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [[]],
            [[1]],
            [[1, 2, 3]],
            [[], []],
            [[], $gen([])],
            [[], $iter([])],
            [[], $trav([])],

            [[], [], []],
            [[], $gen([]), []],
            [[], $iter([]), []],
            [[], $trav([]), []],
            [[], [], $gen([])],
            [[], [], $iter([])],
            [[], [], $trav([])],
            [[], $gen([]), $iter([])],
            [[], $iter([]), $trav([])],

            [[], [], [], [], []],
            [[], $gen([]), $iter([]), $trav([]), []],
            [[], [], $gen([]), $iter([]), $trav([])],

            [[1], [1]],
            [[1], $gen([1])],
            [[1], $iter([1])],
            [[1], $trav([1])],

            [[1], [1], [1]],
            [[1], $gen([1]), [1]],
            [[1], $iter([1]), [1]],
            [[1], $trav([1]), [1]],
            [[1], [1], $gen([1])],
            [[1], [1], $iter([1])],
            [[1], [1], $trav([1])],
            [[1], $gen([1]), $iter([1])],
            [[1], $iter([1]), $trav([1])],

            [[1, 2], [1, 2], [1, 2], [1, 2]],
            [[1, 2], [1, 2], $gen([1, 2]), [1, 2]],
            [[1, 2], [1, 2], $iter([1, 2]), [1, 2]],
            [[1, 2], [1, 2], $trav([1, 2]), [1, 2]],
            [[1, 2], [1, 2], $gen([1, 2]), $iter([1, 2])],
            [[1, 2], [1, 2], $iter([1, 2]), $trav([1, 2])],

            [[1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3]],
            [[1, 2, 3], [1, 2, 3], $gen([1, 2, 3]), [1, 2, 3]],
            [[1, 2, 3], [1, 2, 3], $iter([1, 2, 3]), [1, 2, 3]],
            [[1, 2, 3], [1, 2, 3], $trav([1, 2, 3]), [1, 2, 3]],

            [['a', 2], ['a', 2], ['a', 2], ['a', 2]],
            [['a', 2], $gen(['a', 2]), ['a', 2], ['a', 2]],
            [['a', 2], $iter(['a', 2]), ['a', 2], ['a', 2]],
            [['a', 2], $trav(['a', 2]), ['a', 2], ['a', 2]],

            [[1, null], [1, null], [1, null], [1, null]],
            [[1, null], [1, null], [1, null], $gen([1, null])],
            [[1, null], [1, null], [1, null], $iter([1, null])],
            [[1, null], [1, null], [1, null], $trav([1, null])],
        ];
    }

    /**
     * @test         Same array true
     * @dataProvider dataProviderForArrayFalse
     * @param        iterable ...$iterables
     */
    public function testArrayFalse(iterable ...$iterables)
    {
        // When
        $result = Summary::same(...$iterables);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForArrayFalse(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [[], [1]],
            [[], $gen([1])],
            [[], $iter([1])],
            [[], $trav([1])],

            [[1], []],
            [[1], $gen([])],
            [[1], $iter([])],
            [[1], $trav([])],

            [['1'], [1], [1], [1]],
            [['1'], $gen([1]), [1], [1]],
            [['1'], [1], $iter([1]), [1]],
            [['1'], [1], [1], $trav([1])],

            [[1], ['1']],
            [[1], $gen(['1'])],
            [[1], $iter(['1'])],
            [[1], $trav(['1'])],

            [[1], [2]],
            [[1], $gen([2])],
            [[1], $iter([2])],
            [[1], $trav([2])],

            [[1], ['1'], [1]],
            [[1], $gen(['1']), [1]],
            [[1], $iter(['1']), [1]],
            [[1], $trav(['1']), [1]],
            [[1], ['1'], $gen([1])],
            [[1], ['1'], $iter([1])],
            [[1], ['1'], $trav([1])],
            [[1], $iter(['1']), $gen([1])],
            [[1], $trav(['1']), $iter([1])],
            [[1], $gen(['1']), $trav([1])],

            [[1], [1], ['1']],
            [[1], [1], [2], [1]],
            [[1], [1], ['1'], [1]],
            [[1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, '3']],
            [[1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 1]],
            [[1], [1, null], [1, null], [1]],
            [[1], [1, null], [1, null], [1, null]],
            [[1, null], [1], [1], [1]],
            [[1, null], [1], [1, null], [1]],
        ];
    }

    /**
     * @test         Same generators true
     * @dataProvider dataProviderForGeneratorsTrue
     * @param        array<\Generator> $iterables
     */
    public function testGeneratorsTrue(iterable ...$iterables)
    {
        // When
        $result = Summary::same(...$iterables);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForGeneratorsTrue(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [$gen([]), []],
            [$gen([]), $gen([])],
            [$gen([]), $iter([])],
            [$gen([]), $trav([])],

            [$gen([]), [], []],
            [$gen([]), $gen([]), []],
            [$gen([]), $iter([]), []],
            [$gen([]), $trav([]), []],
            [$gen([]), [], $gen([])],
            [$gen([]), [], $iter([])],
            [$gen([]), [], $trav([])],
            [$gen([]), $gen([]), $iter([])],
            [$gen([]), $iter([]), $trav([])],

            [$gen([]), [], [], [], []],
            [$gen([]), $gen([]), $iter([]), $trav([]), []],
            [$gen([]), [], $gen([]), $iter([]), $trav([])],

            [$gen([1]), [1]],
            [$gen([1]), $gen([1])],
            [$gen([1]), $iter([1])],
            [$gen([1]), $trav([1])],

            [$gen([1]), [1], [1]],
            [$gen([1]), $gen([1]), [1]],
            [$gen([1]), $iter([1]), [1]],
            [$gen([1]), $trav([1]), [1]],
            [$gen([1]), [1], $gen([1])],
            [$gen([1]), [1], $iter([1])],
            [$gen([1]), [1], $trav([1])],
            [$gen([1]), $gen([1]), $iter([1])],
            [$gen([1]), $iter([1]), $trav([1])],

            [$gen([1, 2]), [1, 2], [1, 2], [1, 2]],
            [$gen([1, 2]), [1, 2], $gen([1, 2]), [1, 2]],
            [$gen([1, 2]), [1, 2], $iter([1, 2]), [1, 2]],
            [$gen([1, 2]), [1, 2], $trav([1, 2]), [1, 2]],
            [$gen([1, 2]), [1, 2], $gen([1, 2]), $iter([1, 2])],
            [$gen([1, 2]), [1, 2], $iter([1, 2]), $trav([1, 2])],

            [$gen([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, 3]],
            [$gen([1, 2, 3]), [1, 2, 3], $gen([1, 2, 3]), [1, 2, 3]],
            [$gen([1, 2, 3]), [1, 2, 3], $iter([1, 2, 3]), [1, 2, 3]],
            [$gen([1, 2, 3]), [1, 2, 3], $trav([1, 2, 3]), [1, 2, 3]],

            [$gen(['a', 2]), ['a', 2], ['a', 2], ['a', 2]],
            [$gen(['a', 2]), $gen(['a', 2]), ['a', 2], ['a', 2]],
            [$gen(['a', 2]), $iter(['a', 2]), ['a', 2], ['a', 2]],
            [$gen(['a', 2]), $trav(['a', 2]), ['a', 2], ['a', 2]],

            [$gen([1, null]), [1, null], [1, null], [1, null]],
            [$gen([1, null]), [1, null], [1, null], $gen([1, null])],
            [$gen([1, null]), [1, null], [1, null], $iter([1, null])],
            [$gen([1, null]), [1, null], [1, null], $trav([1, null])],
        ];
    }

    /**
     * @test         Same generators true
     * @dataProvider dataProviderForGeneratorsFalse
     * @param        iterable ...$iterables
     */
    public function testGeneratorsFalse(iterable ...$iterables)
    {
        // When
        $result = Summary::same(...$iterables);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForGeneratorsFalse(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [$gen([]), [1]],
            [$gen([]), $gen([1])],
            [$gen([]), $iter([1])],
            [$gen([]), $trav([1])],

            [$gen([1]), []],
            [$gen([1]), $gen([])],
            [$gen([1]), $iter([])],
            [$gen([1]), $trav([])],

            [$gen(['1']), [1], [1], [1]],
            [$gen(['1']), $gen([1]), [1], [1]],
            [$gen(['1']), [1], $iter([1]), [1]],
            [$gen(['1']), [1], [1], $trav([1])],

            [$gen([1]), ['1']],
            [$gen([1]), $gen(['1'])],
            [$gen([1]), $iter(['1'])],
            [$gen([1]), $trav(['1'])],

            [$gen([1]), [2]],
            [$gen([1]), $gen([2])],
            [$gen([1]), $iter([2])],
            [$gen([1]), $trav([2])],

            [$gen([1]), ['1'], [1]],
            [$gen([1]), $gen(['1']), [1]],
            [$gen([1]), $iter(['1']), [1]],
            [$gen([1]), $trav(['1']), [1]],
            [$gen([1]), ['1'], $gen([1])],
            [$gen([1]), ['1'], $iter([1])],
            [$gen([1]), ['1'], $trav([1])],
            [$gen([1]), $iter(['1']), $gen([1])],
            [$gen([1]), $trav(['1']), $iter([1])],
            [$gen([1]), $gen(['1']), $trav([1])],

            [$gen([1]), [1], ['1']],
            [$gen([1]), [1], [2], [1]],
            [$gen([1]), [1], ['1'], [1]],
            [$gen([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, '3']],
            [$gen([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, 1]],
            [$gen([1]), [1, null], [1, null], [1]],
            [$gen([1]), [1, null], [1, null], [1, null]],
            [$gen([1, null]), [1], [1], [1]],
            [$gen([1, null]), [1], [1, null], [1]],
        ];
    }

    /**
     * @test         Same iterators true
     * @dataProvider dataProviderForIteratorsTrue
     * @param        iterable ...$iterables
     */
    public function testIteratorsTrue(iterable ...$iterables)
    {
        // When
        $result = Summary::same(...$iterables);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForIteratorsTrue(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [$iter([]), []],
            [$iter([]), $gen([])],
            [$iter([]), $iter([])],
            [$iter([]), $trav([])],

            [$iter([]), [], []],
            [$iter([]), $gen([]), []],
            [$iter([]), $iter([]), []],
            [$iter([]), $trav([]), []],
            [$iter([]), [], $gen([])],
            [$iter([]), [], $iter([])],
            [$iter([]), [], $trav([])],
            [$iter([]), $gen([]), $iter([])],
            [$iter([]), $iter([]), $trav([])],

            [$iter([]), [], [], [], []],
            [$iter([]), $gen([]), $iter([]), $trav([]), []],
            [$iter([]), [], $gen([]), $iter([]), $trav([])],

            [$iter([1]), [1]],
            [$iter([1]), $gen([1])],
            [$iter([1]), $iter([1])],
            [$iter([1]), $trav([1])],

            [$iter([1]), [1], [1]],
            [$iter([1]), $gen([1]), [1]],
            [$iter([1]), $iter([1]), [1]],
            [$iter([1]), $trav([1]), [1]],
            [$iter([1]), [1], $gen([1])],
            [$iter([1]), [1], $iter([1])],
            [$iter([1]), [1], $trav([1])],
            [$iter([1]), $gen([1]), $iter([1])],
            [$iter([1]), $iter([1]), $trav([1])],

            [$iter([1, 2]), [1, 2], [1, 2], [1, 2]],
            [$iter([1, 2]), [1, 2], $gen([1, 2]), [1, 2]],
            [$iter([1, 2]), [1, 2], $iter([1, 2]), [1, 2]],
            [$iter([1, 2]), [1, 2], $trav([1, 2]), [1, 2]],
            [$iter([1, 2]), [1, 2], $gen([1, 2]), $iter([1, 2])],
            [$iter([1, 2]), [1, 2], $iter([1, 2]), $trav([1, 2])],

            [$iter([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, 3]],
            [$iter([1, 2, 3]), [1, 2, 3], $gen([1, 2, 3]), [1, 2, 3]],
            [$iter([1, 2, 3]), [1, 2, 3], $iter([1, 2, 3]), [1, 2, 3]],
            [$iter([1, 2, 3]), [1, 2, 3], $trav([1, 2, 3]), [1, 2, 3]],

            [$iter(['a', 2]), ['a', 2], ['a', 2], ['a', 2]],
            [$iter(['a', 2]), $gen(['a', 2]), ['a', 2], ['a', 2]],
            [$iter(['a', 2]), $iter(['a', 2]), ['a', 2], ['a', 2]],
            [$iter(['a', 2]), $trav(['a', 2]), ['a', 2], ['a', 2]],

            [$iter([1, null]), [1, null], [1, null], [1, null]],
            [$iter([1, null]), [1, null], [1, null], $gen([1, null])],
            [$iter([1, null]), [1, null], [1, null], $iter([1, null])],
            [$iter([1, null]), [1, null], [1, null], $trav([1, null])],
        ];
    }

    /**
     * @test         Same iterators true
     * @dataProvider dataProviderForIteratorsFalse
     * @param        iterable ...$iterables
     */
    public function testIteratorsFalse(iterable ...$iterables)
    {
        // When
        $result = Summary::same(...$iterables);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForIteratorsFalse(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [$iter([]), [1]],
            [$iter([]), $gen([1])],
            [$iter([]), $iter([1])],
            [$iter([]), $trav([1])],

            [$iter([1]), []],
            [$iter([1]), $gen([])],
            [$iter([1]), $iter([])],
            [$iter([1]), $trav([])],

            [$iter(['1']), [1], [1], [1]],
            [$iter(['1']), $gen([1]), [1], [1]],
            [$iter(['1']), [1], $iter([1]), [1]],
            [$iter(['1']), [1], [1], $trav([1])],

            [$iter([1]), ['1']],
            [$iter([1]), $gen(['1'])],
            [$iter([1]), $iter(['1'])],
            [$iter([1]), $trav(['1'])],

            [$iter([1]), [2]],
            [$iter([1]), $gen([2])],
            [$iter([1]), $iter([2])],
            [$iter([1]), $trav([2])],

            [$iter([1]), ['1'], [1]],
            [$iter([1]), $gen(['1']), [1]],
            [$iter([1]), $iter(['1']), [1]],
            [$iter([1]), $trav(['1']), [1]],
            [$iter([1]), ['1'], $gen([1])],
            [$iter([1]), ['1'], $iter([1])],
            [$iter([1]), ['1'], $trav([1])],
            [$iter([1]), $iter(['1']), $gen([1])],
            [$iter([1]), $trav(['1']), $iter([1])],
            [$iter([1]), $gen(['1']), $trav([1])],

            [$iter([1]), [1], ['1']],
            [$iter([1]), [1], [2], [1]],
            [$iter([1]), [1], ['1'], [1]],
            [$iter([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, '3']],
            [$iter([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, 1]],
            [$iter([1]), [1, null], [1, null], [1]],
            [$iter([1]), [1, null], [1, null], [1, null]],
            [$iter([1, null]), [1], [1], [1]],
            [$iter([1, null]), [1], [1, null], [1]],
        ];
    }

    /**
     * @test         Same iterators true
     * @dataProvider dataProviderForTraversablesTrue
     * @param        iterable ...$iterables
     */
    public function testTraversablesTrue(iterable ...$iterables)
    {
        // When
        $result = Summary::same(...$iterables);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForTraversablesTrue(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [$trav([]),        []],
            [$trav([]),        $gen([])],
            [$trav([]),        $iter([])],
            [$trav([]),        $trav([])],

            [$trav([]),        [], []],
            [$trav([]),        $gen([]), []],
            [$trav([]),        $iter([]), []],
            [$trav([]),        $trav([]), []],
            [$trav([]),        [], $gen([])],
            [$trav([]),        [], $iter([])],
            [$trav([]),        [], $trav([])],
            [$trav([]),        $gen([]), $iter([])],
            [$trav([]),        $iter([]), $trav([])],

            [$trav([]),        [], [], [], []],
            [$trav([]),        $gen([]), $iter([]), $trav([]), []],
            [$trav([]),        [], $gen([]), $iter([]), $trav([])],

            [$trav([1]),       [1]],
            [$trav([1]),       $gen([1])],
            [$trav([1]),       $iter([1])],
            [$trav([1]),       $trav([1])],

            [$trav([1]),       [1], [1]],
            [$trav([1]),       $gen([1]), [1]],
            [$trav([1]),       $iter([1]), [1]],
            [$trav([1]),       $trav([1]), [1]],
            [$trav([1]),       [1], $gen([1])],
            [$trav([1]),       [1], $iter([1])],
            [$trav([1]),       [1], $trav([1])],
            [$trav([1]),       $gen([1]), $iter([1])],
            [$trav([1]),       $iter([1]), $trav([1])],

            [$trav([1, 2]),    [1, 2], [1, 2], [1, 2]],
            [$trav([1, 2]),    [1, 2], $gen([1, 2]), [1, 2]],
            [$trav([1, 2]),    [1, 2], $iter([1, 2]), [1, 2]],
            [$trav([1, 2]),    [1, 2], $trav([1, 2]), [1, 2]],
            [$trav([1, 2]),    [1, 2], $gen([1, 2]), $iter([1, 2])],
            [$trav([1, 2]),    [1, 2], $iter([1, 2]), $trav([1, 2])],

            [$trav([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, 3]],
            [$trav([1, 2, 3]), [1, 2, 3], $gen([1, 2, 3]), [1, 2, 3]],
            [$trav([1, 2, 3]), [1, 2, 3], $iter([1, 2, 3]), [1, 2, 3]],
            [$trav([1, 2, 3]), [1, 2, 3], $trav([1, 2, 3]), [1, 2, 3]],

            [$trav(['a', 2]),  ['a', 2], ['a', 2], ['a', 2]],
            [$trav(['a', 2]),  $gen(['a', 2]), ['a', 2], ['a', 2]],
            [$trav(['a', 2]),  $iter(['a', 2]), ['a', 2], ['a', 2]],
            [$trav(['a', 2]),  $trav(['a', 2]), ['a', 2], ['a', 2]],

            [$trav([1, null]), [1, null], [1, null], [1, null]],
            [$trav([1, null]), [1, null], [1, null], $gen([1, null])],
            [$trav([1, null]), [1, null], [1, null], $iter([1, null])],
            [$trav([1, null]), [1, null], [1, null], $trav([1, null])],
        ];
    }

    /**
     * @test         Same iterators true
     * @dataProvider dataProviderForTraversablesFalse
     * @param        iterable ...$iterables
     */
    public function testTraversablesFalse(iterable ...$iterables)
    {
        // When
        $result = Summary::same(...$iterables);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForTraversablesFalse(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [$trav([]), [1]],
            [$trav([]), $gen([1])],
            [$trav([]), $iter([1])],
            [$trav([]), $trav([1])],

            [$trav([1]), []],
            [$trav([1]), $gen([])],
            [$trav([1]), $iter([])],
            [$trav([1]), $trav([])],

            [$trav(['1']), [1], [1], [1]],
            [$trav(['1']), $gen([1]), [1], [1]],
            [$trav(['1']), [1], $iter([1]), [1]],
            [$trav(['1']), [1], [1], $trav([1])],

            [$trav([1]), ['1']],
            [$trav([1]), $gen(['1'])],
            [$trav([1]), $iter(['1'])],
            [$trav([1]), $trav(['1'])],

            [$trav([1]), [2]],
            [$trav([1]), $gen([2])],
            [$trav([1]), $iter([2])],
            [$trav([1]), $trav([2])],

            [$trav([1]), ['1'], [1]],
            [$trav([1]), $gen(['1']), [1]],
            [$trav([1]), $iter(['1']), [1]],
            [$trav([1]), $trav(['1']), [1]],
            [$trav([1]), ['1'], $gen([1])],
            [$trav([1]), ['1'], $iter([1])],
            [$trav([1]), ['1'], $trav([1])],
            [$trav([1]), $iter(['1']), $gen([1])],
            [$trav([1]), $trav(['1']), $iter([1])],
            [$trav([1]), $gen(['1']), $trav([1])],

            [$trav([1]), [1], ['1']],
            [$trav([1]), [1], [2], [1]],
            [$trav([1]), [1], ['1'], [1]],
            [$trav([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, '3']],
            [$trav([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, 1]],
            [$trav([1]), [1, null], [1, null], [1]],
            [$trav([1]), [1, null], [1, null], [1, null]],
            [$trav([1, null]), [1], [1], [1]],
            [$trav([1, null]), [1], [1, null], [1]],
        ];
    }

    /**
     * @test The same iterator holding NAN passed twice is true, though NAN never matches NAN.
     */
    public function testRepeatedIteratorWithNanIsTrue(): void
    {
        // Given
        $iterator = new ArrayIteratorFixture([NAN]);

        // When
        $result = Summary::same($iterator, $iterator);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test Separate iterators holding NAN are not the same, since NAN never matches NAN.
     */
    public function testSeparateIteratorsWithNanAreNotSame(): void
    {
        // Given
        $lhs = new ArrayIteratorFixture([NAN]);
        $rhs = new ArrayIteratorFixture([NAN]);

        // When
        $result = Summary::same($lhs, $rhs);

        // Then
        $this->assertFalse($result);
    }
}
