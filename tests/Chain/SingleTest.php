<?php

declare(strict_types=1);

namespace IterTools\Tests\Chain;

use IterTools\Chain;

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
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        });
                },
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->takeWhile(static function ($value) {
                            return abs($value) < 3;
                        });
                },
                [1, -1, 2, -2],
            ],
            [
                [],
                static function (array $iterable) {
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
                static function (array $iterable) {
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
                static function (array $iterable) {
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
                static function (array $iterable) {
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
                static function (array $iterable) {
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
                static function (array $iterable) {
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
                static function (array $iterable) {
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
                static function (array $iterable) {
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
            [
                [],
                static function (array $iterable) {
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
                static function (array $iterable) {
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
