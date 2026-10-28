<?php

declare(strict_types=1);

namespace IterTools\Tests\Util\Iterators;

use IterTools\Tests\Fixture;
use IterTools\Util\Iterators\ShortestMultipleIterator;

class ShortestMultipleIteratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test valid - no iterables
     */
    public function testValidNoIterables(): void
    {
        // Given
        $iterator = new ShortestMultipleIterator();

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertFalse($isValid);
    }

    /**
     * @test         iteration stops when the shortest iterable is exhausted, keyed by step
     * @dataProvider dataProviderForIteration
     * @param        array $iterables
     * @param        array $expected
     */
    public function testIteration(array $iterables, array $expected): void
    {
        // Given
        $iterator = new ShortestMultipleIterator(...$iterables);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @return array
     */
    public static function dataProviderForIteration(): array
    {
        return [
            [
                [[]],
                [],
            ],
            [
                [[1, 2, 3]],
                [[1], [2], [3]],
            ],
            [
                [[1, 2, 3], [4, 5, 6]],
                [[1, 4], [2, 5], [3, 6]],
            ],
            [
                [[1, 2, 3], [4]],
                [[1, 4]],
            ],
            [
                [[1], [4, 5, 6]],
                [[1, 4]],
            ],
            [
                [[1, 2], [], [3, 4]],
                [],
            ],
            [
                [[1, 2, 3], [4, 5], ['a', 'b', 'c', 'd']],
                [[1, 4, 'a'], [2, 5, 'b']],
            ],
            [
                [
                    new Fixture\ArrayIteratorFixture([1, 2, 3]),
                    Fixture\GeneratorFixture::getGenerator([4, 5]),
                    new Fixture\IteratorAggregateFixture(['a', 'b', 'c']),
                ],
                [[1, 4, 'a'], [2, 5, 'b']],
            ],
        ];
    }

    /**
     * @test an iterator passed more than once is read as independent copies of it
     */
    public function testIteratorPassedTwice(): void
    {
        // Given
        $source   = new Fixture\ArrayIteratorFixture([1, 2, 3]);
        $iterator = new ShortestMultipleIterator($source, $source);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3]], $result);
    }

    /**
     * @test a generator passed more than once is read as independent copies of it
     */
    public function testGeneratorPassedTwice(): void
    {
        // Given
        $source   = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $iterator = new ShortestMultipleIterator($source, $source);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3]], $result);
    }

    /**
     * @test         an iterator passed more than once around another iterable stops with the shorter of them
     * @dataProvider dataProviderForIteratorPassedTwiceAroundOtherIterable
     * @param        array $other
     * @param        array $expected
     */
    public function testIteratorPassedTwiceAroundOtherIterable(array $other, array $expected): void
    {
        // Given
        $source   = new Fixture\ArrayIteratorFixture([1, 2]);
        $iterator = new ShortestMultipleIterator($source, $other, $source);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @return array
     */
    public static function dataProviderForIteratorPassedTwiceAroundOtherIterable(): array
    {
        return [
            [
                ['a'],
                [[1, 'a', 1]],
            ],
            [
                ['a', 'b'],
                [[1, 'a', 1], [2, 'b', 2]],
            ],
            [
                ['a', 'b', 'c'],
                [[1, 'a', 1], [2, 'b', 2]],
            ],
        ];
    }

    /**
     * @test an iterator passed more than once is advanced once per step
     */
    public function testIteratorPassedTwiceIsAdvancedOncePerStep(): void
    {
        // Given
        $pulled   = [];
        $source   = new Fixture\RecordingIteratorFixture([1, 2, 3], $pulled);
        $iterator = new ShortestMultipleIterator($source, ['a', 'b', 'c'], $source);

        // When
        \iterator_to_array($iterator);

        // Then
        $this->assertSame([1, 2, 3], $pulled);
    }

    /**
     * @test rewind restarts iteration from the first step
     */
    public function testRewind(): void
    {
        // Given
        $source   = new Fixture\ArrayIteratorFixture([1, 2, 3]);
        $iterator = new ShortestMultipleIterator($source, ['a', 'b'], $source);

        // And
        \iterator_to_array($iterator);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertSame([[1, 'a', 1], [2, 'b', 2]], $result);
    }
}
