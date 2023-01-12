<?php

declare(strict_types=1);

namespace IterTools\Tests\Chain;

use IterTools\Chain;

class SingleAndReduceTest extends \PHPUnit\Framework\TestCase
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
        foreach ($chain as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable);
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable);
                },
                [1, -1, 2, -2, 3, -3],
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        });
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        });
                },
                [1, 2, 3],
            ],
            [
                [],
                static function (array $iterable) {
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
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        });
                },
                [-1, -2, -3],
            ],
            [
                [],
                static function (array $iterable) {
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
                static function (array $iterable) {
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
                static function (array $iterable) {
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
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->pairwise();
                },
                [[-1, -2], [-2, -3]],
            ],
        ];
    }
    /**
     * @param array $input
     * @param callable $reducer
     * @param mixed $expected
     * @return void
     * @dataProvider dataProviderForArrayReduce
     */
    public function testArrayReduce(array $input, callable $reducer, $expected): void
    {
        // Given
        // When
        $result = $reducer($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArrayReduce(): array
    {
        return [
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)->toCount();
                },
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)->toCount();
                },
                6,
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                3,
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)
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
                static function (array $iterable) {
                    return Chain::create($iterable)
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
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toCount();
                },
                3,
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)
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
                static function (array $iterable) {
                    return Chain::create($iterable)
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
                static function (array $iterable) {
                    return Chain::create($iterable)->toMax();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)->toMax();
                },
                3,
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                3,
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMax();
                },
                -1,
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)->toMin();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)->toMin();
                },
                -3,
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                1,
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toMin();
                },
                -3,
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)->toProduct();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)->toProduct();
                },
                -36,
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                6,
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toProduct();
                },
                -6,
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)->toSum();
                },
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)->toSum();
                },
                0,
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                6,
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toSum();
                },
                -6,
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                null,
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        });
                },
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                1,
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)
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
                static function (array $iterable) {
                    return Chain::create($iterable)
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
                static function (array $iterable) {
                    return Chain::create($iterable)
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
                static function (array $iterable) {
                    return Chain::create($iterable)
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
                static function (array $iterable) {
                    return Chain::create($iterable)
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
                static function (array $iterable) {
                    return Chain::create($iterable)
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
                static function (array $iterable) {
                    return Chain::create($iterable)
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
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterFalse(static function ($value) {
                            return $value > 0;
                        })
                        ->toValue(function ($carry, $item) {
                            return $carry + $item;
                        }, 1);
                },
                -5,
            ],
        ];
    }
}
