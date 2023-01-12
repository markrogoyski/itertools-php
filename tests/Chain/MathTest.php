<?php

declare(strict_types=1);

namespace IterTools\Tests\Chain;

use IterTools\Chain;

class MathTest extends \PHPUnit\Framework\TestCase
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
                    return Chain::create($iterable)->runningAverage();
                },
                [],
            ],
            [
                [],
                static function (array $iterable) {
                    return Chain::create($iterable)->runningAverage(1);
                },
                [1],
            ],
            [
                [1, 3, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)->runningAverage();
                },
                [1, 2, 3],
            ],
            [
                [3, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)->runningAverage(1);
                },
                [1, 2, 3],
            ],
            [
                [1, 3, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningAverage()
                        ->runningTotal();
                },
                [1, 3, 6],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningMax();
                },
                [1, 1, 2, 2, 3, 3],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningMax()
                        ->runningTotal();
                },
                [1, 2, 4, 6, 9, 12],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)->runningMin();
                },
                [1, -1, -1, -2, -2, -3],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)->runningDifference();
                },
                [-1, -3, -6, -10, -15],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningDifference()
                        ->runningTotal();
                },
                [-1, -4, -10, -20, -35],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningDifference()
                        ->runningTotal()
                        ->runningMin();
                },
                [-1, -4, -10, -20, -35],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningDifference()
                        ->runningTotal()
                        ->runningMax();
                },
                [-1, -1, -1, -1, -1],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningMin()
                        ->runningTotal();
                },
                [1, 0, -1, -3, -5, -8],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)->runningProduct();
                },
                [1, 2, 6, 24, 120],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningProduct()
                        ->runningTotal();
                },
                [1, 3, 9, 33, 153],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningProduct()
                        ->runningTotal()
                        ->runningMax();
                },
                [1, 3, 9, 33, 153],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->runningProduct()
                        ->runningTotal()
                        ->runningMin();
                },
                [1, 1, 1, 1, 1],
            ],
        ];
    }
}
