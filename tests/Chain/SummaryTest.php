<?php

declare(strict_types=1);

namespace IterTools\Tests\Chain;

use IterTools\Chain;

class SummaryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param array $input
     * @param callable $reducer
     * @return void
     * @dataProvider dataProviderForArrayTrue
     */
    public function testArrayTrue(array $input, callable $reducer): void
    {
        // Given
        // When
        $result = $reducer($input);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForArrayTrue(): array
    {
        return [
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)->isSorted();
                },
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)->isReversed();
                },
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)->sameWith([]);
                },
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)->sameCountWith([]);
                },
            ],
            [
                [1, 3, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningAverage()
                        ->isSorted();
                },
            ],
            [
                [1, 3, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningAverage()
                        ->sameWith([1, 2, 3]);
                },
            ],
            [
                [1, 3, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningAverage()
                        ->sameCountWith([11, 22, 33]);
                },
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($item) {
                            return $item > 0;
                        })
                        ->runningMax()
                        ->isSorted();
                },
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($item) {
                            return $item < 0;
                        })
                        ->runningMin()
                        ->isReversed();
                },
            ],
            [
                [1, 2, 3, 4, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningProduct()
                        ->runningTotal()
                        ->runningMin()
                        ->isSorted();
                },
            ],
            [
                [1, 2, 3, 4, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningProduct()
                        ->runningTotal()
                        ->runningMin()
                        ->isReversed();
                },
            ],
        ];
    }

    /**
     * @param array $input
     * @param callable $reducer
     * @return void
     * @dataProvider dataProviderForArrayFalse
     */
    public function testArrayFalse(array $input, callable $reducer): void
    {
        // Given
        // When
        $result = $reducer($input);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForArrayFalse(): array
    {
        return [
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)->sameWith([1]);
                },
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)->sameCountWith([1]);
                },
            ],
            [
                [1, 3, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningAverage()
                        ->isReversed();
                },
            ],
            [
                [1, 3, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningAverage()
                        ->sameWith([11, 22, 33]);
                },
            ],
            [
                [1, 3, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningAverage()
                        ->sameCountWith([11, 22, 33, 44, 55]);
                },
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($item) {
                            return $item > 0;
                        })
                        ->runningMax()
                        ->isReversed();
                },
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($item) {
                            return $item < 0;
                        })
                        ->runningMin()
                        ->isSorted();
                },
            ],
        ];
    }
}
