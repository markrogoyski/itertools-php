<?php

declare(strict_types=1);

namespace IterTools\Tests\Chain;

use IterTools\Chain;

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
                static function (array $iterable) {
                    return Chain::create($iterable)->infiniteCycle();
                },
                [],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($item) {
                            return $item < 0;
                        })
                        ->infiniteCycle();
                },
                [],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)->infiniteCycle();
                },
                [1, 2, 3, 4, 5, 1, 2, 3, 4, 5, 1, 2, 3, 4, 5],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->filterTrue(static function ($item) {
                            return $item % 2 !== 0;
                        })
                        ->infiniteCycle();
                },
                [1, 3, 5, 1, 3, 5, 1, 3, 5],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (array $iterable) {
                    return Chain::create($iterable)
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
                static function (array $iterable) {
                    return Chain::create($iterable)
                        ->infiniteCycle()
                        ->runningTotal();
                },
                [1, 3, 6, 10, 15, 16, 18, 21, 25, 30, 31, 33, 36, 40, 45],
            ],
        ];
    }
}
