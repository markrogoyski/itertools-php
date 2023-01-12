<?php

declare(strict_types=1);

namespace IterTools\Tests\Chain;

use IterTools\Chain;
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
        $result = [];

        // When
        $chain = $chainMaker($input);
        foreach ($chain as $key => $value) {
            $result[$key] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                static function (iterable $iterable) {
                    return Chain::create($iterable);
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Chain::create($iterable);
                },
                [1, -1, 2, -2, 3, -3],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        });
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        });
                },
                [1, 2, 3],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        });
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        });
                },
                [1, -1, 2, -2],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1, 0, 1]);
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1, 0, 1]);
                },
                [-1, -2],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->dropWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1]);
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->dropWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1]);
                },
                [-3],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1]);
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1]);
                },
                [2, 3],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1]);
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1]);
                },
                [2, 3],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        });
                },
                [-1, -2, -3],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [[1, 2], [2, 3]],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [[-1, -2], [-2, -3]],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value % 2 === 0;
                        })
                        ->groupBy(static function ($item) {
                            return $item > 0 ? 'pos' : 'neg';
                        });
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value % 2 === 0;
                        })
                        ->groupBy(static function ($item) {
                            return $item > 0 ? 'pos' : 'neg';
                        });
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
        $result = [];

        // When
        $chain = $chainMaker($input);
        foreach ($chain as $key => $value) {
            $result[$key] = $value;
        }

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
                    return Chain::create($iterable);
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable);
                },
                [1, -1, 2, -2, 3, -3],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        });
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        });
                },
                [1, 2, 3],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        });
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        });
                },
                [1, -1, 2, -2],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1, 0, 1]);
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1, 0, 1]);
                },
                [-1, -2],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->dropWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1]);
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->dropWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1]);
                },
                [-3],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1]);
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1]);
                },
                [2, 3],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1]);
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1]);
                },
                [2, 3],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        });
                },
                [-1, -2, -3],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [[1, 2], [2, 3]],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [[-1, -2], [-2, -3]],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value % 2 === 0;
                        })
                        ->groupBy(static function ($item) {
                            return $item > 0 ? 'pos' : 'neg';
                        });
                },
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value % 2 === 0;
                        })
                        ->groupBy(static function ($item) {
                            return $item > 0 ? 'pos' : 'neg';
                        });
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
        $result = [];

        // When
        $chain = $chainMaker($input);
        foreach ($chain as $key => $value) {
            $result[$key] = $value;
        }

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
                    return Chain::create($iterable);
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable);
                },
                [1, -1, 2, -2, 3, -3],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        });
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        });
                },
                [1, 2, 3],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        });
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        });
                },
                [1, -1, 2, -2],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1, 0, 1]);
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1, 0, 1]);
                },
                [-1, -2],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->dropWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1]);
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->dropWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1]);
                },
                [-3],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1]);
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1]);
                },
                [2, 3],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1]);
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1]);
                },
                [2, 3],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        });
                },
                [-1, -2, -3],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [[1, 2], [2, 3]],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [[-1, -2], [-2, -3]],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value % 2 === 0;
                        })
                        ->groupBy(static function ($item) {
                            return $item > 0 ? 'pos' : 'neg';
                        });
                },
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value % 2 === 0;
                        })
                        ->groupBy(static function ($item) {
                            return $item > 0 ? 'pos' : 'neg';
                        });
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
        $result = [];

        // When
        $chain = $chainMaker($input);
        foreach ($chain as $key => $value) {
            $result[$key] = $value;
        }

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
                    return Chain::create($iterable);
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable);
                },
                [1, -1, 2, -2, 3, -3],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        });
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        });
                },
                [1, 2, 3],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        });
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        });
                },
                [1, -1, 2, -2],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1, 0, 1]);
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1, 0, 1]);
                },
                [-1, -2],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->dropWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1]);
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->dropWhile(static function ($value) {
                            return abs($value) < 3;
                        })
                        ->compress([0, 1]);
                },
                [-3],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1]);
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1]);
                },
                [2, 3],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1]);
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->compress([0, 1, 1]);
                },
                [2, 3],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        });
                },
                [-1, -2, -3],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [[1, 2], [2, 3]],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [[-1, -2], [-2, -3]],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value % 2 === 0;
                        })
                        ->groupBy(static function ($item) {
                            return $item > 0 ? 'pos' : 'neg';
                        });
                },
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value % 2 === 0;
                        })
                        ->groupBy(static function ($item) {
                            return $item > 0 ? 'pos' : 'neg';
                        });
                },
                ['pos' => [1, 3], 'neg' => [-1, -3]],
            ],
        ];
    }
}
