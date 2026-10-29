<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\Zip;

use IterTools\Multi;
use IterTools\Stream;
use IterTools\Tests\Fixture;
use IterTools\Transform;

class TraversableTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         zip with two traversable objects of the same size
     * @dataProvider dataProviderForZipTwoTraversableSameSize
     * @param        \Traversable $iter1
     * @param        \Traversable $iter2
     * @param        array     $expected
     */
    public function testZipTwoIteratorSameSize(\Traversable $iter1, \Traversable $iter2, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::zip($iter1, $iter2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public static function dataProviderForZipTwoTraversableSameSize(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([]),
                new Fixture\IteratorAggregateFixture([]),
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([1]),
                new Fixture\IteratorAggregateFixture([2]),
                [[1, 2]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2]),
                new Fixture\IteratorAggregateFixture([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                new Fixture\IteratorAggregateFixture([4, 5, 6]),
                [[1, 4], [2, 5], [3, 6]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                new Fixture\IteratorAggregateFixture([4, 5, 6, 7, 8, 9, 1, 2, 3]),
                [[1, 4], [2, 5], [3, 6], [4, 7], [5, 8], [6, 9], [7, 1], [8, 2], [9, 3]],
            ],
        ];
    }

    /**
     * @test         zip with two traversable objects of the different sizes
     * @dataProvider dataProviderForZipTwoTraversableDifferentSize
     * @param        \Traversable $iter1
     * @param        \Traversable $iter2
     * @param        array     $expected
     */
    public function testZipTwoTraversableDifferentSize(\Traversable $iter1, \Traversable $iter2, array $expected): void
    {
        // Given
        $result = [];

        foreach (Multi::zip($iter1, $iter2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public static function dataProviderForZipTwoTraversableDifferentSize(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([1]),
                new Fixture\IteratorAggregateFixture([]),
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([]),
                new Fixture\IteratorAggregateFixture([2]),
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2]),
                new Fixture\IteratorAggregateFixture([4]),
                [[1, 4]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1]),
                new Fixture\IteratorAggregateFixture([4, 5]),
                [[1, 4]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                new Fixture\IteratorAggregateFixture([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2]),
                new Fixture\IteratorAggregateFixture([4, 5, 6]),
                [[1, 4], [2, 5]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                new Fixture\IteratorAggregateFixture([4]),
                [[1, 4]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1]),
                new Fixture\IteratorAggregateFixture([4, 5, 6]),
                [[1, 4]],
            ],
        ];
    }

    /**
     * @test zip reads a stream passed more than once as independent copies of it
     */
    public function testZipStreamPassedTwice(): void
    {
        // Given
        $stream = Stream::of(Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4]));

        // When
        $result = \iterator_to_array(Multi::zip($stream, $stream), false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3], [4, 4]], $result);
    }

    /**
     * @test zip reads a stream passed around a longer iterable as independent copies of it
     */
    public function testZipStreamPassedTwiceAroundLongerIterable(): void
    {
        // Given
        $stream = Stream::of(Fixture\GeneratorFixture::getGenerator([1, 2]));

        // When
        $result = \iterator_to_array(Multi::zip($stream, [4, 5, 6], $stream), false);

        // Then
        $this->assertSame([[1, 4, 1], [2, 5, 2]], $result);
    }

    /**
     * @test zip reads nested aggregates of one generator passed more than once as independent copies of it
     */
    public function testZipNestedAggregatePassedTwice(): void
    {
        // Given
        $aggregate = new Fixture\WrappingIteratorAggregateFixture(
            new Fixture\WrappingIteratorAggregateFixture(Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4]))
        );

        // When
        $result = \iterator_to_array(Multi::zip($aggregate, $aggregate), false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3], [4, 4]], $result);
    }

    /**
     * @test zip reads two aggregates that return the same generator as independent copies of it
     */
    public function testZipAggregatesReturningSameGenerator(): void
    {
        // Given
        $generator  = Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4]);
        $aggregate1 = new Fixture\WrappingIteratorAggregateFixture($generator);
        $aggregate2 = new Fixture\WrappingIteratorAggregateFixture(new Fixture\WrappingIteratorAggregateFixture($generator));

        // When
        $result = \iterator_to_array(Multi::zip($aggregate1, $aggregate2), false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3], [4, 4]], $result);
    }

    /**
     * @test zip reads a stream and the generator it holds as independent copies of the generator
     */
    public function testZipStreamAndItsGenerator(): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4]);
        $stream    = Stream::of($generator);

        // When
        $result = \iterator_to_array(Multi::zip($stream, $generator), false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3], [4, 4]], $result);
    }

    /**
     * @test         zip reads an aggregate that gives a fresh iterator per call, passed twice, independently
     * @dataProvider dataProviderForZipReiterableAggregatePassedTwice
     * @param        \IteratorAggregate $aggregate
     */
    public function testZipReiterableAggregatePassedTwice(\IteratorAggregate $aggregate): void
    {
        // When
        $result = \iterator_to_array(Multi::zip($aggregate, $aggregate), false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3], [4, 4]], $result);
    }

    /**
     * @return array
     */
    public static function dataProviderForZipReiterableAggregatePassedTwice(): array
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
