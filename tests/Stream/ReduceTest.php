<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ReduceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param array $input
     * @param callable $reducer
     * @param mixed $expected
     * @return void
     * @dataProvider dataProviderForArray
     */
    public function testArray(array $input, callable $reducer, $expected): void
    {
        // Given
        // When
        $result = $reducer($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toCount();
                },
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toCount();
                },
                6,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                3,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toCount();
                },
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toCount();
                },
                2,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                3,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toCount();
                },
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toCount();
                },
                2,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toAverage();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toAverage();
                },
                0,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toAverage();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toAverage();
                },
                2,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toAverage();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toAverage();
                },
                -2,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toMax();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toMax();
                },
                3,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                3,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                -1,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toMin();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toMin();
                },
                -3,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                1,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                -3,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toProduct();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toProduct();
                },
                -36,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                6,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                -6,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toSum();
                },
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toSum();
                },
                0,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                6,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                -6,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                null,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                null,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                6,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                7,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                null,
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                -6,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                -5,
            ],
            [
                [1, 2, 3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->zipWith(
                            [10, 20, 30],
                            [100, 200, 300]
                        )
                        ->toValue(function ($carry, $item) {
                            return $carry + array_sum($item);
                        }, 0);
                },
                666,
            ],
            [
                [1, 2, 3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->zipEqualWith(
                            [10, 20, 30],
                            [100, 200, 300]
                        )
                        ->toValue(function ($carry, $item) {
                            return $carry + array_sum($item);
                        }, 0);
                },
                666,
            ],
            [
                [1, 2, 3, 4, 5],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->zipWith(
                            [10, 20, 30],
                            [100, 200, 300]
                        )
                        ->toValue(function ($carry, $item) {
                            return $carry + array_sum($item);
                        }, 0);
                },
                666,
            ],
            [
                [1, 2, 3, 4, 5],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->zipLongestWith(
                            [10, 20, 30],
                            [100, 200, 300]
                        )
                        ->toValue(function ($carry, $item) {
                            return $carry + array_sum($item);
                        }, 0);
                },
                675,
            ],
            [
                [1, 2, 3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->chainWith(
                            [4, 5, 6],
                            [7, 8, 9]
                        )
                        ->toSum();
                },
                45,
            ],
            [
                [1, 2, 3],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->chainWith(
                            [4, 5, 6],
                            [7, 8, 9]
                        )
                        ->zipEqualWith([1, 2, 3, 4, 5, 6, 7, 8, 9])
                        ->toValue(static function ($carry, $item) {
                            return $carry + array_sum($item);
                        });
                },
                90,
            ],
        ];
    }

    /**
     * @param \Generator $input
     * @param callable $reducer
     * @param mixed $expected
     * @return void
     * @dataProvider dataProviderForGenerator
     */
    public function testGenerator(\Generator $input, callable $reducer, $expected): void
    {
        // Given
        // When
        $result = $reducer($input);

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
                    return Stream::of($iterable)->toCount();
                },
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toCount();
                },
                6,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                3,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toCount();
                },
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toCount();
                },
                2,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                3,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toCount();
                },
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toCount();
                },
                2,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toAverage();
                },
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toAverage();
                },
                0,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toAverage();
                },
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toAverage();
                },
                2,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toAverage();
                },
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toAverage();
                },
                -2,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toMax();
                },
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toMax();
                },
                3,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                3,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                -1,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toMin();
                },
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toMin();
                },
                -3,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                1,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                -3,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toProduct();
                },
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toProduct();
                },
                -36,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                6,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                -6,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toSum();
                },
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toSum();
                },
                0,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                6,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                -6,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                null,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                null,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                6,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                7,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                null,
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                -6,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                -5,
            ],
            [
                $gen([1, 2, 3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->zipWith(
                            [10, 20, 30],
                            [100, 200, 300]
                        )
                        ->toValue(function ($carry, $item) {
                            return $carry + array_sum($item);
                        }, 0);
                },
                666,
            ],
            [
                $gen([1, 2, 3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->zipEqualWith(
                            [10, 20, 30],
                            [100, 200, 300]
                        )
                        ->toValue(function ($carry, $item) {
                            return $carry + array_sum($item);
                        }, 0);
                },
                666,
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->zipWith(
                            [10, 20, 30],
                            [100, 200, 300]
                        )
                        ->toValue(function ($carry, $item) {
                            return $carry + array_sum($item);
                        }, 0);
                },
                666,
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->zipLongestWith(
                            [10, 20, 30],
                            [100, 200, 300]
                        )
                        ->toValue(function ($carry, $item) {
                            return $carry + array_sum($item);
                        }, 0);
                },
                675,
            ],
            [
                $gen([1, 2, 3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->chainWith(
                            [4, 5, 6],
                            [7, 8, 9]
                        )
                        ->toSum();
                },
                45,
            ],
            [
                $gen([1, 2, 3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->chainWith(
                            [4, 5, 6],
                            [7, 8, 9]
                        )
                        ->zipEqualWith([1, 2, 3, 4, 5, 6, 7, 8, 9])
                        ->toValue(static function ($carry, $item) {
                            return $carry + array_sum($item);
                        });
                },
                90,
            ],
        ];
    }

    /**
     * @param \Iterator $input
     * @param callable $reducer
     * @param mixed $expected
     * @return void
     * @dataProvider dataProviderForIterator
     */
    public function testIterator(\Iterator $input, callable $reducer, $expected): void
    {
        // Given
        // When
        $result = $reducer($input);

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
                    return Stream::of($iterable)->toCount();
                },
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toCount();
                },
                6,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                3,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toCount();
                },
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toCount();
                },
                2,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                3,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toCount();
                },
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toCount();
                },
                2,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toAverage();
                },
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toAverage();
                },
                0,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toAverage();
                },
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toAverage();
                },
                2,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toAverage();
                },
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toAverage();
                },
                -2,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toMax();
                },
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toMax();
                },
                3,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                3,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                -1,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toMin();
                },
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toMin();
                },
                -3,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                1,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                -3,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toProduct();
                },
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toProduct();
                },
                -36,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                6,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                -6,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toSum();
                },
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toSum();
                },
                0,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                6,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                -6,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                null,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                null,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                6,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                7,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                null,
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                -6,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                -5,
            ],
            [
                $iter([1, 2, 3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->zipWith(
                            [10, 20, 30],
                            [100, 200, 300]
                        )
                        ->toValue(function ($carry, $item) {
                            return $carry + array_sum($item);
                        }, 0);
                },
                666,
            ],
            [
                $iter([1, 2, 3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->zipEqualWith(
                            [10, 20, 30],
                            [100, 200, 300]
                        )
                        ->toValue(function ($carry, $item) {
                            return $carry + array_sum($item);
                        }, 0);
                },
                666,
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->zipWith(
                            [10, 20, 30],
                            [100, 200, 300]
                        )
                        ->toValue(function ($carry, $item) {
                            return $carry + array_sum($item);
                        }, 0);
                },
                666,
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->zipLongestWith(
                            [10, 20, 30],
                            [100, 200, 300]
                        )
                        ->toValue(function ($carry, $item) {
                            return $carry + array_sum($item);
                        }, 0);
                },
                675,
            ],
            [
                $iter([1, 2, 3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->chainWith(
                            [4, 5, 6],
                            [7, 8, 9]
                        )
                        ->toSum();
                },
                45,
            ],
            [
                $iter([1, 2, 3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->chainWith(
                            [4, 5, 6],
                            [7, 8, 9]
                        )
                        ->zipEqualWith([1, 2, 3, 4, 5, 6, 7, 8, 9])
                        ->toValue(static function ($carry, $item) {
                            return $carry + array_sum($item);
                        });
                },
                90,
            ],
        ];
    }

    /**
     * @param \Traversable $input
     * @param callable $reducer
     * @param mixed $expected
     * @return void
     * @dataProvider dataProviderForTraversable
     */
    public function testTraversable(\Traversable $input, callable $reducer, $expected): void
    {
        // Given
        // When
        $result = $reducer($input);

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
                    return Stream::of($iterable)->toCount();
                },
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toCount();
                },
                6,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                3,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toCount();
                },
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toCount();
                },
                2,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                3,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toCount();
                },
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise()
                        ->toCount();
                },
                2,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toAverage();
                },
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toAverage();
                },
                0,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toAverage();
                },
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toAverage();
                },
                2,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toAverage();
                },
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toAverage();
                },
                -2,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toMax();
                },
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toMax();
                },
                3,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                3,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                -1,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toMin();
                },
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toMin();
                },
                -3,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                1,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                -3,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toProduct();
                },
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toProduct();
                },
                -36,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                6,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                -6,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toSum();
                },
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->toSum();
                },
                0,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                6,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                -6,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                null,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                null,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                6,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                7,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                null,
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                -6,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                -5,
            ],
            [
                $trav([1, 2, 3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->zipWith(
                            [10, 20, 30],
                            [100, 200, 300]
                        )
                        ->toValue(function ($carry, $item) {
                            return $carry + array_sum($item);
                        }, 0);
                },
                666,
            ],
            [
                $trav([1, 2, 3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->zipEqualWith(
                            [10, 20, 30],
                            [100, 200, 300]
                        )
                        ->toValue(function ($carry, $item) {
                            return $carry + array_sum($item);
                        }, 0);
                },
                666,
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->zipWith(
                            [10, 20, 30],
                            [100, 200, 300]
                        )
                        ->toValue(function ($carry, $item) {
                            return $carry + array_sum($item);
                        }, 0);
                },
                666,
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->zipLongestWith(
                            [10, 20, 30],
                            [100, 200, 300]
                        )
                        ->toValue(function ($carry, $item) {
                            return $carry + array_sum($item);
                        }, 0);
                },
                675,
            ],
            [
                $trav([1, 2, 3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->chainWith(
                            [4, 5, 6],
                            [7, 8, 9]
                        )
                        ->toSum();
                },
                45,
            ],
            [
                $trav([1, 2, 3]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->chainWith(
                            [4, 5, 6],
                            [7, 8, 9]
                        )
                        ->zipEqualWith([1, 2, 3, 4, 5, 6, 7, 8, 9])
                        ->toValue(static function ($carry, $item) {
                            return $carry + array_sum($item);
                        });
                },
                90,
            ],
        ];
    }
}
