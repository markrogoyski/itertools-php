<?php

declare(strict_types=1);

namespace IterTools\Tests\Util\Iterators;

use IterTools\Tests\Fixture;
use IterTools\Util\Iterators\JustifyMultipleIterator;

class JustifyMultipleIteratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test valid - no iterables
     */
    public function testValidNoIterables(): void
    {
        // Given
        $iterator = new JustifyMultipleIterator('filler');

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertFalse($isValid);
    }

    /**
     * @test         iteration continues until the longest iterable is exhausted, filling the others, keyed by step
     * @dataProvider dataProviderForIteration
     * @param        mixed $filler
     * @param        array $iterables
     * @param        array $expected
     */
    public function testIteration(mixed $filler, array $iterables, array $expected): void
    {
        // Given
        $iterator = new JustifyMultipleIterator($filler, ...$iterables);

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
                'filler',
                [[]],
                [],
            ],
            [
                'filler',
                [[1, 2, 3]],
                [[1], [2], [3]],
            ],
            [
                'filler',
                [[1, 2, 3], [4, 5, 6]],
                [[1, 4], [2, 5], [3, 6]],
            ],
            [
                'filler',
                [[1, 2, 3], [4]],
                [[1, 4], [2, 'filler'], [3, 'filler']],
            ],
            [
                null,
                [[1], [4, 5, 6]],
                [[1, 4], [null, 5], [null, 6]],
            ],
            [
                0,
                [[1, 2], [], [3, 4, 5]],
                [[1, 0, 3], [2, 0, 4], [0, 0, 5]],
            ],
            [
                'filler',
                [
                    new Fixture\ArrayIteratorFixture([1, 2, 3]),
                    Fixture\GeneratorFixture::getGenerator([4, 5]),
                    new Fixture\IteratorAggregateFixture(['a']),
                ],
                [[1, 4, 'a'], [2, 5, 'filler'], [3, 'filler', 'filler']],
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
        $iterator = new JustifyMultipleIterator('filler', $source, $source);

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
        $iterator = new JustifyMultipleIterator('filler', $source, $source);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3]], $result);
    }

    /**
     * @test         an iterator passed more than once around another iterable fills every one of its positions
     * @dataProvider dataProviderForIteratorPassedTwiceAroundOtherIterable
     * @param        array $other
     * @param        array $expected
     */
    public function testIteratorPassedTwiceAroundOtherIterable(array $other, array $expected): void
    {
        // Given
        $source   = new Fixture\ArrayIteratorFixture([1, 2]);
        $iterator = new JustifyMultipleIterator('filler', $source, $other, $source);

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
                [[1, 'a', 1], [2, 'filler', 2]],
            ],
            [
                ['a', 'b'],
                [[1, 'a', 1], [2, 'b', 2]],
            ],
            [
                ['a', 'b', 'c'],
                [[1, 'a', 1], [2, 'b', 2], ['filler', 'c', 'filler']],
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
        $iterator = new JustifyMultipleIterator('filler', $source, ['a', 'b', 'c', 'd'], $source);

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
        $source   = new Fixture\ArrayIteratorFixture([1, 2]);
        $iterator = new JustifyMultipleIterator('filler', $source, ['a', 'b', 'c'], $source);

        // And
        \iterator_to_array($iterator);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertSame([[1, 'a', 1], [2, 'b', 2], ['filler', 'c', 'filler']], $result);
    }
}
