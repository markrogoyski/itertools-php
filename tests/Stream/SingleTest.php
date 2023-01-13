<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class SingleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param array $input
     * @param callable $chainMaker
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForArray
     */
    public function testArray(array $input, callable $chainMaker, array $expected): void
    {
        // Given
        $result = $chainMaker($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toAssociativeArray();
                },
                [1, -1, 2, -2, 3, -3],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toAssociativeArray();
                },
                [1, 2, 3],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->toAssociativeArray();
                },
                [1, -1, 2, -2],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1, 0, 1])
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1, 0, 1])
                        ->toAssociativeArray();
                },
                [-1, -2],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->dropWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1])
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->dropWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1])
                        ->toAssociativeArray();
                },
                [-3],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1])
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1])
                        ->toAssociativeArray();
                },
                [2, 3],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1])
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1])
                        ->toAssociativeArray();
                },
                [2, 3],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toAssociativeArray();
                },
                [-1, -2, -3],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [[1, 2], [2, 3]],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [[-1, -2], [-2, -3]],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value % 2 === 0;
                        })
                        ->groupBy(static function ($item) {
                            return $item > 0 ? 'pos' : 'neg';
                        })
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value % 2 === 0;
                        })
                        ->groupBy(static function ($item) {
                            return $item > 0 ? 'pos' : 'neg';
                        })
                        ->toAssociativeArray();
                },
                ['pos' => [1, 3], 'neg' => [-1, -3]],
            ],
        ];
    }

    /**
     * @param \Generator $input
     * @param callable $chainMaker
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForGenerator
     */
    public function testGenerator(\Generator $input, callable $chainMaker, array $expected): void
    {
        // Given
        $result = $chainMaker($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForGenerator(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toAssociativeArray();
                },
                [1, -1, 2, -2, 3, -3],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toAssociativeArray();
                },
                [1, 2, 3],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->toAssociativeArray();
                },
                [1, -1, 2, -2],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1, 0, 1])
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1, 0, 1])
                        ->toAssociativeArray();
                },
                [-1, -2],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->dropWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1])
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->dropWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1])
                        ->toAssociativeArray();
                },
                [-3],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1])
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1])
                        ->toAssociativeArray();
                },
                [2, 3],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1])
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1])
                        ->toAssociativeArray();
                },
                [2, 3],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toAssociativeArray();
                },
                [-1, -2, -3],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [[1, 2], [2, 3]],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [[-1, -2], [-2, -3]],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value % 2 === 0;
                        })
                        ->groupBy(static function ($item) {
                            return $item > 0 ? 'pos' : 'neg';
                        })
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value % 2 === 0;
                        })
                        ->groupBy(static function ($item) {
                            return $item > 0 ? 'pos' : 'neg';
                        })
                        ->toAssociativeArray();
                },
                ['pos' => [1, 3], 'neg' => [-1, -3]],
            ],
        ];
    }

    /**
     * @param \Iterator $input
     * @param callable $chainMaker
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForIterator
     */
    public function testIterator(\Iterator $input, callable $chainMaker, array $expected): void
    {
        // Given
        $result = $chainMaker($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForIterator(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toAssociativeArray();
                },
                [1, -1, 2, -2, 3, -3],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toAssociativeArray();
                },
                [1, 2, 3],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->toAssociativeArray();
                },
                [1, -1, 2, -2],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1, 0, 1])
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1, 0, 1])
                        ->toAssociativeArray();
                },
                [-1, -2],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->dropWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1])
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->dropWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1])
                        ->toAssociativeArray();
                },
                [-3],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1])
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1])
                        ->toAssociativeArray();
                },
                [2, 3],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1])
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1])
                        ->toAssociativeArray();
                },
                [2, 3],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toAssociativeArray();
                },
                [-1, -2, -3],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [[1, 2], [2, 3]],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [[-1, -2], [-2, -3]],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value % 2 === 0;
                        })
                        ->groupBy(static function ($item) {
                            return $item > 0 ? 'pos' : 'neg';
                        })
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value % 2 === 0;
                        })
                        ->groupBy(static function ($item) {
                            return $item > 0 ? 'pos' : 'neg';
                        })
                        ->toAssociativeArray();
                },
                ['pos' => [1, 3], 'neg' => [-1, -3]],
            ],
        ];
    }

    /**
     * @param \Traversable $input
     * @param callable $chainMaker
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForTraversable
     */
    public function testTraversable(\Traversable $input, callable $chainMaker, array $expected): void
    {
        // Given
        // When
        $result = $chainMaker($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForTraversable(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toAssociativeArray();
                },
                [1, -1, 2, -2, 3, -3],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toAssociativeArray();
                },
                [1, 2, 3],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->toAssociativeArray();
                },
                [1, -1, 2, -2],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1, 0, 1])
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1, 0, 1])
                        ->toAssociativeArray();
                },
                [-1, -2],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->dropWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1])
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->dropWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1])
                        ->toAssociativeArray();
                },
                [-3],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1])
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1])
                        ->toAssociativeArray();
                },
                [2, 3],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1])
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1])
                        ->toAssociativeArray();
                },
                [2, 3],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toAssociativeArray();
                },
                [-1, -2, -3],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [[1, 2], [2, 3]],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toAssociativeArray();
                },
                [[-1, -2], [-2, -3]],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value % 2 === 0;
                        })
                        ->groupBy(static function ($item) {
                            return $item > 0 ? 'pos' : 'neg';
                        })
                        ->toAssociativeArray();
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value % 2 === 0;
                        })
                        ->groupBy(static function ($item) {
                            return $item > 0 ? 'pos' : 'neg';
                        })
                        ->toAssociativeArray();
                },
                ['pos' => [1, 3], 'neg' => [-1, -3]],
            ],
        ];
    }
}
