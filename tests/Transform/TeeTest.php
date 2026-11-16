<?php

declare(strict_types=1);

namespace IterTools\Tests\Transform;

use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\CountingIteratorAggregateFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;
use IterTools\Transform;

class TeeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test tee example usage
     */
    public function testTeeExampleUsageArrayDestructuring(): void
    {
        // Given
        $data = ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun'];
        $count = 3;

        // When
        [$iter1, $iter2, $iter3] = Transform::tee($data, $count);

        // Then
        $this->assertEquals($data, \iterator_to_array($iter1));
        $this->assertEquals($data, \iterator_to_array($iter2));
        $this->assertEquals($data, \iterator_to_array($iter3));
    }

    /**
     * @test tee example usage
     */
    public function testTeeExampleUsageIteratingMultipleIterators(): void
    {
        // Given
        $data = ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun'];
        $count = 3;

        // And
        $result = [];
        $i = 0;

        // When
        foreach (Transform::tee($data, $count) as $week) {
            foreach ($week as $day) {
                $result[$i][] = $day;
            }
            $i++;
        }

        // Then
        $this->assertEquals($data, $result[0]);
        $this->assertEquals($data, $result[1]);
        $this->assertEquals($data, $result[2]);
    }

    /**
     * @dataProvider dataProviderForArray
     * @param array<scalar, mixed> $data
     * @param int $relatedCount
     * @param array $expected
     */
    public function testChainArray(array $data, int $relatedCount, array $expected): void
    {
        // Given
        $iterators = Transform::tee($data, $relatedCount);
        $result = \array_fill(0, $relatedCount, []);

        // When
        foreach ($iterators as $i => $iterator) {
            foreach ($iterator as $key => $value) {
                $result[$i][$key] = $value;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider dataProviderForArray
     * @param array<scalar, mixed> $data
     * @param int $relatedCount
     * @param array $expected
     */
    public function testZipArray(array $data, int $relatedCount, array $expected): void
    {
        // Given
        $iterators = Transform::tee($data, $relatedCount);
        $result = \array_fill(0, $relatedCount, []);

        // When
        while (\array_reduce($iterators, fn (bool $carry, \Iterator $it) => $carry ?: $it->valid(), false)) {
            foreach ($iterators as $i => $iterator) {
                $result[$i][$iterator->key()] = $iterator->current();
                $iterator->next();
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider dataProviderForArray
     * @param array<scalar, mixed> $data
     * @param int $relatedCount
     * @param array $expected
     */
    public function testLadderArray(array $data, int $relatedCount, array $expected): void
    {
        // Given
        $iterators = Transform::tee($data, $relatedCount);
        $result = \array_fill(0, $relatedCount, []);

        // When
        $j = 0;
        while (\array_reduce($iterators, fn (bool $carry, \Iterator $it) => $carry ?: $it->valid(), false)) {
            foreach ($iterators as $i => $iterator) {
                if ($i > $j) {
                    continue;
                }

                if (!$iterator->valid()) {
                    continue;
                }

                $result[$i][$iterator->key()] = $iterator->current();
                $iterator->next();
            }
            $j++;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [
                [],
                1,
                [
                    [],
                ],
            ],
            [
                [],
                2,
                [
                    [],
                    [],
                ],
            ],
            [
                [],
                3,
                [
                    [],
                    [],
                    [],
                ],
            ],
            [
                [1, 2, 3, 4, 5],
                1,
                [
                    [1, 2, 3, 4, 5],
                ],
            ],
            [
                [1, 2, 3, 4, 5],
                2,
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                ],
            ],
            [
                [1, 2, 3, 4, 5],
                3,
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                ],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                1,
                [
                    ['a' => 1, 'b' => 2, 'c' => 3],
                ],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                2,
                [
                    ['a' => 1, 'b' => 2, 'c' => 3],
                    ['a' => 1, 'b' => 2, 'c' => 3],
                ],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                3,
                [
                    ['a' => 1, 'b' => 2, 'c' => 3],
                    ['a' => 1, 'b' => 2, 'c' => 3],
                    ['a' => 1, 'b' => 2, 'c' => 3],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator<scalar, mixed> $data
     * @param int $relatedCount
     * @param array $expected
     */
    public function testChainGenerators(\Generator $data, int $relatedCount, array $expected): void
    {
        // Given
        $iterators = Transform::tee($data, $relatedCount);
        $result = \array_fill(0, $relatedCount, []);

        // When
        foreach ($iterators as $i => $iterator) {
            foreach ($iterator as $key => $value) {
                $result[$i][$key] = $value;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator<scalar, mixed> $data
     * @param int $relatedCount
     * @param array $expected
     */
    public function testZipGenerators(\Generator $data, int $relatedCount, array $expected): void
    {
        // Given
        $iterators = Transform::tee($data, $relatedCount);
        $result = \array_fill(0, $relatedCount, []);

        // When
        while (\array_reduce($iterators, fn (bool $carry, \Iterator $it) => $carry ?: $it->valid(), false)) {
            foreach ($iterators as $i => $iterator) {
                $result[$i][$iterator->key()] = $iterator->current();
                $iterator->next();
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator<scalar, mixed> $data
     * @param int $relatedCount
     * @param array $expected
     */
    public function testLadderGenerators(\Generator $data, int $relatedCount, array $expected): void
    {
        // Given
        $iterators = Transform::tee($data, $relatedCount);
        $result = \array_fill(0, $relatedCount, []);

        // When
        $j = 0;
        while (\array_reduce($iterators, fn (bool $carry, \Iterator $it) => $carry ?: $it->valid(), false)) {
            foreach ($iterators as $i => $iterator) {
                if ($i > $j) {
                    continue;
                }

                if (!$iterator->valid()) {
                    continue;
                }

                $result[$i][$iterator->key()] = $iterator->current();
                $iterator->next();
            }
            $j++;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForGenerators(): array
    {
        $gen = static function ($data) {
            foreach ($data as $key => $datum) {
                yield $key => $datum;
            }
        };

        return [
            [
                $gen([]),
                1,
                [
                    [],
                ],
            ],
            [
                $gen([]),
                2,
                [
                    [],
                    [],
                ],
            ],
            [
                $gen([]),
                3,
                [
                    [],
                    [],
                    [],
                ],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                1,
                [
                    [1, 2, 3, 4, 5],
                ],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                2,
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                ],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                3,
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                ],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3]),
                1,
                [
                    ['a' => 1, 'b' => 2, 'c' => 3],
                ],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3]),
                2,
                [
                    ['a' => 1, 'b' => 2, 'c' => 3],
                    ['a' => 1, 'b' => 2, 'c' => 3],
                ],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3]),
                3,
                [
                    ['a' => 1, 'b' => 2, 'c' => 3],
                    ['a' => 1, 'b' => 2, 'c' => 3],
                    ['a' => 1, 'b' => 2, 'c' => 3],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator<scalar, mixed> $data
     * @param int $relatedCount
     * @param array $expected
     */
    public function testChainIterators(\Iterator $data, int $relatedCount, array $expected): void
    {
        // Given
        $iterators = Transform::tee($data, $relatedCount);
        $result = \array_fill(0, $relatedCount, []);

        // When
        foreach ($iterators as $i => $iterator) {
            foreach ($iterator as $key => $value) {
                $result[$i][$key] = $value;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator<scalar, mixed> $data
     * @param int $relatedCount
     * @param array $expected
     */
    public function testZipIterators(\Iterator $data, int $relatedCount, array $expected): void
    {
        // Given
        $iterators = Transform::tee($data, $relatedCount);
        $result = \array_fill(0, $relatedCount, []);

        // When
        while (\array_reduce($iterators, fn (bool $carry, \Iterator $it) => $carry ?: $it->valid(), false)) {
            foreach ($iterators as $i => $iterator) {
                $result[$i][$iterator->key()] = $iterator->current();
                $iterator->next();
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider dataProviderForIterators
     * @dataProvider dataProviderForNestedIterators
     * @param \Iterator<scalar, mixed> $data
     * @param int $relatedCount
     * @param array $expected
     */
    public function testLadderIterators(\Iterator $data, int $relatedCount, array $expected): void
    {
        // Given
        $iterators = Transform::tee($data, $relatedCount);
        $result = \array_fill(0, $relatedCount, []);

        // When
        $j = 0;
        while (\array_reduce($iterators, fn (bool $carry, \Iterator $it) => $carry ?: $it->valid(), false)) {
            foreach ($iterators as $i => $iterator) {
                if ($i > $j) {
                    continue;
                }

                if (!$iterator->valid()) {
                    continue;
                }

                $result[$i][$iterator->key()] = $iterator->current();
                $iterator->next();
            }
            $j++;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForIterators(): array
    {
        $iter = fn ($data) => new \ArrayIterator($data);

        return [
            [
                $iter([]),
                1,
                [
                    [],
                ],
            ],
            [
                $iter([]),
                2,
                [
                    [],
                    [],
                ],
            ],
            [
                $iter([]),
                3,
                [
                    [],
                    [],
                    [],
                ],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                1,
                [
                    [1, 2, 3, 4, 5],
                ],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                2,
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                ],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                3,
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                ],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3]),
                1,
                [
                    ['a' => 1, 'b' => 2, 'c' => 3],
                ],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3]),
                2,
                [
                    ['a' => 1, 'b' => 2, 'c' => 3],
                    ['a' => 1, 'b' => 2, 'c' => 3],
                ],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3]),
                3,
                [
                    ['a' => 1, 'b' => 2, 'c' => 3],
                    ['a' => 1, 'b' => 2, 'c' => 3],
                    ['a' => 1, 'b' => 2, 'c' => 3],
                ],
            ],
        ];
    }

    public static function dataProviderForNestedIterators(): array
    {
        $nested = fn ($data) => new \IteratorIterator(new \IteratorIterator(new \ArrayIterator($data)));

        return [
            [
                $nested([]),
                1,
                [
                    [],
                ],
            ],
            [
                $nested([]),
                2,
                [
                    [],
                    [],
                ],
            ],
            [
                $nested([]),
                3,
                [
                    [],
                    [],
                    [],
                ],
            ],
            [
                $nested([1, 2, 3, 4, 5]),
                1,
                [
                    [1, 2, 3, 4, 5],
                ],
            ],
            [
                $nested([1, 2, 3, 4, 5]),
                2,
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                ],
            ],
            [
                $nested([1, 2, 3, 4, 5]),
                3,
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                ],
            ],
            [
                $nested(['a' => 1, 'b' => 2, 'c' => 3]),
                1,
                [
                    ['a' => 1, 'b' => 2, 'c' => 3],
                ],
            ],
            [
                $nested(['a' => 1, 'b' => 2, 'c' => 3]),
                2,
                [
                    ['a' => 1, 'b' => 2, 'c' => 3],
                    ['a' => 1, 'b' => 2, 'c' => 3],
                ],
            ],
            [
                $nested(['a' => 1, 'b' => 2, 'c' => 3]),
                3,
                [
                    ['a' => 1, 'b' => 2, 'c' => 3],
                    ['a' => 1, 'b' => 2, 'c' => 3],
                    ['a' => 1, 'b' => 2, 'c' => 3],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable<scalar, mixed> $data
     * @param int $relatedCount
     * @param array $expected
     */
    public function testChainTraversables(\Traversable $data, int $relatedCount, array $expected): void
    {
        // Given
        $iterators = Transform::tee($data, $relatedCount);
        $result = \array_fill(0, $relatedCount, []);

        // When
        foreach ($iterators as $i => $iterator) {
            foreach ($iterator as $key => $value) {
                $result[$i][$key] = $value;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable<scalar, mixed> $data
     * @param int $relatedCount
     * @param array $expected
     */
    public function testZipTraversables(\Traversable $data, int $relatedCount, array $expected): void
    {
        // Given
        $iterators = Transform::tee($data, $relatedCount);
        $result = \array_fill(0, $relatedCount, []);

        // When
        while (\array_reduce($iterators, fn (bool $carry, \Iterator $it) => $carry ?: $it->valid(), false)) {
            foreach ($iterators as $i => $iterator) {
                $result[$i][$iterator->key()] = $iterator->current();
                $iterator->next();
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable<scalar, mixed> $data
     * @param int $relatedCount
     * @param array $expected
     */
    public function testLadderTraversables(\Traversable $data, int $relatedCount, array $expected): void
    {
        // Given
        $iterators = Transform::tee($data, $relatedCount);
        $result = \array_fill(0, $relatedCount, []);

        // When
        $j = 0;
        while (\array_reduce($iterators, fn (bool $carry, \Iterator $it) => $carry ?: $it->valid(), false)) {
            foreach ($iterators as $i => $iterator) {
                if ($i > $j) {
                    continue;
                }

                if (!$iterator->valid()) {
                    continue;
                }

                $result[$i][$iterator->key()] = $iterator->current();
                $iterator->next();
            }
            $j++;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForTraversables(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                1,
                [
                    [],
                ],
            ],
            [
                $trav([]),
                2,
                [
                    [],
                    [],
                ],
            ],
            [
                $trav([]),
                3,
                [
                    [],
                    [],
                    [],
                ],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                1,
                [
                    [1, 2, 3, 4, 5],
                ],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                2,
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                ],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                3,
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                ],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3]),
                1,
                [
                    ['a' => 1, 'b' => 2, 'c' => 3],
                ],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3]),
                2,
                [
                    ['a' => 1, 'b' => 2, 'c' => 3],
                    ['a' => 1, 'b' => 2, 'c' => 3],
                ],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3]),
                3,
                [
                    ['a' => 1, 'b' => 2, 'c' => 3],
                    ['a' => 1, 'b' => 2, 'c' => 3],
                    ['a' => 1, 'b' => 2, 'c' => 3],
                ],
            ],
        ];
    }

    /**
     * @test         tee throws InvalidArgumentException for zero count
     */
    public function testTeeThrowsExceptionForZeroCount(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Count must be ≥ 1. Got 0');

        // When
        Transform::tee([1, 2, 3], 0);
    }

    /**
     * @test         tee throws InvalidArgumentException for negative count
     * @dataProvider dataProviderForNegativeCount
     */
    public function testTeeThrowsExceptionForNegativeCount(int $count): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Count must be ≥ 1. Got {$count}");

        // When
        Transform::tee([1, 2, 3], $count);
    }

    public static function dataProviderForNegativeCount(): array
    {
        return [
            [-1],
            [-2],
            [-10],
        ];
    }

    /**
     * @test         tee iterator_to_array
     * @dataProvider dataProviderForArray
     * @param array  $data
     * @param int   $relatedCount
     */
    public function testIteratorToArray(array $data, int $relatedCount): void
    {
        // Given
        $iterators = Transform::tee($data, $relatedCount);

        // Then
        $this->assertEquals($relatedCount, \count($iterators));

        // And When
        foreach ($iterators as $iterator) {
            // Then
            $this->assertEquals($data, \iterator_to_array($iterator));
        }
    }

    /**
     * @test tee keeps a cached null value when another copy advances the source
     */
    public function testNullValueSurvivesOtherCopyAdvancing(): void
    {
        // Given
        [$a, $b] = Transform::tee([null, 'x'], 2);

        // When
        $a->next();

        // Then
        $this->assertNull($b->current());
        $this->assertSame(0, $b->key());
        $this->assertSame('x', $a->current());
    }

    /**
     * @test tee keeps cached null values for a copy that lags behind
     */
    public function testLaggingCopyReplaysNullValues(): void
    {
        // Given
        $data = [null, 1, null, 2];
        [$a, $b] = Transform::tee($data, 2);

        // When copy a runs one element ahead of copy b
        $first = [];
        $second = [];
        $a->rewind();
        $b->rewind();
        foreach ($data as $_) {
            $first[] = $a->current();
            $a->next();
            if ($a->valid()) {
                $second[] = $b->current();
                $b->next();
            }
        }
        while ($b->valid()) {
            $second[] = $b->current();
            $b->next();
        }

        // Then
        $this->assertSame($data, $first);
        $this->assertSame($data, $second);
    }

    /**
     * @test tee keeps a cached null key when another copy advances the source
     */
    public function testNullKeySurvivesOtherCopyAdvancing(): void
    {
        // Given
        $generator = (static function () {
            yield null => 'a';
            yield 'k' => 'b';
        })();
        [$a, $b] = Transform::tee($generator, 2);

        // When
        $a->next();

        // Then
        $this->assertNull($b->key());
        $this->assertSame('a', $b->current());
        $this->assertSame('k', $a->key());
    }

    /**
     * @test tee yields the last line of a file that has no trailing newline
     */
    public function testSplFileObjectWithoutTrailingNewline(): void
    {
        // Given
        $file = new \SplTempFileObject();
        $file->fwrite("a\nb");
        $file->rewind();
        $expected = \iterator_to_array($file);
        $file->rewind();

        // When
        [$a, $b] = Transform::tee($file, 2);

        // Then
        $this->assertSame(["a\n", 'b'], \array_values($expected));
        $this->assertSame($expected, \iterator_to_array($a));
        $this->assertSame($expected, \iterator_to_array($b));
    }

    /**
     * @test tee over an SplQueue
     */
    public function testSplQueue(): void
    {
        // Given
        $queue = new \SplQueue();
        $queue->enqueue('a');
        $queue->enqueue('b');
        $queue->enqueue('c');

        // When
        [$a, $b] = Transform::tee($queue, 2);

        // Then
        $this->assertSame(['a', 'b', 'c'], \iterator_to_array($a));
        $this->assertSame(['a', 'b', 'c'], \iterator_to_array($b));
    }

    /**
     * @test tee over an SplStack
     */
    public function testSplStack(): void
    {
        // Given
        $stack = new \SplStack();
        $stack->push('a');
        $stack->push('b');
        $stack->push('c');

        // When
        [$a, $b] = Transform::tee($stack, 2);

        // Then
        $this->assertSame([2 => 'c', 1 => 'b', 0 => 'a'], \iterator_to_array($a));
        $this->assertSame([2 => 'c', 1 => 'b', 0 => 'a'], \iterator_to_array($b));
    }

    /**
     * @test tee over an SplDoublyLinkedList
     */
    public function testSplDoublyLinkedList(): void
    {
        // Given
        $list = new \SplDoublyLinkedList();
        $list->push('a');
        $list->push('b');
        $list->push('c');

        // When
        [$a, $b] = Transform::tee($list, 2);

        // Then
        $this->assertSame(['a', 'b', 'c'], \iterator_to_array($a));
        $this->assertSame(['a', 'b', 'c'], \iterator_to_array($b));
    }

    /**
     * @test tee does not read the source until a copy is first used
     */
    public function testSourceIsNotReadBeforeFirstUse(): void
    {
        // Given
        $log = [];
        $generator = (static function () use (&$log) {
            $log[] = 'read';
            yield 1;
            yield 2;
        })();

        // When
        [$a, $b] = Transform::tee($generator, 2);

        // Then
        $this->assertSame([], $log);

        // And when
        $result = \iterator_to_array($a);

        // Then
        $this->assertSame([1, 2], $result);
        $this->assertSame(['read'], $log);
        $this->assertSame([1, 2], \iterator_to_array($b));
    }

    /**
     * @test tee never pairs a key with a value from a different element when current() throws
     */
    public function testKeysAndValuesStayAlignedWhenCurrentThrows(): void
    {
        // Given
        $source = new class implements \Iterator {
            private int $i = 0;
            private bool $thrown = false;
            private const PAIRS = [['k1', 'v1'], ['k2', 'v2'], ['k3', 'v3']];

            public function rewind(): void
            {
                $this->i = 0;
            }

            public function valid(): bool
            {
                return $this->i < \count(self::PAIRS);
            }

            public function key(): string
            {
                return self::PAIRS[$this->i][0];
            }

            public function current(): string
            {
                if ($this->i === 1 && !$this->thrown) {
                    $this->thrown = true;
                    throw new \RuntimeException('boom');
                }
                return self::PAIRS[$this->i][1];
            }

            public function next(): void
            {
                ++$this->i;
            }
        };
        [$a] = Transform::tee($source, 1);

        // When
        $pairs = [];
        $steps = 0;
        while ($steps++ < 10) {
            try {
                if (!$a->valid()) {
                    break;
                }
                $pairs[] = [$a->key(), $a->current()];
                $a->next();
            } catch (\RuntimeException) {
                // the source failed once; keep going
            }
        }

        // Then
        foreach ($pairs as $pair) {
            $this->assertContains($pair, [['k1', 'v1'], ['k2', 'v2'], ['k3', 'v3']]);
        }
    }

    /**
     * @test every tee copy that reaches a source failure receives the same exception, after the elements before it
     */
    public function testSourceExceptionReachesEveryCopy(): void
    {
        // Given
        $exception = new \RuntimeException('boom');
        $source = (function () use ($exception) {
            yield 1;
            yield 2;
            throw $exception;
        })();
        [$a, $b, $c] = Transform::tee($source, 3);

        foreach ([$a, $b, $c] as $copy) {
            // When
            $values = [];
            $caught = null;
            try {
                foreach ($copy as $value) {
                    $values[] = $value;
                }
            } catch (\RuntimeException $e) {
                $caught = $e;
            }

            // Then
            $this->assertSame([1, 2], $values);
            $this->assertSame($exception, $caught);
        }
    }

    /**
     * Regression: a source released once read to its end can throw from its destructor. That is a
     * source failure like any other, so every copy gets it, not only the copy whose read released it.
     *
     * @test every tee copy receives an exception thrown while releasing the exhausted source
     */
    public function testSourceReleaseExceptionReachesEveryCopy(): void
    {
        // Given
        $exception = new \RuntimeException('cleanup failed');
        $source = new class ([1, 2], $exception) extends \ArrayIterator {
            public function __construct(array $array, private ?\Throwable $onDestruct)
            {
                parent::__construct($array);
            }

            public function __destruct()
            {
                $exception = $this->onDestruct;
                $this->onDestruct = null;
                if ($exception !== null) {
                    throw $exception;
                }
            }
        };
        [$a, $b] = Transform::tee($source, 2);
        unset($source);

        foreach ([$a, $b] as $copy) {
            // When
            $values = [];
            $caught = null;
            try {
                foreach ($copy as $value) {
                    $values[] = $value;
                }
            } catch (\RuntimeException $e) {
                $caught = $e;
            }

            // Then
            $this->assertSame([1, 2], $values);
            $this->assertSame($exception, $caught);
        }
    }

    /**
     * @test every tee copy receives an exception thrown while releasing an empty source
     */
    public function testEmptySourceReleaseExceptionReachesEveryCopy(): void
    {
        // Given
        $exception = new \RuntimeException('cleanup failed');
        $source = new class ([], $exception) extends \ArrayIterator {
            public function __construct(array $array, private ?\Throwable $onDestruct)
            {
                parent::__construct($array);
            }

            public function __destruct()
            {
                $exception = $this->onDestruct;
                $this->onDestruct = null;
                if ($exception !== null) {
                    throw $exception;
                }
            }
        };
        [$a, $b] = Transform::tee($source, 2);
        unset($source);

        foreach ([$a, $b] as $copy) {
            // When
            $values = [];
            $caught = null;
            try {
                foreach ($copy as $value) {
                    $values[] = $value;
                }
            } catch (\RuntimeException $e) {
                $caught = $e;
            }

            // Then
            $this->assertSame([], $values);
            $this->assertSame($exception, $caught);
        }
    }

    /**
     * @test a tee copy lagging behind the one that hit a source failure still gets the elements before it, then the failure
     */
    public function testSourceExceptionReachesLaggingCopyAfterBufferedElements(): void
    {
        // Given
        $source = (function () {
            yield 1;
            yield 2;
            throw new \RuntimeException('boom');
        })();
        [$a, $b] = Transform::tee($source, 2);
        $a->rewind();
        $a->next();
        $a->next();
        try {
            $a->valid();
            $this->fail('Expected \RuntimeException');
        } catch (\RuntimeException $first) {
        }

        // When
        $values = [];
        $caught = null;
        try {
            foreach ($b as $value) {
                $values[] = $value;
            }
        } catch (\RuntimeException $e) {
            $caught = $e;
        }

        // Then
        $this->assertSame([1, 2], $values);
        $this->assertSame($first, $caught);
    }

    /**
     * @test a tee copy that hit a source failure keeps throwing it rather than reading the source again
     */
    public function testSourceExceptionIsStickyForTheCopyThatHitIt(): void
    {
        // Given
        $reads = 0;
        $source = (function () use (&$reads) {
            ++$reads;
            yield 1;
            ++$reads;
            throw new \RuntimeException('boom');
        })();
        [$a] = Transform::tee($source, 1);
        $a->rewind();
        $a->next();
        try {
            $a->valid();
            $this->fail('Expected \RuntimeException');
        } catch (\RuntimeException $first) {
        }

        // When
        $caught = [];
        foreach ([fn () => $a->valid(), fn () => $a->current(), fn () => $a->key(), fn () => $a->next()] as $call) {
            try {
                $call();
            } catch (\RuntimeException $e) {
                $caught[] = $e;
            }
        }

        // Then
        $this->assertSame([$first, $first, $first, $first], $caught);
        $this->assertSame(2, $reads);
    }

    /**
     * @test a source whose current() fails once is not retried: the failure ends every tee copy at that element
     */
    public function testTransientSourceFailureIsNotRetried(): void
    {
        // Given
        $source = new class implements \Iterator {
            private int $i = 0;
            private bool $thrown = false;

            public function rewind(): void
            {
                $this->i = 0;
            }

            public function valid(): bool
            {
                return $this->i < 3;
            }

            public function key(): int
            {
                return $this->i;
            }

            public function current(): int
            {
                if ($this->i === 1 && !$this->thrown) {
                    $this->thrown = true;
                    throw new \RuntimeException('boom');
                }
                return $this->i;
            }

            public function next(): void
            {
                ++$this->i;
            }
        };
        [$a, $b] = Transform::tee($source, 2);

        foreach ([$a, $b] as $copy) {
            // When
            $values = [];
            try {
                foreach ($copy as $value) {
                    $values[] = $value;
                }
                $completed = true;
            } catch (\RuntimeException) {
                $completed = false;
            }

            // Then
            $this->assertSame([0], $values);
            $this->assertFalse($completed);
        }
    }

    /**
     * @test after a source failure whose trace holds tee copies, dropping every copy frees them all once the cycle
     *       collector runs
     */
    public function testCopiesHeldBySourceFailureTraceAreCollectedOnceAllAreDropped(): void
    {
        // Given
        $ignoreArgs = \ini_set('zend.exception_ignore_args', '0');
        $source = (function () {
            yield 1;
            yield 2;
            throw new \RuntimeException('boom');
        })();
        [$a, $b, $c] = Transform::tee($source, 3);
        $copies = [\WeakReference::create($a), \WeakReference::create($b), \WeakReference::create($c)];
        $consumeFirst = function (\Iterator $first, \Iterator $_lagging): void {
            foreach ($first as $_) {
                // no-op
            }
        };
        try {
            $consumeFirst($a, $b);
        } catch (\RuntimeException) {
        }

        // When
        unset($a, $b, $c);
        \gc_collect_cycles();
        if ($ignoreArgs !== false) {
            \ini_set('zend.exception_ignore_args', $ignoreArgs);
        }

        // Then
        foreach ($copies as $copy) {
            $this->assertNull($copy->get());
        }
    }

    /**
     * @test tee: a clone of an advanced copy yields the rest of the source, and the original is unaffected (array)
     */
    public function testCloneBranchOfAdvancedCopyYieldsTheRestArray(): void
    {
        // Given
        $data = [10, 20, 30];
        [$copy] = Transform::tee($data, 1);
        $copy->rewind();
        $copy->next();

        // When
        $clone = clone $copy;

        // Then
        $this->assertSame([1 => 20, 2 => 30], \iterator_to_array($clone));

        // And when the original is walked on (it has advanced, so it is not rewound)
        $original = [];
        while ($copy->valid()) {
            $original[$copy->key()] = $copy->current();
            $copy->next();
        }

        // Then
        $this->assertSame([1 => 20, 2 => 30], $original);
    }

    /**
     * @test tee: a clone of an advanced copy yields the rest of the source, and the original is unaffected (generator)
     */
    public function testCloneBranchOfAdvancedCopyYieldsTheRestGenerator(): void
    {
        // Given
        $data = GeneratorFixture::getGenerator([10, 20, 30]);
        [$copy] = Transform::tee($data, 1);
        $copy->rewind();
        $copy->next();

        // When
        $clone = clone $copy;

        // Then
        $this->assertSame([1 => 20, 2 => 30], \iterator_to_array($clone));

        // And when the original is walked on (it has advanced, so it is not rewound)
        $original = [];
        while ($copy->valid()) {
            $original[$copy->key()] = $copy->current();
            $copy->next();
        }

        // Then
        $this->assertSame([1 => 20, 2 => 30], $original);
    }

    /**
     * @test tee: a clone of an advanced copy yields the rest of the source, and the original is unaffected (iterator)
     */
    public function testCloneBranchOfAdvancedCopyYieldsTheRestIterator(): void
    {
        // Given
        $data = new ArrayIteratorFixture([10, 20, 30]);
        [$copy] = Transform::tee($data, 1);
        $copy->rewind();
        $copy->next();

        // When
        $clone = clone $copy;

        // Then
        $this->assertSame([1 => 20, 2 => 30], \iterator_to_array($clone));

        // And when the original is walked on (it has advanced, so it is not rewound)
        $original = [];
        while ($copy->valid()) {
            $original[$copy->key()] = $copy->current();
            $copy->next();
        }

        // Then
        $this->assertSame([1 => 20, 2 => 30], $original);
    }

    /**
     * @test tee: a clone of an advanced copy yields the rest of the source, and the original is unaffected (traversable)
     */
    public function testCloneBranchOfAdvancedCopyYieldsTheRestTraversable(): void
    {
        // Given
        $data = new IteratorAggregateFixture([10, 20, 30]);
        [$copy] = Transform::tee($data, 1);
        $copy->rewind();
        $copy->next();

        // When
        $clone = clone $copy;

        // Then
        $this->assertSame([1 => 20, 2 => 30], \iterator_to_array($clone));

        // And when the original is walked on (it has advanced, so it is not rewound)
        $original = [];
        while ($copy->valid()) {
            $original[$copy->key()] = $copy->current();
            $copy->next();
        }

        // Then
        $this->assertSame([1 => 20, 2 => 30], $original);
    }

    /**
     * @test tee: a clone of an advanced copy can be rewound until it advances, then not
     */
    public function testCloneBranchRewindsOnlyBeforeAdvancing(): void
    {
        // Given
        [$copy] = Transform::tee([10, 20, 30], 1);
        $copy->next();
        $clone = clone $copy;
        $clone->rewind();
        $clone->rewind();

        // When
        $clone->next();

        // Then
        $this->assertSame(30, $clone->current());
        $this->assertSame(20, $copy->current());
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('RelatedIterator cannot be rewinded after it has advanced');
        $clone->rewind();
    }

    /**
     * @test tee rejects a count of 0 without calling an IteratorAggregate's getIterator()
     */
    public function testLazyTeeRejectsZeroCountBeforeResolvingTheAggregate(): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        try {
            Transform::tee($aggregate, 0);
            $this->fail('Expected an InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('Count must be ≥ 1. Got 0', $e->getMessage());
        }
        $this->assertSame(0, $aggregate->getIteratorCallCount());
    }

    /**
     * @test tee rejects a count of 0 even when the IteratorAggregate's getIterator() would throw
     */
    public function testLazyTeeRejectsZeroCountOfAThrowingAggregate(): void
    {
        // Given
        $aggregate = new class () implements \IteratorAggregate {
            public function getIterator(): \Iterator
            {
                throw new \RuntimeException('getIterator must not be called');
            }
        };

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Count must be ≥ 1. Got 0');

        // When
        Transform::tee($aggregate, 0);
    }

    /**
     * @test         tee calls an IteratorAggregate's getIterator() once, on the first read of any copy
     * @dataProvider dataProviderForLazyTeeCount
     * @param        int $count
     */
    public function testLazyTeeResolvesTheAggregateOnceOnFirstRead(int $count): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $copies = Transform::tee($aggregate, $count);

        // Then
        $this->assertSame(0, $aggregate->getIteratorCallCount());

        // And when the last copy is read first
        $last = $copies[$count - 1];
        $this->assertTrue($last->valid());

        // Then
        $this->assertSame(1, $aggregate->getIteratorCallCount());

        // And when every copy is read to the end
        foreach ($copies as $copy) {
            $this->assertSame([1, 2, 3], \iterator_to_array($copy));
        }

        // Then
        $this->assertSame(1, $aggregate->getIteratorCallCount());
    }

    public static function dataProviderForLazyTeeCount(): array
    {
        return [
            'one copy'     => [1],
            'three copies' => [3],
        ];
    }

    /**
     * @test tee of an IteratorAggregate whose getIterator() throws: every copy gets that exception when first read
     */
    public function testLazyTeeGivesAThrowingAggregatesExceptionToEveryCopy(): void
    {
        // Given
        $aggregate = new class () implements \IteratorAggregate {
            public int $calls = 0;

            public function getIterator(): \Iterator
            {
                ++$this->calls;
                throw new \RuntimeException('cannot open the source');
            }
        };
        $copies = Transform::tee($aggregate, 2);

        // When
        $messages = [];
        foreach ($copies as $copy) {
            try {
                $copy->valid();
            } catch (\RuntimeException $e) {
                $messages[] = $e->getMessage();
            }
        }

        // Then
        $this->assertSame(['cannot open the source', 'cannot open the source'], $messages);
        $this->assertSame(1, $aggregate->calls);
    }

    /**
     * Regression: the source is resolved on the first read, so a copy read from inside getIterator() re-entered
     * that read. It found the iterable already released, resolved it as empty, and the tee ended after one element.
     *
     * @test a tee copy read from inside the source's getIterator() throws the re-entrancy guard to every copy, as memoize does
     */
    public function testReentrantReadFromAggregateFactoryThrowsTheSharedGuardFailureAsMemoizeDoes(): void
    {
        // Given
        $b = null;
        $teeSource = self::aggregateThatReadsWhileResolving(static function () use (&$b): void {
            $b->valid();
        });
        [$a, $b] = Transform::tee($teeSource, 2);

        // And
        $clone = null;
        $memoizeSource = self::aggregateThatReadsWhileResolving(static function () use (&$clone): void {
            $clone->getIterator()->valid();
        });
        $memoized = Transform::memoize($memoizeSource);
        $clone = clone $memoized;

        // When
        [$valuesA, $failureA] = self::readFromWhereItIs($a);
        [$valuesB, $failureB] = self::readFromWhereItIs($b);
        [$memoizedValues, $memoizedFailure] = self::readFromWhereItIs($memoized->getIterator());
        [$clonedValues, $clonedFailure] = self::readFromWhereItIs($clone->getIterator());

        // Then
        $this->assertSame([], $valuesA);
        $this->assertInstanceOf(\LogicException::class, $failureA);
        $this->assertSame('Tee iterator cannot advance its source re-entrantly', $failureA->getMessage());
        $this->assertSame([], $valuesB);
        $this->assertSame($failureA, $failureB);
        $this->assertSame(1, $teeSource->calls);

        // And
        $this->assertSame([], $memoizedValues);
        $this->assertInstanceOf(\LogicException::class, $memoizedFailure);
        $this->assertSame('Memoized iterable cannot advance its source re-entrantly', $memoizedFailure->getMessage());
        $this->assertSame([], $clonedValues);
        $this->assertSame($memoizedFailure, $clonedFailure);
        $this->assertSame(1, $memoizeSource->calls);
    }

    /**
     * Regression: a copy read from inside the source's valid() re-entered the read in progress, which buffered the
     * first element, and the outer read then buffered it again.
     *
     * @test a tee copy read from inside a source iterator's method throws the re-entrancy guard to every copy, as memoize does
     */
    public function testReentrantReadFromIteratorMethodThrowsTheSharedGuardFailureAsMemoizeDoes(): void
    {
        // Given
        $b = null;
        $teeSource = self::iteratorThatReadsOnFirstValid(static function () use (&$b): void {
            $b->current();
        });
        [$a, $b] = Transform::tee($teeSource, 2);

        // And
        $clone = null;
        $memoizeSource = self::iteratorThatReadsOnFirstValid(static function () use (&$clone): void {
            $clone->getIterator()->current();
        });
        $memoized = Transform::memoize($memoizeSource);
        $clone = clone $memoized;

        // When
        [$valuesA, $failureA] = self::readFromWhereItIs($a);
        [$valuesB, $failureB] = self::readFromWhereItIs($b);
        [$memoizedValues, $memoizedFailure] = self::readFromWhereItIs($memoized->getIterator());
        [$clonedValues, $clonedFailure] = self::readFromWhereItIs($clone->getIterator());

        // Then
        $this->assertSame([], $valuesA);
        $this->assertInstanceOf(\LogicException::class, $failureA);
        $this->assertSame('Tee iterator cannot advance its source re-entrantly', $failureA->getMessage());
        $this->assertSame([], $valuesB);
        $this->assertSame($failureA, $failureB);
        $this->assertSame(1, $teeSource->validCalls);

        // And
        $this->assertSame([], $memoizedValues);
        $this->assertInstanceOf(\LogicException::class, $memoizedFailure);
        $this->assertSame('Memoized iterable cannot advance its source re-entrantly', $memoizedFailure->getMessage());
        $this->assertSame([], $clonedValues);
        $this->assertSame($memoizedFailure, $clonedFailure);
        $this->assertSame(1, $memoizeSource->validCalls);
    }

    /**
     * Regression: a copy read from inside a running generator recorded "Cannot resume an already running
     * generator" as the source failure and dropped the source. The generator caught it, so no copy ever threw it,
     * and the tee silently ended after one element.
     *
     * @test a re-entrant tee read that the source generator catches leaves the rest of the source to every copy, as memoize does
     */
    public function testReentrantReadCaughtByGeneratorKeepsTheRestOfTheSourceAsMemoizeDoes(): void
    {
        // Given
        $b = null;
        $teeCaught = null;
        $teeSource = (static function () use (&$b, &$teeCaught): \Generator {
            yield 10;
            try {
                $b->next();
                $b->valid();
            } catch (\Throwable $e) {
                $teeCaught = $e;
            }
            yield 20;
            yield 30;
        })();
        [$a, $b] = Transform::tee($teeSource, 2);

        // And
        $clone = null;
        $memoizeCaught = null;
        $memoizeSource = (static function () use (&$clone, &$memoizeCaught): \Generator {
            yield 10;
            try {
                foreach ($clone as $_value) {
                }
            } catch (\Throwable $e) {
                $memoizeCaught = $e;
            }
            yield 20;
            yield 30;
        })();
        $memoized = Transform::memoize($memoizeSource);
        $clone = clone $memoized;

        // When
        [$valuesA, $failureA] = self::readFromWhereItIs($a);
        [$valuesB, $failureB] = self::readFromWhereItIs($b);
        [$memoizedValues, $memoizedFailure] = self::readFromWhereItIs($memoized->getIterator());

        // Then
        $this->assertSame([10, 20, 30], $valuesA);
        $this->assertNull($failureA);
        $this->assertInstanceOf(\LogicException::class, $teeCaught);
        $this->assertSame('Tee iterator cannot advance its source re-entrantly', $teeCaught->getMessage());
        $this->assertSame([20, 30], $valuesB);
        $this->assertNull($failureB);

        // And
        $this->assertSame([10, 20, 30], $memoizedValues);
        $this->assertNull($memoizedFailure);
        $this->assertInstanceOf(\LogicException::class, $memoizeCaught);
        $this->assertSame('Memoized iterable cannot advance its source re-entrantly', $memoizeCaught->getMessage());
    }

    /**
     * @test a tee copy can read an element already buffered from inside the source generator (no re-entrant advance)
     */
    public function testReentrantReadOfABufferedElementStillWorksGenerator(): void
    {
        // Given
        $b = null;
        $read = null;
        $source = (static function () use (&$b, &$read): \Generator {
            yield 10;
            $read = $b->current();
            yield 20;
            yield 30;
        })();
        [$a, $b] = Transform::tee($source, 2);

        // When
        [$valuesA, $failureA] = self::readFromWhereItIs($a);
        [$valuesB, $failureB] = self::readFromWhereItIs($b);

        // Then
        $this->assertSame(10, $read);
        $this->assertSame([10, 20, 30], $valuesA);
        $this->assertNull($failureA);
        $this->assertSame([10, 20, 30], $valuesB);
        $this->assertNull($failureB);
    }

    /**
     * @test a tee copy can read an element already buffered from inside a source iterator's next() (no re-entrant advance)
     */
    public function testReentrantReadOfABufferedElementStillWorksIterator(): void
    {
        // Given
        $b = null;
        $read = null;
        $source = self::iteratorThatReadsOnFirstNext(static function () use (&$b, &$read): void {
            $read = $b->current();
        });
        [$a, $b] = Transform::tee($source, 2);

        // When
        [$valuesA, $failureA] = self::readFromWhereItIs($a);
        [$valuesB, $failureB] = self::readFromWhereItIs($b);

        // Then
        $this->assertSame(10, $read);
        $this->assertSame([10, 20, 30], $valuesA);
        $this->assertNull($failureA);
        $this->assertSame([10, 20, 30], $valuesB);
        $this->assertNull($failureB);
    }

    /**
     * Read an iterator from where it is, with valid() / current() / next() and no rewind(): rewinding a tee copy
     * that has advanced throws.
     *
     * @return array{0: list<mixed>, 1: \Throwable|null} the values read, and what the read threw, if anything
     */
    private static function readFromWhereItIs(\Iterator $iterator): array
    {
        $values = [];
        try {
            for (; $iterator->valid(); $iterator->next()) {
                $values[] = $iterator->current();
            }
        } catch (\Throwable $failure) {
            return [$values, $failure];
        }

        return [$values, null];
    }

    /**
     * An aggregate of [10, 20, 30] whose getIterator() calls $read before returning the iterator, and counts its calls.
     */
    private static function aggregateThatReadsWhileResolving(\Closure $read): \IteratorAggregate
    {
        return new class ($read) implements \IteratorAggregate {
            public int $calls = 0;

            public function __construct(private readonly \Closure $read)
            {
            }

            public function getIterator(): \Iterator
            {
                ++$this->calls;
                ($this->read)();

                return new \ArrayIterator([10, 20, 30]);
            }
        };
    }

    /**
     * An iterator over [10, 20, 30] whose first valid() calls $read, and which counts its valid() calls.
     */
    private static function iteratorThatReadsOnFirstValid(\Closure $read): \Iterator
    {
        return new class ([10, 20, 30], $read) extends \ArrayIterator {
            public int $validCalls = 0;
            private bool $hasRead = false;

            public function __construct(array $array, private readonly \Closure $read)
            {
                parent::__construct($array);
            }

            public function valid(): bool
            {
                ++$this->validCalls;
                if (!$this->hasRead) {
                    // Set first, so that a valid() call from inside $read does not read again.
                    $this->hasRead = true;
                    ($this->read)();
                }

                return parent::valid();
            }
        };
    }

    /**
     * An iterator over [10, 20, 30] whose first next() calls $read before advancing.
     */
    private static function iteratorThatReadsOnFirstNext(\Closure $read): \Iterator
    {
        return new class ([10, 20, 30], $read) extends \ArrayIterator {
            private bool $hasRead = false;

            public function __construct(array $array, private readonly \Closure $read)
            {
                parent::__construct($array);
            }

            public function next(): void
            {
                if (!$this->hasRead) {
                    $this->hasRead = true;
                    ($this->read)();
                }
                parent::next();
            }
        };
    }
}
