<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;
use IterTools\Tests\Fixture\MisreportingCountableFixture;
use IterTools\Tests\Fixture\SinglePassMisreportingCountableFixture;

class ToRandomValueTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        mixed $expected
     */
    public function testArray(array $data, array $expected): void
    {
        // When
        $result = Reduce::toRandomValue($data);

        // Then
        $this->assertContains($result, $expected);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [
                [1],
                [1],
            ],
            [
                [1, 2, 3],
                [1, 2, 3],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                [1, 2, 3],
            ],
            [
                ['1' => 'a', '2' => 'b', '3' => 'c'],
                ['a', 'b', 'c'],
            ],
            [
                [1, 2.2, '3', true, false, null, [1, 2, 3]],
                [1, 2.2, '3', true, false, null, [1, 2, 3]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        mixed $expected
     */
    public function testGenerators(\Generator $data, array $expected): void
    {
        // When
        $result = Reduce::toRandomValue($data);

        // Then
        $this->assertContains($result, $expected);
    }

    public static function dataProviderForGenerators(): array
    {
        $gen = fn ($data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [
                $gen([1]),
                [1],
            ],
            [
                $gen([1, 2, 3]),
                [1, 2, 3],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3]),
                [1, 2, 3],
            ],
            [
                $gen(['1' => 'a', '2' => 'b', '3' => 'c']),
                ['a', 'b', 'c'],
            ],
            [
                $gen([1, 2.2, '3', true, false, null, [1, 2, 3]]),
                [1, 2.2, '3', true, false, null, [1, 2, 3]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param        \Iterator $data
     * @param        mixed $expected
     */
    public function testIterators(\Iterator $data, array $expected): void
    {
        // When
        $result = Reduce::toRandomValue($data);

        // Then
        $this->assertContains($result, $expected);
    }

    public static function dataProviderForIterators(): array
    {
        $iter = fn ($data) => new \ArrayIterator($data);

        return [
            [
                $iter([1]),
                [1],
            ],
            [
                $iter([1, 2, 3]),
                [1, 2, 3],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3]),
                [1, 2, 3],
            ],
            [
                $iter(['1' => 'a', '2' => 'b', '3' => 'c']),
                ['a', 'b', 'c'],
            ],
            [
                $iter([1, 2.2, '3', true, false, null, [1, 2, 3]]),
                [1, 2.2, '3', true, false, null, [1, 2, 3]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        mixed $expected
     */
    public function testTraversables(\Traversable $data, array $expected): void
    {
        // When
        $result = Reduce::toRandomValue($data);

        // Then
        $this->assertContains($result, $expected);
    }

    public static function dataProviderForTraversables(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([1]),
                [1],
            ],
            [
                $trav([1, 2, 3]),
                [1, 2, 3],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3]),
                [1, 2, 3],
            ],
            [
                $trav(['1' => 'a', '2' => 'b', '3' => 'c']),
                ['a', 'b', 'c'],
            ],
            [
                $trav([1, 2.2, '3', true, false, null, [1, 2, 3]]),
                [1, 2.2, '3', true, false, null, [1, 2, 3]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForEmptyIterable
     * @param iterable $data
     * @return void
     */
    public function testErrorOnEmpty(iterable $data): void
    {
        // Then
        $this->expectException(\LengthException::class);
        $this->expectExceptionMessage('Given iterable must be non-empty');

        // When
        Reduce::toRandomValue($data);
    }

    /**
     * @test toRandomValue reads a Countable that reports 0 instead of trusting it as empty
     * @dataProvider dataProviderForCountableReportingZero
     * @param \Countable&\Traversable $data
     * @param array $expected
     */
    public function testCountableReportingZeroIsRead(\Countable&\Traversable $data, array $expected): void
    {
        // When
        $result = Reduce::toRandomValue($data);

        // Then
        $this->assertContains($result, $expected);
    }

    public static function dataProviderForCountableReportingZero(): array
    {
        return [
            [
                new MisreportingCountableFixture([1, 2, 3], 0),
                [1, 2, 3],
            ],
            [
                new SinglePassMisreportingCountableFixture([1, 2, 3], 0),
                [1, 2, 3],
            ],
            [
                new \CachingIterator(new \ArrayIterator([1, 2, 3]), \CachingIterator::FULL_CACHE),
                [1, 2, 3],
            ],
            [
                new \CachingIterator(new \ArrayIterator(['a' => 'aa', 'b' => 'bb']), \CachingIterator::FULL_CACHE),
                ['aa', 'bb'],
            ],
        ];
    }

    /**
     * @test toRandomValue picks from the elements read, without reading again, when a Countable over-reports
     * @dataProvider dataProviderForOverReportingCountable
     * @param \Countable&\Traversable $data
     * @param array $expected
     */
    public function testOverReportingCountableIsReadOnce(\Countable&\Traversable $data, array $expected): void
    {
        // When
        $result = Reduce::toRandomValue($data);

        // Then
        $this->assertContains($result, $expected);
    }

    /**
     * A count of PHP_INT_MAX puts the chosen index past the end in all but 3 in 2^63 calls.
     */
    public static function dataProviderForOverReportingCountable(): array
    {
        return [
            [
                new SinglePassMisreportingCountableFixture([1], \PHP_INT_MAX),
                [1],
            ],
            [
                new SinglePassMisreportingCountableFixture([1, 2, 3], \PHP_INT_MAX),
                [1, 2, 3],
            ],
            [
                new SinglePassMisreportingCountableFixture(['a' => 'aa', 'b' => 'bb', 'c' => 'cc'], \PHP_INT_MAX),
                ['aa', 'bb', 'cc'],
            ],
            [
                new SinglePassMisreportingCountableFixture([1, 2, 3], 10),
                [1, 2, 3],
            ],
            [
                new MisreportingCountableFixture([1, 2, 3], \PHP_INT_MAX),
                [1, 2, 3],
            ],
        ];
    }

    /**
     * @test toRandomValue can pick any element of a Countable that over-reports
     */
    public function testOverReportingCountablePicksEveryElement(): void
    {
        // Given
        $picked = [];

        // When
        for ($i = 0; $i < 200; ++$i) {
            $picked[] = Reduce::toRandomValue(new SinglePassMisreportingCountableFixture([1, 2, 3], \PHP_INT_MAX));
        }

        // Then
        $picked = \array_unique($picked);
        \sort($picked);
        $this->assertSame([1, 2, 3], $picked);
    }

    /**
     * @test toRandomValue holds at most one element read before the chosen one, not every element before it
     */
    public function testCountableHoldsOneElementReadBeforeTheChosenOne(): void
    {
        // Given
        $data = new class () implements \IteratorAggregate, \Countable {
            public int $maxLiveElements = 0;

            public function getIterator(): \Generator
            {
                $references = [];
                for ($i = 0; $i < 100; ++$i) {
                    $element = new \stdClass();
                    $references[] = \WeakReference::create($element);
                    yield $element;
                    unset($element);

                    $live = \count(\array_filter($references, fn (\WeakReference $r) => $r->get() !== null));
                    $this->maxLiveElements = \max($this->maxLiveElements, $live);
                }
            }

            // Over-reports, so every element is read before the fallback is picked.
            public function count(): int
            {
                return \PHP_INT_MAX;
            }
        };

        // When
        Reduce::toRandomValue($data);

        // Then the current element and the one kept as fallback
        $this->assertLessThanOrEqual(2, $data->maxLiveElements);
    }

    /**
     * @test toRandomValue throws LengthException for an empty Countable that misreports its size
     * @dataProvider dataProviderForEmptyMisreportingCountable
     * @param \Countable&\Traversable $data
     */
    public function testErrorOnEmptyMisreportingCountable(\Countable&\Traversable $data): void
    {
        // Then
        $this->expectException(\LengthException::class);
        $this->expectExceptionMessage('Given iterable must be non-empty');

        // When
        Reduce::toRandomValue($data);
    }

    public static function dataProviderForEmptyMisreportingCountable(): array
    {
        return [
            [new MisreportingCountableFixture([], 5)],
            [new SinglePassMisreportingCountableFixture([], 5)],
            [new SinglePassMisreportingCountableFixture([], \PHP_INT_MAX)],
            [new \CachingIterator(new \ArrayIterator([]), \CachingIterator::FULL_CACHE)],
        ];
    }
}
