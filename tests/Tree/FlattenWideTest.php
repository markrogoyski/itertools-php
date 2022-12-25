<?php

declare(strict_types=1);

namespace IterTools\Tests\Tree;

use IterTools\Tree;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class FlattenWideTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         flattenWide array
     * @dataProvider dataProviderForArrayWithChildrenContainerKey
     * @param        array<mixed> $data
     * @param        string $childrenContainerKey
     * @param        int $initialLevel
     * @param        array<int> $expectedLevels
     * @param        array<int> $expectedIds
     */
    public function testArrayWithChildrenContainerKey(
        array $data,
        string $childrenContainerKey,
        int $initialLevel,
        array $expectedLevels,
        array $expectedIds
    )
    {
        // Given
        $actualLevels = [];
        $actualIds = [];

        // When
        foreach(Tree::flattenWide($data, $childrenContainerKey, $initialLevel) as $level => $item) {
            $actualLevels[] = $level;
            $actualIds[] = $this->getId($item);
        }

        // Then
        $this->assertSame($expectedLevels, $actualLevels);
        $this->assertSame($expectedIds, $actualIds);
    }

    public function dataProviderForArrayWithChildrenContainerKey(): array
    {
        return [
            [
                [],
                'children',
                0,
                [],
                [],
            ],
            [
                [1, 2, 3],
                'children',
                0,
                [0, 0, 0],
                [1, 2, 3],
            ],
            [
                [1, 2, 3],
                'children',
                1,
                [1, 1, 1],
                [1, 2, 3],
            ],
            [
                [
                    ['id' => 1, 'children' => [11, 12, 13]],
                    ['id' => 2, 'children' => [21, 22, 23]],
                    ['id' => 3, 'children' => [31, 32, 33]],
                ],
                'children',
                2,
                [2, 2, 2, 3, 3, 3, 3, 3, 3, 3, 3, 3],
                [1, 2, 3, 11, 12, 13, 21, 22, 23, 31, 32, 33],
            ],
            [
                [
                    [
                        'id' => 1,
                        'children' => [
                            11,
                            ['id' => 12],
                            (object)['id' => 13],
                        ]
                    ],
                    2,
                    [
                        'id' => 3,
                        'children' => [
                            [
                                'id' => 31,
                                'children' => [
                                    311,
                                    ['id' => 312],
                                    new class (313) {
                                        public int $id;

                                        public function __construct(int $id)
                                        {
                                            $this->id = $id;
                                        }
                                    }
                                ],
                            ],
                            (object)['id' => 32],
                            new class (33) {
                                public int $id;

                                public function __construct(int $id)
                                {
                                    $this->id = $id;
                                }

                                public function getChildren(): array
                                {
                                    return [
                                        ['id' => 331],
                                        332,
                                        (object)['id' => 333]
                                    ];
                                }
                            },
                            new class (34) {
                                public int $id;

                                public function __construct(int $id)
                                {
                                    $this->id = $id;
                                }

                                protected function getChildren(): array
                                {
                                    return [
                                        ['id' => 341],
                                        342,
                                        (object)['id' => 343]
                                    ];
                                }
                            },
                            new class (35) {
                                public int $id;

                                public function __construct(int $id)
                                {
                                    $this->id = $id;
                                }

                                private function getChildren(): array
                                {
                                    return [
                                        ['id' => 351],
                                        352,
                                        (object)['id' => 353]
                                    ];
                                }
                            }
                        ],
                    ],
                ],
                'children',
                0,
                [0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2],
                [1, 2, 3, 11, 12, 13, 31, 32, 33, 34, 35, 311, 312, 313, 331, 332, 333],
            ],
            [
                [
                    [
                        'id' => 1,
                        'items' => [
                            11,
                            ['id' => 12],
                            (object)['id' => 13],
                        ]
                    ],
                    2,
                    [
                        'id' => 3,
                        'items' => [
                            [
                                'id' => 31,
                                'items' => [
                                    311,
                                    ['id' => 312],
                                    new class (313) {
                                        public int $id;

                                        public function __construct(int $id)
                                        {
                                            $this->id = $id;
                                        }
                                    }
                                ],
                            ],
                            (object)['id' => 32],
                            new class (33) {
                                public int $id;

                                public function __construct(int $id)
                                {
                                    $this->id = $id;
                                }

                                public function getItems(): array
                                {
                                    return [
                                        ['id' => 331],
                                        332,
                                        (object)['id' => 333]
                                    ];
                                }
                            },
                            new class (34) {
                                public int $id;

                                public function __construct(int $id)
                                {
                                    $this->id = $id;
                                }

                                protected function getChildren(): array
                                {
                                    return [
                                        ['id' => 341],
                                        342,
                                        (object)['id' => 343]
                                    ];
                                }
                            },
                            new class (35) {
                                public int $id;

                                public function __construct(int $id)
                                {
                                    $this->id = $id;
                                }

                                private function getChildren(): array
                                {
                                    return [
                                        ['id' => 351],
                                        352,
                                        (object)['id' => 353]
                                    ];
                                }
                            }
                        ],
                    ],
                ],
                'items',
                0,
                [0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2],
                [1, 2, 3, 11, 12, 13, 31, 32, 33, 34, 35, 311, 312, 313, 331, 332, 333],
            ],
        ];
    }

    /**
     * @test         flattenWide array
     * @dataProvider dataProviderForArrayWithoutChildrenContainerKey
     * @param        array<mixed> $data
     * @param        string $childrenContainerKey
     * @param        int $initialLevel
     * @param        array<int> $expectedLevels
     * @param        array<int> $expectedIds
     */
    public function testArrayWithoutChildrenContainerKey(
        array $data,
        int $initialLevel,
        array $expectedLevels,
        array $expectedIds
    )
    {
        // Given
        $actualLevels = [];
        $actualIds = [];

        // When
        foreach(Tree::flattenWide($data, null, $initialLevel) as $level => $item) {
            $actualLevels[] = $level;
            $actualIds[] = $this->getId($item);
        }

        // Then
        $this->assertSame($expectedLevels, $actualLevels);
        $this->assertSame($expectedIds, $actualIds);
    }

    public function dataProviderForArrayWithoutChildrenContainerKey(): array
    {
        return [
            [
                [],
                0,
                [],
                [],
            ],
            [
                [1, 2, 3],
                0,
                [0, 0, 0],
                [1, 2, 3],
            ],
            [
                [1, 2, 3],
                1,
                [1, 1, 1],
                [1, 2, 3],
            ],
            [
                [
                    ['id' => 1, [11, 12, 13]],
                    ['id' => 2, [21, 22, 23]],
                    ['id' => 3, [31, 32, 33]],
                ],
                2,
                [2, 2, 2, 3, 3, 3, 3, 3, 3, 3, 3, 3],
                [1, 2, 3, 11, 12, 13, 21, 22, 23, 31, 32, 33],
            ],
            [
                [
                    [
                        'id' => 1,
                        'children' => [
                            11,
                            ['id' => 12],
                            (object)['id' => 13],
                        ]
                    ],
                    2,
                    [
                        'id' => 3,
                        'children' => [
                            [
                                'id' => 31,
                                'children' => [
                                    311,
                                    ['id' => 312],
                                    new class (313) {
                                        public int $id;

                                        public function __construct(int $id)
                                        {
                                            $this->id = $id;
                                        }
                                    }
                                ],
                            ],
                            (object)['id' => 32],
                            new class (33) {
                                public int $id;

                                public function __construct(int $id)
                                {
                                    $this->id = $id;
                                }

                                public function getChildren(): array
                                {
                                    return [
                                        ['id' => 331],
                                        332,
                                        (object)['id' => 333]
                                    ];
                                }
                            },
                            new class (34) {
                                public int $id;

                                public function __construct(int $id)
                                {
                                    $this->id = $id;
                                }

                                protected function getChildren(): array
                                {
                                    return [
                                        ['id' => 341],
                                        342,
                                        (object)['id' => 343]
                                    ];
                                }
                            },
                            new class (35) {
                                public int $id;

                                public function __construct(int $id)
                                {
                                    $this->id = $id;
                                }

                                private function getChildren(): array
                                {
                                    return [
                                        ['id' => 351],
                                        352,
                                        (object)['id' => 353]
                                    ];
                                }
                            }
                        ],
                    ],
                ],
                'children',
                0,
                [0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2],
                [1, 2, 3, 11, 12, 13, 31, 32, 33, 34, 35, 311, 312, 313, 331, 332, 333],
            ],
            [
                [
                    [
                        'id' => 1,
                        'items' => [
                            11,
                            ['id' => 12],
                            (object)['id' => 13],
                        ]
                    ],
                    2,
                    [
                        'id' => 3,
                        'items' => [
                            [
                                'id' => 31,
                                'items' => [
                                    311,
                                    ['id' => 312],
                                    new class (313) {
                                        public int $id;

                                        public function __construct(int $id)
                                        {
                                            $this->id = $id;
                                        }
                                    }
                                ],
                            ],
                            (object)['id' => 32],
                            new class (33) {
                                public int $id;

                                public function __construct(int $id)
                                {
                                    $this->id = $id;
                                }

                                public function getItems(): array
                                {
                                    return [
                                        ['id' => 331],
                                        332,
                                        (object)['id' => 333]
                                    ];
                                }
                            },
                            new class (34) {
                                public int $id;

                                public function __construct(int $id)
                                {
                                    $this->id = $id;
                                }

                                protected function getChildren(): array
                                {
                                    return [
                                        ['id' => 341],
                                        342,
                                        (object)['id' => 343]
                                    ];
                                }
                            },
                            new class (35) {
                                public int $id;

                                public function __construct(int $id)
                                {
                                    $this->id = $id;
                                }

                                private function getChildren(): array
                                {
                                    return [
                                        ['id' => 351],
                                        352,
                                        (object)['id' => 353]
                                    ];
                                }
                            }
                        ],
                    ],
                ],
                'items',
                0,
                [0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2],
                [1, 2, 3, 11, 12, 13, 31, 32, 33, 34, 35, 311, 312, 313, 331, 332, 333],
            ],
        ];
    }


//    /**
//     * @test         toCount generators
//     * @dataProvider dataProviderForGenerators
//     * @param        \Generator $data
//     * @param        mixed $expected
//     */
//    public function testGenerators(\Generator $data, $expected)
//    {
//        // Given: $data
//
//        // When
//        $result = Reduce::toCount($data);
//
//        // Then
//        $this->assertSame($expected, $result);
//    }
//
//    public function dataProviderForGenerators(): array
//    {
//        $gen = static function (array $data) {
//            return GeneratorFixture::getGenerator($data);
//        };
//
//        return [
//            //  data                          expected
//            [   $gen([]),                     0      ],
//            [   $gen([0]),                    1      ],
//            [   $gen([null]),                 1      ],
//            [   $gen(['']),                   1      ],
//            [   $gen(['', null]),             2      ],
//            [   $gen([1, 2, 3]),              3      ],
//            [   $gen([[1], '2', 3]),          3      ],
//        ];
//    }
//
//    /**
//     * @test         toCount iterators
//     * @dataProvider dataProviderForIterators
//     * @param        \Generator $data
//     * @param        mixed $expected
//     */
//    public function testIterators(\Iterator $data, $expected)
//    {
//        // Given: $data
//
//        // When
//        $result = Reduce::toCount($data);
//
//        // Then
//        $this->assertSame($expected, $result);
//    }
//
//    public function dataProviderForIterators(): array
//    {
//        $iter = static function (array $data) {
//            return new ArrayIteratorFixture($data);
//        };
//
//        return [
//            //  data                           expected
//            [   $iter([]),                     0      ],
//            [   $iter([0]),                    1      ],
//            [   $iter([null]),                 1      ],
//            [   $iter(['']),                   1      ],
//            [   $iter(['', null]),             2      ],
//            [   $iter([1, 2, 3]),              3      ],
//            [   $iter([[1], '2', 3]),          3      ],
//        ];
//    }
//
//    /**
//     * @test         toCount traversables
//     * @dataProvider dataProviderForTraversables
//     * @param        \Traversable $data
//     * @param        mixed $expected
//     */
//    public function testTraversables(\Traversable $data, $expected)
//    {
//        // Given: $data
//
//        // When
//        $result = Reduce::toCount($data);
//
//        // Then
//        $this->assertSame($expected, $result);
//    }
//
//    public function dataProviderForTraversables(): array
//    {
//        $trav = static function (array $data) {
//            return new IteratorAggregateFixture($data);
//        };
//
//        return [
//            //  data                           expected
//            [   $trav([]),                     0      ],
//            [   $trav([0]),                    1      ],
//            [   $trav([null]),                 1      ],
//            [   $trav(['']),                   1      ],
//            [   $trav(['', null]),             2      ],
//            [   $trav([1, 2, 3]),              3      ],
//            [   $trav([[1], '2', 3]),          3      ],
//        ];
//    }

    /**
     * @param array<string, mixed>|object|scalar $container
     * @return mixed|null
     */
    protected function getId($container)
    {
        $key = 'id';
        switch(true) {
            case is_scalar($container):
                return $container;
            case is_array($container) || $container instanceof \ArrayAccess:
                if (array_key_exists($key, $container)) {
                    return $container[$key];
                }
                break;
            case is_object($container):
                if (property_exists($container, $key)) {
                    return $container->{$key};
                }
                break;
        }

        return null;
    }
}
