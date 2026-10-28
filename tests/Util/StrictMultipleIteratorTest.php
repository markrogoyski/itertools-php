<?php

declare(strict_types=1);

namespace IterTools\Tests\Util;

use IterTools\Tests\Fixture;
use IterTools\Util\Iterators\StrictMultipleIterator;

class StrictMultipleIteratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test valid - empty
     */
    public function testValidEmpty(): void
    {
        // Given
        $iterator = new StrictMultipleIterator();

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertFalse($isValid);
    }

    /**
     * @test valid - one empty array
     */
    public function testValidOneEmptyArray(): void
    {
        // Given
        $iterator = new StrictMultipleIterator([]);

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertFalse($isValid);
    }

    /**
     * @test valid - one non-empty array, single element
     */
    public function testValidOneArraySingleElement(): void
    {
        // Given
        $iterator = new StrictMultipleIterator([0]);

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertTrue($isValid);
    }

    /**
     * @test valid - one non-empty array
     */
    public function testValidOneArray(): void
    {
        // Given
        $iterator = new StrictMultipleIterator([1, 2, 3]);

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertTrue($isValid);
    }

    /**
     * @test valid - two non-empty arrays, single elements
     */
    public function testValidTwoEqualArraysSingleElements(): void
    {
        // Given
        $iterator = new StrictMultipleIterator([1], [0]);

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertTrue($isValid);
    }

    /**
     * @test valid - two non-empty arrays
     */
    public function testValidTwoEqualArrays(): void
    {
        // Given
        $iterator = new StrictMultipleIterator([1, 2, 3], [3, 4, 5]);

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertTrue($isValid);
    }

    /**
     * @test valid - three non-empty arrays, single elements
     */
    public function testValidThreeEqualArraysSingleElements(): void
    {
        // Given
        $iterator = new StrictMultipleIterator([1], [3], [5]);

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertTrue($isValid);
    }

    /**
     * @test valid - three non-empty arrays
     */
    public function testValidThreeEqualArrays(): void
    {
        // Given
        $iterator = new StrictMultipleIterator([1, 2, 3], [3, 4, 5], [5, 6, 7]);

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertTrue($isValid);
    }

    /**
     * @test valid - two unequal arrays - first has fewer items
     */
    public function testValidTwoUnequalArraysFirstSmaller(): void
    {
        // Given
        $iterator = new StrictMultipleIterator([1, 2], [3, 4, 5]);

        // And
        $iterator->next();
        $iterator->next();

        // Then
        $this->expectException(\LengthException::class);

        // When
        $iterator->valid();
    }

    /**
     * @test valid - two unequal arrays - second has fewer items
     */
    public function testValidTwoUnequalArraysSecondSmaller(): void
    {
        // Given
        $iterator = new StrictMultipleIterator([1, 2, 3], [3, 4]);

        // And
        $iterator->next();
        $iterator->next();

        // Then
        $this->expectException(\LengthException::class);

        // When
        $iterator->valid();
    }

    /**
     * @test valid - three unequal arrays - first has fewer items
     */
    public function testValidThreeUnequalArraysFirstSmaller(): void
    {
        // Given
        $iterator = new StrictMultipleIterator([0, 1, 2], [0, 3, 4, 5], [0, 5, 6, 7]);

        // And
        $iterator->next();
        $iterator->next();
        $iterator->next();

        // Then
        $this->expectException(\LengthException::class);

        // When
        $iterator->valid();
    }

    /**
     * @test valid - three unequal arrays - second has fewer items
     */
    public function testValidThreeUnequalArraysSecondSmaller(): void
    {
        // Given
        $iterator = new StrictMultipleIterator([0, 1, 2, 3], [0, 3, 4], [0, 5, 6, 7]);

        // And
        $iterator->next();
        $iterator->next();
        $iterator->next();

        // Then
        $this->expectException(\LengthException::class);

        // When
        $iterator->valid();
    }

    /**
     * @test valid - three unequal arrays - third has fewer items
     */
    public function testValidThreeUnequalArraysThirdSmaller(): void
    {
        // Given
        $iterator = new StrictMultipleIterator([0, 1, 2, 3], [0, 3, 4, 5], [0, 5, 6]);

        // And
        $iterator->next();
        $iterator->next();
        $iterator->next();

        // Then
        $this->expectException(\LengthException::class);

        // When
        $iterator->valid();
    }

    /**
     * @test         iteration over iterables of equal lengths, keyed by step
     * @dataProvider dataProviderForIterationEqualLengths
     * @param        array $iterables
     * @param        array $expected
     */
    public function testIterationEqualLengths(array $iterables, array $expected): void
    {
        // Given
        $iterator = new StrictMultipleIterator(...$iterables);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @return array
     */
    public static function dataProviderForIterationEqualLengths(): array
    {
        return [
            [
                [[], []],
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
                [
                    new Fixture\ArrayIteratorFixture([1, 2]),
                    Fixture\GeneratorFixture::getGenerator([4, 5]),
                    new Fixture\IteratorAggregateFixture(['a', 'b']),
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
        $iterator = new StrictMultipleIterator($source, $source);

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
        $iterator = new StrictMultipleIterator($source, $source);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3]], $result);
    }

    /**
     * @test an iterator passed more than once around an iterable of equal length
     */
    public function testIteratorPassedTwiceAroundIterableOfEqualLength(): void
    {
        // Given
        $source   = new Fixture\ArrayIteratorFixture([1, 2]);
        $iterator = new StrictMultipleIterator($source, ['a', 'b'], $source);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertSame([[1, 'a', 1], [2, 'b', 2]], $result);
    }

    /**
     * @test         an iterator passed more than once around an iterable of different length throws after the common steps
     * @dataProvider dataProviderForIteratorPassedTwiceAroundIterableOfDifferentLength
     * @param        array $other
     * @param        array $expected
     */
    public function testIteratorPassedTwiceAroundIterableOfDifferentLength(array $other, array $expected): void
    {
        // Given
        $source   = new Fixture\ArrayIteratorFixture([1, 2]);
        $iterator = new StrictMultipleIterator($source, $other, $source);
        $result   = [];

        // When
        try {
            foreach ($iterator as $values) {
                $result[] = $values;
            }

            $this->fail();
        } catch (\LengthException $e) {
            // Then
            $this->assertSame($expected, $result);
        }
    }

    /**
     * @return array
     */
    public static function dataProviderForIteratorPassedTwiceAroundIterableOfDifferentLength(): array
    {
        return [
            [
                [],
                [],
            ],
            [
                ['a'],
                [[1, 'a', 1]],
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
        $iterator = new StrictMultipleIterator($source, ['a', 'b', 'c'], $source);

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
        $iterator = new StrictMultipleIterator($source, ['a', 'b'], $source);

        // And
        \iterator_to_array($iterator);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertSame([[1, 'a', 1], [2, 'b', 2]], $result);
    }
}
