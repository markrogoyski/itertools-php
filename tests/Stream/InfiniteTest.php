<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class InfiniteTest extends \PHPUnit\Framework\TestCase
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

        $i = 0;
        foreach ($chain as $value) {
            $result[] = $value;

            if (count($expected) > 0 && $i === count($expected)-1) {
                break;
            }

            ++$i;
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
                    return Stream::of($iterable)->infiniteCycle();
                },
                [],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($item) {
                            return $item < 0;
                        })
                        ->infiniteCycle();
                },
                [],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (iterable $iterable) {
                    return Stream::of($iterable)->infiniteCycle();
                },
                [1, 2, 3, 4, 5, 1, 2, 3, 4, 5, 1, 2, 3, 4, 5],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($item) {
                            return $item % 2 !== 0;
                        })
                        ->infiniteCycle();
                },
                [1, 3, 5, 1, 3, 5, 1, 3, 5],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($item) {
                            return $item % 2 !== 0;
                        })
                        ->infiniteCycle()
                        ->runningMax();
                },
                [1, 3, 5, 5, 5, 5, 5, 5, 5],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->infiniteCycle()
                        ->runningTotal();
                },
                [1, 3, 6, 10, 15, 16, 18, 21, 25, 30, 31, 33, 36, 40, 45],
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

        $i = 0;
        foreach ($chain as $value) {
            $result[] = $value;

            if (count($expected) > 0 && $i === count($expected)-1) {
                break;
            }

            ++$i;
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
                    return Stream::of($iterable)->infiniteCycle();
                },
                [],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($item) {
                            return $item < 0;
                        })
                        ->infiniteCycle();
                },
                [],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->infiniteCycle();
                },
                [1, 2, 3, 4, 5, 1, 2, 3, 4, 5, 1, 2, 3, 4, 5],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($item) {
                            return $item % 2 !== 0;
                        })
                        ->infiniteCycle();
                },
                [1, 3, 5, 1, 3, 5, 1, 3, 5],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($item) {
                            return $item % 2 !== 0;
                        })
                        ->infiniteCycle()
                        ->runningMax();
                },
                [1, 3, 5, 5, 5, 5, 5, 5, 5],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->infiniteCycle()
                        ->runningTotal();
                },
                [1, 3, 6, 10, 15, 16, 18, 21, 25, 30, 31, 33, 36, 40, 45],
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

        $i = 0;
        foreach ($chain as $value) {
            $result[] = $value;

            if (count($expected) > 0 && $i === count($expected)-1) {
                break;
            }

            ++$i;
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
                    return Stream::of($iterable)->infiniteCycle();
                },
                [],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($item) {
                            return $item < 0;
                        })
                        ->infiniteCycle();
                },
                [],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->infiniteCycle();
                },
                [1, 2, 3, 4, 5, 1, 2, 3, 4, 5, 1, 2, 3, 4, 5],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($item) {
                            return $item % 2 !== 0;
                        })
                        ->infiniteCycle();
                },
                [1, 3, 5, 1, 3, 5, 1, 3, 5],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($item) {
                            return $item % 2 !== 0;
                        })
                        ->infiniteCycle()
                        ->runningMax();
                },
                [1, 3, 5, 5, 5, 5, 5, 5, 5],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->infiniteCycle()
                        ->runningTotal();
                },
                [1, 3, 6, 10, 15, 16, 18, 21, 25, 30, 31, 33, 36, 40, 45],
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

        $i = 0;
        foreach ($chain as $value) {
            $result[] = $value;

            if (count($expected) > 0 && $i === count($expected)-1) {
                break;
            }

            ++$i;
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
                    return Stream::of($iterable)->infiniteCycle();
                },
                [],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($item) {
                            return $item < 0;
                        })
                        ->infiniteCycle();
                },
                [],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)->infiniteCycle();
                },
                [1, 2, 3, 4, 5, 1, 2, 3, 4, 5, 1, 2, 3, 4, 5],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($item) {
                            return $item % 2 !== 0;
                        })
                        ->infiniteCycle();
                },
                [1, 3, 5, 1, 3, 5, 1, 3, 5],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->filterTrue(static function ($item) {
                            return $item % 2 !== 0;
                        })
                        ->infiniteCycle()
                        ->runningMax();
                },
                [1, 3, 5, 5, 5, 5, 5, 5, 5],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Stream::of($iterable)
                        ->infiniteCycle()
                        ->runningTotal();
                },
                [1, 3, 6, 10, 15, 16, 18, 21, 25, 30, 31, 33, 36, 40, 45],
            ],
        ];
    }
}
