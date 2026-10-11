<?php

declare(strict_types=1);

namespace IterTools\Tests\Transform;

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
}
