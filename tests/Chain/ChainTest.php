<?php

declare(strict_types=1);

namespace IterTools\Tests\Chain;

use IterTools\Chain;

class ChainTest extends \PHPUnit\Framework\TestCase
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
        ];
    }
}
