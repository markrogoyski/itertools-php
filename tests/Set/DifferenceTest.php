<?php

declare(strict_types=1);

namespace IterTools\Tests\Set;

use IterTools\Set;
use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class DifferenceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArraySets
     * @dataProvider dataProviderForArrayMultisets
     * @param        iterable<mixed> $a
     * @param        array<iterable<mixed>> $iterables
     * @param        array $expected
     */
    public function testArray(iterable $a, array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::difference($a, ...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public static function dataProviderForArraySets(): array
    {
        return [
            [
                [],
                [],
                [],
            ],
            [
                [],
                [[]],
                [],
            ],
            [
                [],
                [[], []],
                [],
            ],
            [
                [1, 2, 3],
                [],
                [1, 2, 3],
            ],
            [
                [1, 2, 3],
                [[]],
                [1, 2, 3],
            ],
            [
                [],
                [[1, 2, 3]],
                [],
            ],
            [
                [2],
                [[2]],
                [],
            ],
            [
                [1, 2, 3, 4, 5],
                [[1, 2, 3, 4, 5]],
                [],
            ],
            [
                [1, 2, 3, 4, 5],
                [[3, 4, 5, 6, 7]],
                [1, 2],
            ],
            [
                [1, 2, 3, 4, 5],
                [[1, 2, 3, '4', '5']],
                [4, 5],
            ],
            [
                ['1', '2', '3', 4, 5],
                [[1, 2, 3, '4', '5']],
                ['1', '2', '3', 4, 5],
            ],
            [
                [1, 2, 3, 4, 5],
                [[3, 4], [5, 6]],
                [1, 2],
            ],
            [
                [1, 2, 3, 4, 5],
                [[2, 4], [1, 3]],
                [5],
            ],
            [
                [null, 1, 2, null],
                [[null, 3, null]],
                [1, 2],
            ],
            [
                ['1', 2, '3.3', true, false],
                [[true, '2', 3.3, '4', '5']],
                ['1', 2, '3.3', false],
            ],
        ];
    }

    public static function dataProviderForArrayMultisets(): array
    {
        return [
            [
                [2, 2],
                [[]],
                [2, 2],
            ],
            [
                [2, 2],
                [[2]],
                [2],
            ],
            [
                [2, 2],
                [[2, 2]],
                [],
            ],
            [
                [2, 2, 3],
                [[2, 2, 4]],
                [3],
            ],
            [
                [1, 1, 2, 2, 1, 1],
                [[2, 2, 1, 1, 2, 2]],
                [1, 1],
            ],
            [
                [1, 1, 2, 2, 1, 1],
                [[2, 2, 1, 1, '2', '2']],
                [1, 1],
            ],
            [
                [1, 1, 2, 2, 1, 1],
                [[2, 2, '1', '1', 2, 2]],
                [1, 1, 1, 1],
            ],
            [
                [1, 1, 1, 1, 1],
                [[1, 1], [1]],
                [1, 1],
            ],
            [
                [1, 1, 1, 1, 1],
                [[1, 2, 3, 4, 5, 1, 2, 3, 4, 5]],
                [1, 1, 1],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $a
     * @param        array<\Generator> $iterables
     * @param        array $expected
     */
    public function testGenerators(\Generator $a, array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::difference($a, ...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public static function dataProviderForGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [
                $gen([]),
                [],
                [],
            ],
            [
                $gen([]),
                [$gen([])],
                [],
            ],
            [
                $gen([1, 2, 3]),
                [],
                [1, 2, 3],
            ],
            [
                $gen([1, 2, 3]),
                [$gen([])],
                [1, 2, 3],
            ],
            [
                $gen([]),
                [$gen([1, 2, 3])],
                [],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                [$gen([1, 2, 3, 4, 5])],
                [],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                [$gen([3, 4, 5, 6, 7])],
                [1, 2],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                [$gen([1, 2, 3, '4', '5'])],
                [4, 5],
            ],
            [
                $gen(['1', '2', '3', 4, 5]),
                [$gen([1, 2, 3, '4', '5'])],
                ['1', '2', '3', 4, 5],
            ],
            [
                $gen([1, 1, 2, 2, 1, 1]),
                [$gen([2, 2, 1, 1, 2, 2])],
                [1, 1],
            ],
            [
                $gen([1, 1, 2, 2, 1, 1]),
                [$gen([2, 2, '1', '1', 2, 2])],
                [1, 1, 1, 1],
            ],
            [
                $gen([1, 1, 1, 1, 1]),
                [$gen([1, 2, 3, 4, 5, 1, 2, 3, 4, 5])],
                [1, 1, 1],
            ],
            [
                $gen([null, 1, 2, null]),
                [$gen([null, 3, null])],
                [1, 2],
            ],
            [
                $gen(['1', 2, '3.3', true, false]),
                [$gen([true, '2', 3.3, '4', '5'])],
                ['1', 2, '3.3', false],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param        \Iterator $a
     * @param        array<\Iterator> $iterables
     * @param        array $expected
     */
    public function testIterators(\Iterator $a, array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::difference($a, ...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public static function dataProviderForIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [
                $iter([]),
                [],
                [],
            ],
            [
                $iter([]),
                [$iter([])],
                [],
            ],
            [
                $iter([1, 2, 3]),
                [],
                [1, 2, 3],
            ],
            [
                $iter([1, 2, 3]),
                [$iter([])],
                [1, 2, 3],
            ],
            [
                $iter([]),
                [$iter([1, 2, 3])],
                [],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                [$iter([1, 2, 3, 4, 5])],
                [],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                [$iter([3, 4, 5, 6, 7])],
                [1, 2],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                [$iter([1, 2, 3, '4', '5'])],
                [4, 5],
            ],
            [
                $iter(['1', '2', '3', 4, 5]),
                [$iter([1, 2, 3, '4', '5'])],
                ['1', '2', '3', 4, 5],
            ],
            [
                $iter([1, 1, 2, 2, 1, 1]),
                [$iter([2, 2, 1, 1, 2, 2])],
                [1, 1],
            ],
            [
                $iter([1, 1, 2, 2, 1, 1]),
                [$iter([2, 2, '1', '1', 2, 2])],
                [1, 1, 1, 1],
            ],
            [
                $iter([1, 1, 1, 1, 1]),
                [$iter([1, 2, 3, 4, 5, 1, 2, 3, 4, 5])],
                [1, 1, 1],
            ],
            [
                $iter([null, 1, 2, null]),
                [$iter([null, 3, null])],
                [1, 2],
            ],
            [
                $iter(['1', 2, '3.3', true, false]),
                [$iter([true, '2', 3.3, '4', '5'])],
                ['1', 2, '3.3', false],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $a
     * @param        array<\Traversable> $iterables
     * @param        array $expected
     */
    public function testTraversables(\Traversable $a, array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::difference($a, ...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public static function dataProviderForTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                $trav([]),
                [],
                [],
            ],
            [
                $trav([]),
                [$trav([])],
                [],
            ],
            [
                $trav([1, 2, 3]),
                [],
                [1, 2, 3],
            ],
            [
                $trav([1, 2, 3]),
                [$trav([])],
                [1, 2, 3],
            ],
            [
                $trav([]),
                [$trav([1, 2, 3])],
                [],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                [$trav([1, 2, 3, 4, 5])],
                [],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                [$trav([3, 4, 5, 6, 7])],
                [1, 2],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                [$trav([1, 2, 3, '4', '5'])],
                [4, 5],
            ],
            [
                $trav(['1', '2', '3', 4, 5]),
                [$trav([1, 2, 3, '4', '5'])],
                ['1', '2', '3', 4, 5],
            ],
            [
                $trav([1, 1, 2, 2, 1, 1]),
                [$trav([2, 2, 1, 1, 2, 2])],
                [1, 1],
            ],
            [
                $trav([1, 1, 2, 2, 1, 1]),
                [$trav([2, 2, '1', '1', 2, 2])],
                [1, 1, 1, 1],
            ],
            [
                $trav([1, 1, 1, 1, 1]),
                [$trav([1, 2, 3, 4, 5, 1, 2, 3, 4, 5])],
                [1, 1, 1],
            ],
            [
                $trav([null, 1, 2, null]),
                [$trav([null, 3, null])],
                [1, 2],
            ],
            [
                $trav(['1', 2, '3.3', true, false]),
                [$trav([true, '2', 3.3, '4', '5'])],
                ['1', 2, '3.3', false],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForMixed
     * @param        iterable<mixed> $a
     * @param        array<iterable<mixed>> $iterables
     * @param        array $expected
     */
    public function testMixed(iterable $a, array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::difference($a, ...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public static function dataProviderForMixed(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new \ArrayIterator($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                [],
                [$gen([]), $iter([]), $trav([])],
                [],
            ],
            [
                [1, 2, 3, 4, 5],
                [$gen([2, 3]), $iter([4])],
                [1, 5],
            ],
            [
                [1, 1, 1],
                [$gen([1, 2, 3, 1, 2, 3]), $iter([3, 3, 3, 1]), $trav([])],
                [],
            ],
            [
                [1, 1, 1, 1, 1],
                [$gen([1, 1, 1, 1, '1']), $iter([]), $trav([])],
                [1],
            ],
        ];
    }

    /**
     * @test         iterator_to_array
     * @dataProvider dataProviderForArraySets
     * @dataProvider dataProviderForArrayMultisets
     * @param        iterable<mixed> $a
     * @param        array<iterable<mixed>> $iterables
     * @param        array $expected
     */
    public function testIteratorToArray(iterable $a, array $iterables, array $expected): void
    {
        // Given
        $iterator = Set::difference($a, ...$iterables);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }
    /**
     * @test difference keeps every value when both sides are disjoint fresh objects
     */
    public function testKeepsEveryValueForDisjointFreshObjects(): void
    {
        // Given
        $left = (static function (): \Generator {
            for ($i = 1; $i <= 6; ++$i) {
                yield (object) ['value' => $i];
            }
        })();
        $right = (static function (): \Generator {
            for ($i = 1; $i <= 6; ++$i) {
                yield (object) ['value' => $i];
            }
        })();

        // When
        $ids = [];
        foreach (Set::difference($left, $right) as $value) {
            $ids[] = \spl_object_id($value);
        }

        // Then
        $this->assertCount(6, $ids);
    }

    /**
     * Regression for defect 11: arrays that all share one reference slot collapse without an
     * identity anchor, because retaining the array does not retain the object it held when it
     * was yielded. Only a generator can reproduce this, not the four fixtures.
     *
     * @test difference keeps every value when both sides are reference-slot arrays
     */
    public function testKeepsEveryValueForReferenceSlotArrays(): void
    {
        // Given
        $main = self::referenceSlotArrays();
        $subtracted = self::referenceSlotArrays();

        // When
        $result = [];
        foreach (Set::difference($main, $subtracted) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertCount(5, $result);
    }

    /**
     * Yields arrays that all share one reference slot: each yielded [&$slot] follows the next
     * assignment to $slot, so retaining the array does not retain the object it held when it was
     * yielded.
     *
     * @return \Generator<int, array{0: \stdClass}>
     */
    private static function referenceSlotArrays(): \Generator
    {
        $slot = null;

        for ($i = 0; $i < 5; $i++) {
            $slot = new \stdClass();

            yield [&$slot];
        }
    }

    /**
     * @test difference accepts named arguments that match its internal parameter names
     */
    public function testDifferenceAcceptsNamedArgumentsMatchingInternalParameterNames(): void
    {
        // When
        $result = \iterator_to_array(Set::difference(
            [1, 2, 3, 4],
            strict: [2],
            other: [3],
        ), false);

        // Then
        $this->assertSame([1, 4], $result);
    }

    /**
     * @test difference of a generator and itself is empty
     */
    public function testGeneratorMinusItself(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2, 2, 3]);

        // When
        $result = \iterator_to_array(Set::difference($generator, $generator), false);

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test difference of an iterator and itself is empty
     */
    public function testIteratorMinusItself(): void
    {
        // Given
        $iterator = new ArrayIteratorFixture([1, 2, 2, 3]);

        // When
        $result = \iterator_to_array(Set::difference($iterator, $iterator), false);

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test difference of a stream and itself is empty
     */
    public function testStreamMinusItself(): void
    {
        // Given
        $stream = Stream::of(GeneratorFixture::getGenerator([1, 2, 2, 3]));

        // When
        $result = \iterator_to_array(Set::difference($stream, $stream), false);

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test difference of a generator and itself is empty for a NAN element
     */
    public function testGeneratorMinusItselfWithNan(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([\NAN, 1, \NAN]);

        // When
        $result = \iterator_to_array(Set::difference($generator, $generator), false);

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test difference of a generator passed again among other iterables is empty
     */
    public function testGeneratorMinusItselfAmongOtherIterables(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2, 2, 3]);

        // When
        $result = \iterator_to_array(Set::difference($generator, [9], $generator, [1]), false);

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test difference of a generator and itself still traverses the other iterables
     */
    public function testGeneratorMinusItselfTraversesOtherIterables(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2, 2, 3]);
        $traversed = [];
        $other = (static function () use (&$traversed): \Generator {
            foreach ([7, 8, 9] as $value) {
                $traversed[] = $value;
                yield $value;
            }
        })();

        // When
        $result = \iterator_to_array(Set::difference($generator, $generator, $other), false);

        // Then
        $this->assertSame([], $result);
        $this->assertSame([7, 8, 9], $traversed);
    }

    /**
     * @test difference of a generator and itself propagates the generator's exception
     */
    public function testGeneratorMinusItselfPropagatesException(): void
    {
        // Given
        $generator = (static function (): \Generator {
            yield 1;
            throw new \RuntimeException('source failed');
        })();

        // Then
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('source failed');

        // When
        \iterator_to_array(Set::difference($generator, $generator), false);
    }

    /**
     * @test difference of a generator and itself propagates another iterable's exception
     */
    public function testGeneratorMinusItselfPropagatesOtherIterableException(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2, 2, 3]);
        $other = (static function (): \Generator {
            yield 1;
            throw new \RuntimeException('other failed');
        })();

        // Then
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('other failed');

        // When
        \iterator_to_array(Set::difference($generator, $generator, $other), false);
    }

    /**
     * @test difference subtracts a generator passed twice once per position
     */
    public function testGeneratorSubtractedTwice(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2]);

        // When
        $result = \iterator_to_array(Set::difference([1, 1, 1, 2, 2, 3], $generator, $generator), false);

        // Then
        $this->assertSame([1, 3], $result);
    }

    /**
     * @test difference subtracts an iterator passed twice once per position
     */
    public function testIteratorSubtractedTwice(): void
    {
        // Given
        $iterator = new ArrayIteratorFixture([1, 2]);

        // When
        $result = \iterator_to_array(Set::difference([1, 1, 1, 2, 2, 3], $iterator, $iterator), false);

        // Then
        $this->assertSame([1, 3], $result);
    }

    /**
     * @test difference subtracts a stream passed twice once per position
     */
    public function testStreamSubtractedTwice(): void
    {
        // Given
        $stream = Stream::of(GeneratorFixture::getGenerator([1, 2]));

        // When
        $result = \iterator_to_array(Set::difference([1, 1, 1, 2, 2, 3], $stream, $stream), false);

        // Then
        $this->assertSame([1, 3], $result);
    }

    /**
     * @test difference subtracts a generator and a stream of it once per position
     */
    public function testGeneratorAndStreamOfItSubtracted(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2]);

        // When
        $result = \iterator_to_array(Set::difference([1, 1, 1, 2, 2, 3], $generator, Stream::of($generator)), false);

        // Then
        $this->assertSame([1, 3], $result);
    }

    /**
     * @test difference subtracts a generator passed around another iterable once per position
     */
    public function testGeneratorSubtractedTwiceAroundAnotherIterable(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2]);

        // When
        $result = \iterator_to_array(Set::difference([1, 1, 1, 2, 2, 3], $generator, [3], $generator), false);

        // Then
        $this->assertSame([1], $result);
    }

    /**
     * @test difference subtracts an array passed twice once per position
     */
    public function testArraySubtractedTwice(): void
    {
        // Given
        $array = [1, 2];

        // When
        $result = \iterator_to_array(Set::difference([1, 1, 1, 2, 2, 3], $array, $array), false);

        // Then
        $this->assertSame([1, 3], $result);
    }

    /**
     * Each copy of the array would take about 2 MiB, so the bound fails if a subtracted array is copied.
     *
     * @test difference traverses a subtracted array without copying it
     */
    public function testSubtractedArrayIsNotCopied(): void
    {
        // Given
        $array = \array_fill(0, 100_000, 1);

        // When
        \memory_reset_peak_usage();
        $before = \memory_get_usage();
        $result = \iterator_to_array(Set::difference([], $array, $array, $array), false);
        $peakIncrease = \memory_get_peak_usage() - $before;

        // Then
        $this->assertSame([], $result);
        $this->assertLessThan(1024 * 1024, $peakIncrease);
    }

    /**
     * An array is read in argument order, after the iterables before it: an earlier generator can change the
     * value a later array holds by reference.
     *
     * @test difference reads a subtracted array after the iterables passed before it
     */
    public function testSubtractedArrayReadInArgumentOrder(): void
    {
        // Given
        $slot = 1;
        $later = [&$slot];
        $first = (static function () use (&$slot): \Generator {
            $slot = 2;
            yield 9;
        })();

        // When
        $result = \iterator_to_array(Set::difference([1, 2], $first, $later), false);

        // Then
        $this->assertSame([1], $result);
    }
}
