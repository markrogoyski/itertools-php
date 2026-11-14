<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Stream;
use IterTools\Summary;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;
use IterTools\Tests\Fixture\MisreportingCountableFixture;
use IterTools\Tests\Fixture\RecordingIteratorFixture;
use IterTools\Tests\Fixture\SinglePassMisreportingCountableFixture;

class SameCountTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testEmpty(): void
    {
        // When
        $result = Summary::sameCount();

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
        $result = Summary::sameCount(...$iterables);

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

            [[1], [2]],
            [[1], $gen([2])],
            [[1], $iter([2])],
            [[1], $trav([2])],

            [[1], [2], [3]],
            [[1], $gen([2]), [3]],
            [[1], $iter([2]), [3]],
            [[1], $trav([2]), [3]],
            [[1], [2], $gen([3])],
            [[1], [2], $iter([3])],
            [[1], [2], $trav([3])],
            [[1], $gen([2]), $iter([3])],
            [[1], $iter([2]), $trav([3])],

            [[1, 2], [2, 3], [3, 4], [4, 5]],
            [[1, 2], [2, 3], $gen([3, 4]), [4, 5]],
            [[1, 2], [2, 3], $iter([3, 4]), [4, 5]],
            [[1, 2], [2, 3], $trav([3, 4]), [4, 5]],
            [[1, 2], [2, 3], $gen([3, 4]), $iter([4, 5])],
            [[1, 2], [2, 3], $iter([3, 4]), $trav([4, 5])],

            [[1, 2, 3], [2, 3, 4], [3, 4, 5], [4, 5, 6]],
            [[1, 2, 3], [2, 3, 4], $gen([3, 4, 5]), [4, 5, 6]],
            [[1, 2, 3], [2, 3, 4], $iter([3, 4, 5]), [4, 5, 6]],
            [[1, 2, 3], [2, 3, 4], $trav([3, 4, 5]), [4, 5, 6]],

            [['a', 2], ['b', 2], ['c', 2], ['c', 2]],
            [['a', 2], $gen(['b', 2]), ['c', 2], ['c', 2]],
            [['a', 2], $iter(['b', 2]), ['c', 2], ['c', 2]],
            [['a', 2], $trav(['b', 2]), ['c', 2], ['c', 2]],

            [[1, null], [2, null], [1, null], [2, null]],
            [[1, null], [2, null], [1, null], $gen([2, null])],
            [[1, null], [2, null], [1, null], $iter([2, null])],
            [[1, null], [2, null], [1, null], $trav([2, null])],
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
        $result = Summary::sameCount(...$iterables);

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

            [[1], [1, 2, 3]],
            [[1], $gen([1, 2, 3])],
            [[1], $iter([1, 2, 3])],
            [[1], $trav([1, 2, 3])],
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
        $result = Summary::sameCount(...$iterables);

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
            [$gen([])],
            [$gen([1])],
            [$gen([1, 2, 3])],
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

            [$gen([1]), [2]],
            [$gen([1]), $gen([2])],
            [$gen([1]), $iter([2])],
            [$gen([1]), $trav([2])],

            [$gen([1]), [2], [3]],
            [$gen([1]), $gen([2]), [3]],
            [$gen([1]), $iter([2]), [3]],
            [$gen([1]), $trav([2]), [3]],
            [$gen([1]), [2], $gen([3])],
            [$gen([1]), [2], $iter([3])],
            [$gen([1]), [2], $trav([3])],
            [$gen([1]), $gen([2]), $iter([3])],
            [$gen([1]), $iter([2]), $trav([3])],

            [$gen([1, 2]), [2, 3], [3, 4], [4, 5]],
            [$gen([1, 2]), [2, 3], $gen([3, 4]), [4, 5]],
            [$gen([1, 2]), [2, 3], $iter([3, 4]), [4, 5]],
            [$gen([1, 2]), [2, 3], $trav([3, 4]), [4, 5]],
            [$gen([1, 2]), [2, 3], $gen([3, 4]), $iter([4, 5])],
            [$gen([1, 2]), [2, 3], $iter([3, 4]), $trav([4, 5])],

            [$gen([1, 2, 3]), [2, 3, 4], [3, 4, 5], [4, 5, 6]],
            [$gen([1, 2, 3]), [2, 3, 4], $gen([3, 4, 5]), [4, 5, 6]],
            [$gen([1, 2, 3]), [2, 3, 4], $iter([3, 4, 5]), [4, 5, 6]],
            [$gen([1, 2, 3]), [2, 3, 4], $trav([3, 4, 5]), [4, 5, 6]],

            [$gen(['a', 2]), ['b', 2], ['c', 2], ['c', 2]],
            [$gen(['a', 2]), $gen(['b', 2]), ['c', 2], ['c', 2]],
            [$gen(['a', 2]), $iter(['b', 2]), ['c', 2], ['c', 2]],
            [$gen(['a', 2]), $trav(['b', 2]), ['c', 2], ['c', 2]],

            [$gen([1, null]), [2, null], [1, null], [2, null]],
            [$gen([1, null]), [2, null], [1, null], $gen([2, null])],
            [$gen([1, null]), [2, null], [1, null], $iter([2, null])],
            [$gen([1, null]), [2, null], [1, null], $trav([2, null])],
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
        $result = Summary::sameCount(...$iterables);

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

            [$gen([1]), [1, 2, 3]],
            [$gen([1]), $gen([1, 2, 3])],
            [$gen([1]), $iter([1, 2, 3])],
            [$gen([1]), $trav([1, 2, 3])],
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
        $result = Summary::sameCount(...$iterables);

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
            [$iter([])],
            [$iter([1])],
            [$iter([1, 2, 3])],
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

            [$iter([1]), [2]],
            [$iter([1]), $gen([2])],
            [$iter([1]), $iter([2])],
            [$iter([1]), $trav([2])],

            [$iter([1]), [2], [3]],
            [$iter([1]), $gen([2]), [3]],
            [$iter([1]), $iter([2]), [3]],
            [$iter([1]), $trav([2]), [3]],
            [$iter([1]), [2], $gen([3])],
            [$iter([1]), [2], $iter([3])],
            [$iter([1]), [2], $trav([3])],
            [$iter([1]), $gen([2]), $iter([3])],
            [$iter([1]), $iter([2]), $trav([3])],

            [$iter([1, 2]), [2, 3], [3, 4], [4, 5]],
            [$iter([1, 2]), [2, 3], $gen([3, 4]), [4, 5]],
            [$iter([1, 2]), [2, 3], $iter([3, 4]), [4, 5]],
            [$iter([1, 2]), [2, 3], $trav([3, 4]), [4, 5]],
            [$iter([1, 2]), [2, 3], $gen([3, 4]), $iter([4, 5])],
            [$iter([1, 2]), [2, 3], $iter([3, 4]), $trav([4, 5])],

            [$iter([1, 2, 3]), [2, 3, 4], [3, 4, 5], [4, 5, 6]],
            [$iter([1, 2, 3]), [2, 3, 4], $gen([3, 4, 5]), [4, 5, 6]],
            [$iter([1, 2, 3]), [2, 3, 4], $iter([3, 4, 5]), [4, 5, 6]],
            [$iter([1, 2, 3]), [2, 3, 4], $trav([3, 4, 5]), [4, 5, 6]],

            [$iter(['a', 2]), ['b', 2], ['c', 2], ['c', 2]],
            [$iter(['a', 2]), $gen(['b', 2]), ['c', 2], ['c', 2]],
            [$iter(['a', 2]), $iter(['b', 2]), ['c', 2], ['c', 2]],
            [$iter(['a', 2]), $trav(['b', 2]), ['c', 2], ['c', 2]],

            [$iter([1, null]), [2, null], [1, null], [2, null]],
            [$iter([1, null]), [2, null], [1, null], $gen([2, null])],
            [$iter([1, null]), [2, null], [1, null], $iter([2, null])],
            [$iter([1, null]), [2, null], [1, null], $trav([2, null])],
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
        $result = Summary::sameCount(...$iterables);

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

            [$iter([1]), [1, 2, 3]],
            [$iter([1]), $gen([1, 2, 3])],
            [$iter([1]), $iter([1, 2, 3])],
            [$iter([1]), $trav([1, 2, 3])],
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
        $result = Summary::sameCount(...$iterables);

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
            [$trav([])],
            [$trav([1])],
            [$trav([1, 2, 3])],
            [$trav([]), []],
            [$trav([]), $gen([])],
            [$trav([]), $iter([])],
            [$trav([]), $trav([])],

            [$trav([]), [], []],
            [$trav([]), $gen([]), []],
            [$trav([]), $iter([]), []],
            [$trav([]), $trav([]), []],
            [$trav([]), [], $gen([])],
            [$trav([]), [], $iter([])],
            [$trav([]), [], $trav([])],
            [$trav([]), $gen([]), $iter([])],
            [$trav([]), $iter([]), $trav([])],

            [$trav([]), [], [], [], []],
            [$trav([]), $gen([]), $iter([]), $trav([]), []],
            [$trav([]), [], $gen([]), $iter([]), $trav([])],

            [$trav([1]), [2]],
            [$trav([1]), $gen([2])],
            [$trav([1]), $iter([2])],
            [$trav([1]), $trav([2])],

            [$trav([1]), [2], [3]],
            [$trav([1]), $gen([2]), [3]],
            [$trav([1]), $iter([2]), [3]],
            [$trav([1]), $trav([2]), [3]],
            [$trav([1]), [2], $gen([3])],
            [$trav([1]), [2], $iter([3])],
            [$trav([1]), [2], $trav([3])],
            [$trav([1]), $gen([2]), $iter([3])],
            [$trav([1]), $iter([2]), $trav([3])],

            [$trav([1, 2]), [2, 3], [3, 4], [4, 5]],
            [$trav([1, 2]), [2, 3], $gen([3, 4]), [4, 5]],
            [$trav([1, 2]), [2, 3], $iter([3, 4]), [4, 5]],
            [$trav([1, 2]), [2, 3], $trav([3, 4]), [4, 5]],
            [$trav([1, 2]), [2, 3], $gen([3, 4]), $iter([4, 5])],
            [$trav([1, 2]), [2, 3], $iter([3, 4]), $trav([4, 5])],

            [$trav([1, 2, 3]), [2, 3, 4], [3, 4, 5], [4, 5, 6]],
            [$trav([1, 2, 3]), [2, 3, 4], $gen([3, 4, 5]), [4, 5, 6]],
            [$trav([1, 2, 3]), [2, 3, 4], $iter([3, 4, 5]), [4, 5, 6]],
            [$trav([1, 2, 3]), [2, 3, 4], $trav([3, 4, 5]), [4, 5, 6]],

            [$trav(['a', 2]), ['b', 2], ['c', 2], ['c', 2]],
            [$trav(['a', 2]), $gen(['b', 2]), ['c', 2], ['c', 2]],
            [$trav(['a', 2]), $iter(['b', 2]), ['c', 2], ['c', 2]],
            [$trav(['a', 2]), $trav(['b', 2]), ['c', 2], ['c', 2]],

            [$trav([1, null]), [2, null], [1, null], [2, null]],
            [$trav([1, null]), [2, null], [1, null], $gen([2, null])],
            [$trav([1, null]), [2, null], [1, null], $iter([2, null])],
            [$trav([1, null]), [2, null], [1, null], $trav([2, null])],
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
        $result = Summary::sameCount(...$iterables);

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

            [$trav([1]), [1, 2, 3]],
            [$trav([1]), $gen([1, 2, 3])],
            [$trav([1]), $iter([1, 2, 3])],
            [$trav([1]), $trav([1, 2, 3])],
        ];
    }

    /**
     * @test A generator passed twice has the same count as itself.
     */
    public function testRepeatedGenerator(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2, 3]);

        // When
        $result = Summary::sameCount($generator, $generator);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test An iterator passed twice has the same count as itself and is read once.
     */
    public function testRepeatedIteratorIsReadOnce(): void
    {
        // Given
        $pulled   = [];
        $iterator = new RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = Summary::sameCount($iterator, $iterator);

        // Then
        $this->assertTrue($result);
        $this->assertSame([1, 2, 3], $pulled);
    }

    /**
     * @test A stream of a generator passed twice has the same count as itself.
     */
    public function testRepeatedStream(): void
    {
        // Given
        $stream = Stream::of(GeneratorFixture::getGenerator([1, 2, 3]));

        // When
        $result = Summary::sameCount($stream, $stream);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test A stream of an iterator and that iterator have the same count and the iterator is read once.
     */
    public function testStreamAndItsIteratorAreReadOnce(): void
    {
        // Given
        $pulled   = [];
        $iterator = new RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = Summary::sameCount(Stream::of($iterator), $iterator);

        // Then
        $this->assertTrue($result);
        $this->assertSame([1, 2, 3], $pulled);
    }

    /**
     * @test         A generator passed more than once compared with iterables of its count
     * @dataProvider dataProviderForRepeatedGeneratorTrue
     * @param        \Generator $generator
     * @param        iterable   $other
     */
    public function testRepeatedGeneratorWithOtherTrue(\Generator $generator, iterable $other): void
    {
        // When
        $result = Summary::sameCount($generator, $other, $generator);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForRepeatedGeneratorTrue(): array
    {
        return [
            [GeneratorFixture::getGenerator([]), []],
            [GeneratorFixture::getGenerator([]), GeneratorFixture::getGenerator([])],
            [GeneratorFixture::getGenerator([1, 2, 3]), [4, 5, 6]],
            [GeneratorFixture::getGenerator([1, 2, 3]), GeneratorFixture::getGenerator([4, 5, 6])],
            [GeneratorFixture::getGenerator([1, 2, 3]), new ArrayIteratorFixture([4, 5, 6])],
            [GeneratorFixture::getGenerator([1, 2, 3]), new IteratorAggregateFixture([4, 5, 6])],
        ];
    }

    /**
     * @test         A generator passed more than once compared with an iterable of a different count
     * @dataProvider dataProviderForRepeatedGeneratorFalse
     * @param        \Generator $generator
     * @param        iterable   $other
     */
    public function testRepeatedGeneratorWithOtherFalse(\Generator $generator, iterable $other): void
    {
        // When
        $result = Summary::sameCount($generator, $other, $generator);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForRepeatedGeneratorFalse(): array
    {
        return [
            [GeneratorFixture::getGenerator([]), [1]],
            [GeneratorFixture::getGenerator([1, 2, 3]), []],
            [GeneratorFixture::getGenerator([1, 2, 3]), [4, 5]],
            [GeneratorFixture::getGenerator([1, 2, 3]), [4, 5, 6, 7]],
            [GeneratorFixture::getGenerator([1, 2, 3]), GeneratorFixture::getGenerator([4, 5])],
            [GeneratorFixture::getGenerator([1, 2, 3]), new ArrayIteratorFixture([4, 5, 6, 7])],
            [GeneratorFixture::getGenerator([1, 2, 3]), new IteratorAggregateFixture([4, 5])],
        ];
    }

    /**
     * @test Arrays and generators each passed more than once, interleaved, have the same count.
     */
    public function testRepeatedArrayAndGeneratorInterleaved(): void
    {
        // Given
        $array     = [1, 2, 3];
        $generator = GeneratorFixture::getGenerator([4, 5, 6]);

        // When
        $result = Summary::sameCount($array, $generator, $array, $generator, [7, 8, 9]);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test A Countable is counted by count() without being traversed, passed once or more than once.
     */
    public function testCountableIsNotTraversed(): void
    {
        // Given
        $countable = new MisreportingCountableFixture([1, 2], 3);

        // When
        $result = Summary::sameCount($countable, [1, 2, 3], $countable);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test A single-pass Countable passed twice beside a repeated generator is counted, not traversed.
     */
    public function testRepeatedSinglePassCountableAndRepeatedGenerator(): void
    {
        // Given
        $countable = new SinglePassMisreportingCountableFixture([1, 2], 3);
        $generator = GeneratorFixture::getGenerator([4, 5, 6]);

        // When
        $result = Summary::sameCount($countable, $generator, $countable, $generator);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test A file object, which reads its lines only when its current line is read, is counted by its lines.
     */
    public function testSplFileObjectTrue(): void
    {
        // Given
        $file = new \SplTempFileObject();
        $file->fwrite("a\nb\nc");

        // When
        $result = Summary::sameCount($file, [1, 2, 3]);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test A file object compared with an iterable of a different count
     */
    public function testSplFileObjectFalse(): void
    {
        // Given
        $file = new \SplTempFileObject();
        $file->fwrite("a\nb\nc");

        // When
        $result = Summary::sameCount($file, [1, 2]);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test A file object passed twice has the same count as itself and as an iterable of its line count.
     */
    public function testRepeatedSplFileObject(): void
    {
        // Given
        $file = new \SplTempFileObject();
        $file->fwrite("a\nb\nc");

        // When
        $result = Summary::sameCount($file, [1, 2, 3], $file);

        // Then
        $this->assertTrue($result);
    }
}
