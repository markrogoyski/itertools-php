<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class PairwiseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        array $expected
     */
    public function testArray(array $data, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::pairwise($data) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            //  data                expected
            [   [],                 []                                  ],
            [   [1],                []                                  ],
            [   [1, 2],             [[1, 2]]                            ],
            [   [1, 2, 3],          [[1, 2], [2, 3]]                    ],
            [   [1, 2, 3, 4],       [[1, 2], [2, 3], [3, 4]]            ],
            [   [1, 2, 3, 4, 5],    [[1, 2], [2, 3], [3, 4], [4, 5]]    ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        array $expected
     */
    public function testGenerators(\Generator $data, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::pairwise($data) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            //  data                      expected
            [   $gen([]),                 []                                  ],
            [   $gen([1]),                []                                  ],
            [   $gen([1, 2]),             [[1, 2]]                            ],
            [   $gen([1, 2, 3]),          [[1, 2], [2, 3]]                    ],
            [   $gen([1, 2, 3, 4]),       [[1, 2], [2, 3], [3, 4]]            ],
            [   $gen([1, 2, 3, 4, 5]),    [[1, 2], [2, 3], [3, 4], [4, 5]]    ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param        \Iterator $data
     * @param        array $expected
     */
    public function testIterators(\Iterator $data, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::pairwise($data) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            //  data                       expected
            [   $iter([]),                 []                                  ],
            [   $iter([1]),                []                                  ],
            [   $iter([1, 2]),             [[1, 2]]                            ],
            [   $iter([1, 2, 3]),          [[1, 2], [2, 3]]                    ],
            [   $iter([1, 2, 3, 4]),       [[1, 2], [2, 3], [3, 4]]            ],
            [   $iter([1, 2, 3, 4, 5]),    [[1, 2], [2, 3], [3, 4], [4, 5]]    ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        array $expected
     */
    public function testTraversables(\Traversable $data, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::pairwise($data) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            //  data                       expected
            [   $trav([]),                 []                                  ],
            [   $trav([1]),                []                                  ],
            [   $trav([1, 2]),             [[1, 2]]                            ],
            [   $trav([1, 2, 3]),          [[1, 2], [2, 3]]                    ],
            [   $trav([1, 2, 3, 4]),       [[1, 2], [2, 3], [3, 4]]            ],
            [   $trav([1, 2, 3, 4, 5]),    [[1, 2], [2, 3], [3, 4], [4, 5]]    ],
        ];
    }
}
