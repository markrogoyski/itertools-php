<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\ZipFilled;

use IterTools\Multi;
use IterTools\Stream;
use IterTools\Tests\Fixture;
use IterTools\Transform;

class TraversableTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         zipFilled with two traversable objects of the same size
     * @dataProvider dataProviderForZipFilledTwoTraversableSameSize
     * @param        mixed $filler
     * @param        \Traversable $iter1
     * @param        \Traversable $iter2
     * @param        array     $expected
     */
    public function testZipFilledTwoIteratorSameSize($filler, \Traversable $iter1, \Traversable $iter2, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::zipFilled($filler, $iter1, $iter2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public static function dataProviderForZipFilledTwoTraversableSameSize(): array
    {
        return [
            [
                'test',
                new Fixture\IteratorAggregateFixture([]),
                new Fixture\IteratorAggregateFixture([]),
                [],
            ],
            [
                'test',
                new Fixture\IteratorAggregateFixture([1]),
                new Fixture\IteratorAggregateFixture([2]),
                [[1, 2]],
            ],
            [
                'test',
                new Fixture\IteratorAggregateFixture([1, 2]),
                new Fixture\IteratorAggregateFixture([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                'test',
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                new Fixture\IteratorAggregateFixture([4, 5, 6]),
                [[1, 4], [2, 5], [3, 6]],
            ],
            [
                'test',
                new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                new Fixture\IteratorAggregateFixture([4, 5, 6, 7, 8, 9, 1, 2, 3]),
                [[1, 4], [2, 5], [3, 6], [4, 7], [5, 8], [6, 9], [7, 1], [8, 2], [9, 3]],
            ],
        ];
    }

    /**
     * @test         zipFilled with two traversable objects of the different sizes
     * @dataProvider dataProviderForZipFilledTwoTraversableDifferentSize
     * @param        mixed $filler
     * @param        \Traversable $iter1
     * @param        \Traversable $iter2
     * @param        array     $expected
     */
    public function testZipFilledTwoTraversableDifferentSize($filler, \Traversable $iter1, \Traversable $iter2, array $expected): void
    {
        // Given
        $result = [];

        foreach (Multi::zipFilled($filler, $iter1, $iter2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public static function dataProviderForZipFilledTwoTraversableDifferentSize(): array
    {
        return [
            [
                null,
                new Fixture\IteratorAggregateFixture([1]),
                new Fixture\IteratorAggregateFixture([]),
                [[1, null]],
            ],
            [
                null,
                new Fixture\IteratorAggregateFixture([]),
                new Fixture\IteratorAggregateFixture([2]),
                [[null, 2]],
            ],
            [
                null,
                new Fixture\IteratorAggregateFixture([1, 2]),
                new Fixture\IteratorAggregateFixture([4]),
                [[1, 4], [2, null]],
            ],
            [
                null,
                new Fixture\IteratorAggregateFixture([1]),
                new Fixture\IteratorAggregateFixture([4, 5]),
                [[1, 4], [null, 5]],
            ],
            [
                null,
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                new Fixture\IteratorAggregateFixture([4, 5]),
                [[1, 4], [2, 5], [3, null]],
            ],
            [
                null,
                new Fixture\IteratorAggregateFixture([1, 2]),
                new Fixture\IteratorAggregateFixture([4, 5, 6]),
                [[1, 4], [2, 5], [null, 6]],
            ],
            [
                null,
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                new Fixture\IteratorAggregateFixture([4]),
                [[1, 4], [2, null], [3, null]],
            ],
            [
                null,
                new Fixture\IteratorAggregateFixture([1]),
                new Fixture\IteratorAggregateFixture([4, 5, 6]),
                [[1, 4], [null, 5], [null, 6]],
            ],
        ];
    }

    /**
     * @test zipFilled reads a stream passed more than once as independent copies of it
     */
    public function testZipFilledStreamPassedTwice(): void
    {
        // Given
        $stream = Stream::of(Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4]));

        // When
        $result = \iterator_to_array(Multi::zipFilled('x', $stream, $stream), false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3], [4, 4]], $result);
    }

    /**
     * @test zipFilled reads a stream passed around a longer iterable as independent copies of it
     */
    public function testZipFilledStreamPassedTwiceAroundLongerIterable(): void
    {
        // Given
        $stream = Stream::of(Fixture\GeneratorFixture::getGenerator([1, 2]));

        // When
        $result = \iterator_to_array(Multi::zipFilled('x', $stream, [4, 5, 6], $stream), false);

        // Then
        $this->assertSame([[1, 4, 1], [2, 5, 2], ['x', 6, 'x']], $result);
    }

    /**
     * @test zipFilled reads nested aggregates of one generator passed more than once as independent copies of it
     */
    public function testZipFilledNestedAggregatePassedTwice(): void
    {
        // Given
        $aggregate = new Fixture\WrappingIteratorAggregateFixture(
            new Fixture\WrappingIteratorAggregateFixture(Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4]))
        );

        // When
        $result = \iterator_to_array(Multi::zipFilled('x', $aggregate, $aggregate), false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3], [4, 4]], $result);
    }

    /**
     * @test zipFilled reads two aggregates that return the same generator as independent copies of it
     */
    public function testZipFilledAggregatesReturningSameGenerator(): void
    {
        // Given
        $generator  = Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4]);
        $aggregate1 = new Fixture\WrappingIteratorAggregateFixture($generator);
        $aggregate2 = new Fixture\WrappingIteratorAggregateFixture(new Fixture\WrappingIteratorAggregateFixture($generator));

        // When
        $result = \iterator_to_array(Multi::zipFilled('x', $aggregate1, $aggregate2), false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3], [4, 4]], $result);
    }

    /**
     * @test zipFilled reads a stream and the generator it holds as independent copies of the generator
     */
    public function testZipFilledStreamAndItsGenerator(): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4]);
        $stream    = Stream::of($generator);

        // When
        $result = \iterator_to_array(Multi::zipFilled('x', $stream, $generator), false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3], [4, 4]], $result);
    }

    /**
     * @test         zipFilled reads an aggregate that gives a fresh iterator per call, passed twice, independently
     * @dataProvider dataProviderForZipFilledReiterableAggregatePassedTwice
     * @param        \IteratorAggregate $aggregate
     */
    public function testZipFilledReiterableAggregatePassedTwice(\IteratorAggregate $aggregate): void
    {
        // When
        $result = \iterator_to_array(Multi::zipFilled('x', $aggregate, $aggregate), false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3], [4, 4]], $result);
    }

    /**
     * @return array
     */
    public static function dataProviderForZipFilledReiterableAggregatePassedTwice(): array
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
