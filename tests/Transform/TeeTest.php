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

    public function dataProviderForArray(): array
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

    public function dataProviderForGenerators(): array
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

    public function dataProviderForIterators(): array
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

    public function dataProviderForNestedIterators(): array
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

    public function dataProviderForTraversables(): array
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
        $this->assertEquals($relatedCount, count($iterators));

        // And When
        foreach ($iterators as $iterator) {
            // Then
            $this->assertEquals($data, \iterator_to_array($iterator));
        }
    }
}
