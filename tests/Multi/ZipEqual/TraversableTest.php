<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\ZipEqual;

use IterTools\Multi;
use IterTools\Stream;
use IterTools\Tests\Fixture;
use IterTools\Transform;

class TraversableTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         zip with two traversable objects of the same size
     * @dataProvider dataProviderForZipStrictTwoTraversableSameSize
     * @param        \Traversable $iter1
     * @param        \Traversable $iter2
     * @param        array     $expected
     */
    public function testZipStrictTwoIteratorSameSize(\Traversable $iter1, \Traversable $iter2, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::zipEqual($iter1, $iter2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public static function dataProviderForZipStrictTwoTraversableSameSize(): array
    {
        $trav = static function (array $input) {
            return new Fixture\IteratorAggregateFixture($input);
        };

        return [
            [
                $trav([]),
                $trav([]),
                [],
            ],
            [
                $trav([1]),
                $trav([2]),
                [[1, 2]],
            ],
            [
                $trav([1, 2]),
                $trav([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                $trav([1, 2, 3]),
                $trav([4, 5, 6]),
                [[1, 4], [2, 5], [3, 6]],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                $trav([4, 5, 6, 7, 8, 9, 1, 2, 3]),
                [[1, 4], [2, 5], [3, 6], [4, 7], [5, 8], [6, 9], [7, 1], [8, 2], [9, 3]],
            ],
            [
                $trav(['a', 'b', 'c', 'd']),
                $trav(['one', 'two', 'three', 'four']),
                [['a', 'one'], ['b', 'two'], ['c', 'three'], ['d', 'four']],
            ],
        ];
    }

    /**
     * @test         zip with two traversable objects of the different sizes
     * @dataProvider dataProviderForZipStrictTwoTraversableDifferentSize
     * @param        \Traversable $iter1
     * @param        \Traversable $iter2
     * @param        array     $expected
     */
    public function testZipStrictTwoTraversableDifferentSize(
        \Traversable $iter1,
        \Traversable $iter2,
        array $expected
    ): void {
        // Given
        $result = [];

        // When
        try {
            foreach (Multi::zipEqual($iter1, $iter2) as [$value1, $value2]) {
                $result[] = [$value1, $value2];
            }

            $this->fail();
        } catch (\LengthException $e) {
            // Then
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @return array
     */
    public static function dataProviderForZipStrictTwoTraversableDifferentSize(): array
    {
        $trav = static function (array $input) {
            return new Fixture\IteratorAggregateFixture($input);
        };

        return [
            [
                $trav([1]),
                $trav([]),
                [],
            ],
            [
                $trav([]),
                $trav([2]),
                [],
            ],
            [
                $trav([1, 2]),
                $trav([4]),
                [[1, 4]],
            ],
            [
                $trav([1]),
                $trav([4, 5]),
                [[1, 4]],
            ],
            [
                $trav([1, 2, 3]),
                $trav([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                $trav([1, 2]),
                $trav([4, 5, 6]),
                [[1, 4], [2, 5]],
            ],
            [
                $trav([1, 2, 3]),
                $trav([4]),
                [[1, 4]],
            ],
            [
                $trav([1]),
                $trav([4, 5, 6]),
                [[1, 4]],
            ],
        ];
    }

    /**
     * @test zipEqual reads a stream passed more than once as independent copies of it
     */
    public function testZipEqualStreamPassedTwice(): void
    {
        // Given
        $stream = Stream::of(Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4]));

        // When
        $result = \iterator_to_array(Multi::zipEqual($stream, $stream), false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3], [4, 4]], $result);
    }

    /**
     * @test zipEqual reads a stream passed around an equal-length iterable as independent copies of it
     */
    public function testZipEqualStreamPassedTwiceAroundEqualLengthIterable(): void
    {
        // Given
        $stream = Stream::of(Fixture\GeneratorFixture::getGenerator([1, 2]));

        // When
        $result = \iterator_to_array(Multi::zipEqual($stream, [4, 5], $stream), false);

        // Then
        $this->assertSame([[1, 4, 1], [2, 5, 2]], $result);
    }

    /**
     * @test zipEqual throws when a stream passed more than once and another iterable differ in length
     */
    public function testZipEqualStreamPassedTwiceAroundIterableOfDifferentLength(): void
    {
        // Given
        $stream = Stream::of(Fixture\GeneratorFixture::getGenerator([1, 2]));
        $result = [];

        // When
        try {
            foreach (Multi::zipEqual($stream, [4, 5, 6], $stream) as $values) {
                $result[] = $values;
            }

            $this->fail();
        } catch (\LengthException $e) {
            // Then
            $this->assertSame([[1, 4, 1], [2, 5, 2]], $result);
        }
    }

    /**
     * @test zipEqual reads nested aggregates of one generator passed more than once as independent copies of it
     */
    public function testZipEqualNestedAggregatePassedTwice(): void
    {
        // Given
        $aggregate = new Fixture\WrappingIteratorAggregateFixture(
            new Fixture\WrappingIteratorAggregateFixture(Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4]))
        );

        // When
        $result = \iterator_to_array(Multi::zipEqual($aggregate, $aggregate), false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3], [4, 4]], $result);
    }

    /**
     * @test zipEqual reads two aggregates that return the same generator as independent copies of it
     */
    public function testZipEqualAggregatesReturningSameGenerator(): void
    {
        // Given
        $generator  = Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4]);
        $aggregate1 = new Fixture\WrappingIteratorAggregateFixture($generator);
        $aggregate2 = new Fixture\WrappingIteratorAggregateFixture(new Fixture\WrappingIteratorAggregateFixture($generator));

        // When
        $result = \iterator_to_array(Multi::zipEqual($aggregate1, $aggregate2), false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3], [4, 4]], $result);
    }

    /**
     * @test zipEqual reads a stream and the generator it holds as independent copies of the generator
     */
    public function testZipEqualStreamAndItsGenerator(): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4]);
        $stream    = Stream::of($generator);

        // When
        $result = \iterator_to_array(Multi::zipEqual($stream, $generator), false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3], [4, 4]], $result);
    }

    /**
     * @test         zipEqual reads an aggregate that gives a fresh iterator per call, passed twice, independently
     * @dataProvider dataProviderForZipEqualReiterableAggregatePassedTwice
     * @param        \IteratorAggregate $aggregate
     */
    public function testZipEqualReiterableAggregatePassedTwice(\IteratorAggregate $aggregate): void
    {
        // When
        $result = \iterator_to_array(Multi::zipEqual($aggregate, $aggregate), false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3], [4, 4]], $result);
    }

    /**
     * @return array
     */
    public static function dataProviderForZipEqualReiterableAggregatePassedTwice(): array
    {
        return [
            [new \ArrayObject([1, 2, 3, 4])],
            [new Fixture\IteratorAggregateFixture([1, 2, 3, 4])],
            [Transform::memoize([1, 2, 3, 4])],
            [Transform::memoize(Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4]))],
            [Stream::of([1, 2, 3, 4])],
        ];
    }
}
