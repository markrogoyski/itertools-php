<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToSumTest extends \PHPUnit\Framework\TestCase
{
    protected const ROUND_PRECISION = 4;

    /**
     * @test         toSum array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        int|float $expected
     */
    public function testArray(array $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::toSum($data);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::ROUND_PRECISION);
    }

    public function dataProviderForArray(): array
    {
        return [
            //  data                    expected
            [   [],                     0      ],
            [   [0],                    0      ],
            [   [null],                 0      ],
            [   [false],                0      ],
            [   [null, null],           0      ],
            [   [null, false],          0      ],
            [   [true, false],          1      ],
            [   [false, true],          1      ],
            [   [0, null, false],       0      ],
            [   [1, null, false],       1      ],
            [   [1, null, true],        2      ],
            [   [1, 2, 3],              6      ],
            [   [1.1, 2.2, 3.3],        6.6    ],
            [   [1.1, 2, 3.3],          6.4    ],
        ];
    }

    /**
     * @test         toSum generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        mixed $expected
     */
    public function testGenerators(\Generator $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::toSum($data);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::ROUND_PRECISION);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            //  data                          expected
            [   $gen([]),                     0      ],
            [   $gen([0]),                    0      ],
            [   $gen([null]),                 0      ],
            [   $gen([false]),                0      ],
            [   $gen([null, null]),           0      ],
            [   $gen([null, false]),          0      ],
            [   $gen([true, false]),          1      ],
            [   $gen([false, true]),          1      ],
            [   $gen([0, null, false]),       0      ],
            [   $gen([1, null, false]),       1      ],
            [   $gen([1, null, true]),        2      ],
            [   $gen([1, 2, 3]),              6      ],
            [   $gen([1.1, 2.2, 3.3]),        6.6    ],
            [   $gen([1.1, 2, 3.3]),          6.4    ],
        ];
    }

    /**
     * @test         toSum iterators
     * @dataProvider dataProviderForIterators
     * @param        \Generator $data
     * @param        mixed $expected
     */
    public function testIterators(\Iterator $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::toSum($data);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::ROUND_PRECISION);
    }

    public function dataProviderForIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            //  data                           expected
            [   $iter([]),                     0      ],
            [   $iter([0]),                    0      ],
            [   $iter([null]),                 0      ],
            [   $iter([false]),                0      ],
            [   $iter([null, null]),           0      ],
            [   $iter([null, false]),          0      ],
            [   $iter([true, false]),          1      ],
            [   $iter([false, true]),          1      ],
            [   $iter([0, null, false]),       0      ],
            [   $iter([1, null, false]),       1      ],
            [   $iter([1, null, true]),        2      ],
            [   $iter([1, 2, 3]),              6      ],
            [   $iter([1.1, 2.2, 3.3]),        6.6    ],
            [   $iter([1.1, 2, 3.3]),          6.4    ],
        ];
    }

    /**
     * @test         toSum traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        mixed $expected
     */
    public function testTraversables(\Traversable $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::toSum($data);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::ROUND_PRECISION);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            //  data                           expected
            [   $trav([]),                     0      ],
            [   $trav([0]),                    0      ],
            [   $trav([null]),                 0      ],
            [   $trav([false]),                0      ],
            [   $trav([null, null]),           0      ],
            [   $trav([null, false]),          0      ],
            [   $trav([true, false]),          1      ],
            [   $trav([false, true]),          1      ],
            [   $trav([0, null, false]),       0      ],
            [   $trav([1, null, false]),       1      ],
            [   $trav([1, null, true]),        2      ],
            [   $trav([1, 2, 3]),              6      ],
            [   $trav([1.1, 2.2, 3.3]),        6.6    ],
            [   $trav([1.1, 2, 3.3]),          6.4    ],
        ];
    }
}
