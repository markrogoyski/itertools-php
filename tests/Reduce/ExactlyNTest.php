<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ExactlyNTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         ExactlyN array true
     * @dataProvider dataProviderForArrayTrue
     * @param        array<mixed> $data
     * @param        array<array<mixed>> $anothers
     */
    public function testArrayTrue(array $data, array $anothers)
    {
        // Given: $data

        // When
        $result = Reduce::ExactlyN($data, ...$anothers);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForArrayTrue(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            //  data                    anothers
            [   [],                     [[]],                                       ],
            [   [],                     [$gen([])],                                 ],
            [   [],                     [$iter([])],                                ],
            [   [],                     [$trav([])],                                ],

            [   [],                     [[], []],                                   ],
            [   [],                     [$gen([]), []],                             ],
            [   [],                     [$iter([]), []],                            ],
            [   [],                     [$trav([]), []],                            ],
            [   [],                     [[], $gen([])],                             ],
            [   [],                     [[], $iter([])],                            ],
            [   [],                     [[], $trav([])],                            ],
            [   [],                     [$gen([]), $iter([])],                      ],
            [   [],                     [$iter([]), $trav([])],                     ],

            [   [],                     [[], [], [], []],                           ],
            [   [],                     [$gen([]), $iter([]), $trav([]), []],       ],
            [   [],                     [[], $gen([]), $iter([]), $trav([])],       ],

            [   [1],                    [[1]],                                      ],
            [   [1],                    [$gen([1])],                                ],
            [   [1],                    [$iter([1])],                               ],
            [   [1],                    [$trav([1])],                               ],

            [   [1],                    [[1], [1]],                                 ],
            [   [1],                    [$gen([1]), [1]],                           ],
            [   [1],                    [$iter([1]), [1]],                          ],
            [   [1],                    [$trav([1]), [1]],                          ],
            [   [1],                    [[1], $gen([1])],                           ],
            [   [1],                    [[1], $iter([1])],                          ],
            [   [1],                    [[1], $trav([1])],                          ],
            [   [1],                    [$gen([1]), $iter([1])],                    ],
            [   [1],                    [$iter([1]), $trav([1])],                   ],

            [   [1, 2],                 [[1, 2], [1, 2], [1, 2]],                   ],
            [   [1, 2],                 [[1, 2], $gen([1, 2]), [1, 2]],             ],
            [   [1, 2],                 [[1, 2], $iter([1, 2]), [1, 2]],            ],
            [   [1, 2],                 [[1, 2], $trav([1, 2]), [1, 2]],            ],
            [   [1, 2],                 [[1, 2], $gen([1, 2]), $iter([1, 2])],      ],
            [   [1, 2],                 [[1, 2], $iter([1, 2]), $trav([1, 2])],     ],

            [   [1, 2, 3],              [[1, 2, 3], [1, 2, 3], [1, 2, 3]],          ],
            [   [1, 2, 3],              [[1, 2, 3], $gen([1, 2, 3]), [1, 2, 3]],    ],
            [   [1, 2, 3],              [[1, 2, 3], $iter([1, 2, 3]), [1, 2, 3]],   ],
            [   [1, 2, 3],              [[1, 2, 3], $trav([1, 2, 3]), [1, 2, 3]],   ],

            [   ['a', 2],               [['a', 2], ['a', 2], ['a', 2]],             ],
            [   ['a', 2],               [$gen(['a', 2]), ['a', 2], ['a', 2]],       ],
            [   ['a', 2],               [$iter(['a', 2]), ['a', 2], ['a', 2]],      ],
            [   ['a', 2],               [$trav(['a', 2]), ['a', 2], ['a', 2]],      ],

            [   [1, null],              [[1, null], [1, null], [1, null]],          ],
            [   [1, null],              [[1, null], [1, null], $gen([1, null])],   ],
            [   [1, null],              [[1, null], [1, null], $iter([1, null])],   ],
            [   [1, null],              [[1, null], [1, null], $trav([1, null])],   ],
        ];
    }

    /**
     * @test         ExactlyN array true
     * @dataProvider dataProviderForArrayFalse
     * @param        array<mixed> $data
     * @param        array<array<mixed>> $anothers
     */
    public function testArrayFalse(array $data, array $anothers)
    {
        // Given: $data

        // When
        $result = Reduce::ExactlyN($data, ...$anothers);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForArrayFalse(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            //  data                    anothers
            [   [],                     [[1]],                                  ],
            [   [],                     [$gen([1])],                            ],
            [   [],                     [$iter([1])],                           ],
            [   [],                     [$trav([1])],                           ],

            [   [1],                    [[]],                                   ],
            [   [1],                    [$gen([])],                             ],
            [   [1],                    [$iter([])],                            ],
            [   [1],                    [$trav([])],                            ],

            [   ['1'],                  [[1], [1], [1]],                        ],
            [   ['1'],                  [$gen([1]), [1], [1]],                  ],
            [   ['1'],                  [[1], $iter([1]), [1]],                 ],
            [   ['1'],                  [[1], [1], $trav([1])],                 ],

            [   [1],                    [['1']],                                ],
            [   [1],                    [$gen(['1'])],                          ],
            [   [1],                    [$iter(['1'])],                         ],
            [   [1],                    [$trav(['1'])],                         ],

            [   [1],                    [[2]],                                  ],
            [   [1],                    [$gen([2])],                            ],
            [   [1],                    [$iter([2])],                           ],
            [   [1],                    [$trav([2])],                           ],

            [   [1],                    [['1'], [1]],                           ],
            [   [1],                    [$gen(['1']), [1]],                     ],
            [   [1],                    [$iter(['1']), [1]],                    ],
            [   [1],                    [$trav(['1']), [1]],                    ],
            [   [1],                    [['1'], $gen([1])],                     ],
            [   [1],                    [['1'], $iter([1])],                    ],
            [   [1],                    [['1'], $trav([1])],                    ],
            [   [1],                    [$iter(['1']), $gen([1])],              ],
            [   [1],                    [$trav(['1']), $iter([1])],             ],
            [   [1],                    [$gen(['1']), $trav([1])],              ],

            [   [1],                    [[1], ['1']],                           ],
            [   [1],                    [[1], [2], [1]],                        ],
            [   [1],                    [[1], ['1'], [1]],                      ],
            [   [1, 2, 3],              [[1, 2, 3], [1, 2, 3], [1, 2, '3']],    ],
            [   [1, 2, 3],              [[1, 2, 3], [1, 2, 3], [1, 2, 1]],      ],
            [   [1],                    [[1, null], [1, null], [1]],            ],
            [   [1],                    [[1, null], [1, null], [1, null]],      ],
            [   [1, null],              [[1], [1], [1]],                        ],
            [   [1, null],              [[1], [1, null], [1]],                  ],
        ];
    }

    /**
     * @test         ExactlyN generators true
     * @dataProvider dataProviderForGeneratorsTrue
     * @param        \Generator $data
     * @param        array<\Generator> $anothers
     */
    public function testGeneratorsTrue(\Generator $data, array $anothers)
    {
        // Given: $data

        // When
        $result = Reduce::ExactlyN($data, ...$anothers);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForGeneratorsTrue(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            //  data                    anothers
            [   $gen([]),                     [[]],                                       ],
            [   $gen([]),                     [$gen([])],                                 ],
            [   $gen([]),                     [$iter([])],                                ],
            [   $gen([]),                     [$trav([])],                                ],

            [   $gen([]),                     [[], []],                                   ],
            [   $gen([]),                     [$gen([]), []],                             ],
            [   $gen([]),                     [$iter([]), []],                            ],
            [   $gen([]),                     [$trav([]), []],                            ],
            [   $gen([]),                     [[], $gen([])],                             ],
            [   $gen([]),                     [[], $iter([])],                            ],
            [   $gen([]),                     [[], $trav([])],                            ],
            [   $gen([]),                     [$gen([]), $iter([])],                      ],
            [   $gen([]),                     [$iter([]), $trav([])],                     ],

            [   $gen([]),                     [[], [], [], []],                           ],
            [   $gen([]),                     [$gen([]), $iter([]), $trav([]), []],       ],
            [   $gen([]),                     [[], $gen([]), $iter([]), $trav([])],       ],

            [   $gen([1]),                    [[1]],                                      ],
            [   $gen([1]),                    [$gen([1])],                                ],
            [   $gen([1]),                    [$iter([1])],                               ],
            [   $gen([1]),                    [$trav([1])],                               ],

            [   $gen([1]),                    [[1], [1]],                                 ],
            [   $gen([1]),                    [$gen([1]), [1]],                           ],
            [   $gen([1]),                    [$iter([1]), [1]],                          ],
            [   $gen([1]),                    [$trav([1]), [1]],                          ],
            [   $gen([1]),                    [[1], $gen([1])],                           ],
            [   $gen([1]),                    [[1], $iter([1])],                          ],
            [   $gen([1]),                    [[1], $trav([1])],                          ],
            [   $gen([1]),                    [$gen([1]), $iter([1])],                    ],
            [   $gen([1]),                    [$iter([1]), $trav([1])],                   ],

            [   $gen([1, 2]),                 [[1, 2], [1, 2], [1, 2]],                   ],
            [   $gen([1, 2]),                 [[1, 2], $gen([1, 2]), [1, 2]],             ],
            [   $gen([1, 2]),                 [[1, 2], $iter([1, 2]), [1, 2]],            ],
            [   $gen([1, 2]),                 [[1, 2], $trav([1, 2]), [1, 2]],            ],
            [   $gen([1, 2]),                 [[1, 2], $gen([1, 2]), $iter([1, 2])],      ],
            [   $gen([1, 2]),                 [[1, 2], $iter([1, 2]), $trav([1, 2])],     ],

            [   $gen([1, 2, 3]),              [[1, 2, 3], [1, 2, 3], [1, 2, 3]],          ],
            [   $gen([1, 2, 3]),              [[1, 2, 3], $gen([1, 2, 3]), [1, 2, 3]],    ],
            [   $gen([1, 2, 3]),              [[1, 2, 3], $iter([1, 2, 3]), [1, 2, 3]],   ],
            [   $gen([1, 2, 3]),              [[1, 2, 3], $trav([1, 2, 3]), [1, 2, 3]],   ],

            [   $gen(['a', 2]),               [['a', 2], ['a', 2], ['a', 2]],             ],
            [   $gen(['a', 2]),               [$gen(['a', 2]), ['a', 2], ['a', 2]],       ],
            [   $gen(['a', 2]),               [$iter(['a', 2]), ['a', 2], ['a', 2]],      ],
            [   $gen(['a', 2]),               [$trav(['a', 2]), ['a', 2], ['a', 2]],      ],

            [   $gen([1, null]),              [[1, null], [1, null], [1, null]],          ],
            [   $gen([1, null]),              [[1, null], [1, null], $gen([1, null])],   ],
            [   $gen([1, null]),              [[1, null], [1, null], $iter([1, null])],   ],
            [   $gen([1, null]),              [[1, null], [1, null], $trav([1, null])],   ],
        ];
    }

    /**
     * @test         ExactlyN generators true
     * @dataProvider dataProviderForGeneratorsFalse
     * @param        \Generator $data
     * @param        array<\Generator> $anothers
     */
    public function testGeneratorsFalse(\Generator $data, array $anothers)
    {
        // Given: $data

        // When
        $result = Reduce::ExactlyN($data, ...$anothers);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForGeneratorsFalse(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            //  data                    anothers
            [   $gen([]),                     [[1]],                                  ],
            [   $gen([]),                     [$gen([1])],                            ],
            [   $gen([]),                     [$iter([1])],                           ],
            [   $gen([]),                     [$trav([1])],                           ],

            [   $gen([1]),                    [[]],                                   ],
            [   $gen([1]),                    [$gen([])],                             ],
            [   $gen([1]),                    [$iter([])],                            ],
            [   $gen([1]),                    [$trav([])],                            ],

            [   $gen(['1']),                  [[1], [1], [1]],                        ],
            [   $gen(['1']),                  [$gen([1]), [1], [1]],                  ],
            [   $gen(['1']),                  [[1], $iter([1]), [1]],                 ],
            [   $gen(['1']),                  [[1], [1], $trav([1])],                 ],

            [   $gen([1]),                    [['1']],                                ],
            [   $gen([1]),                    [$gen(['1'])],                          ],
            [   $gen([1]),                    [$iter(['1'])],                         ],
            [   $gen([1]),                    [$trav(['1'])],                         ],

            [   $gen([1]),                    [[2]],                                  ],
            [   $gen([1]),                    [$gen([2])],                            ],
            [   $gen([1]),                    [$iter([2])],                           ],
            [   $gen([1]),                    [$trav([2])],                           ],

            [   $gen([1]),                    [['1'], [1]],                           ],
            [   $gen([1]),                    [$gen(['1']), [1]],                     ],
            [   $gen([1]),                    [$iter(['1']), [1]],                    ],
            [   $gen([1]),                    [$trav(['1']), [1]],                    ],
            [   $gen([1]),                    [['1'], $gen([1])],                     ],
            [   $gen([1]),                    [['1'], $iter([1])],                    ],
            [   $gen([1]),                    [['1'], $trav([1])],                    ],
            [   $gen([1]),                    [$iter(['1']), $gen([1])],              ],
            [   $gen([1]),                    [$trav(['1']), $iter([1])],             ],
            [   $gen([1]),                    [$gen(['1']), $trav([1])],              ],

            [   $gen([1]),                    [[1], ['1']],                           ],
            [   $gen([1]),                    [[1], [2], [1]],                        ],
            [   $gen([1]),                    [[1], ['1'], [1]],                      ],
            [   $gen([1, 2, 3]),              [[1, 2, 3], [1, 2, 3], [1, 2, '3']],    ],
            [   $gen([1, 2, 3]),              [[1, 2, 3], [1, 2, 3], [1, 2, 1]],      ],
            [   $gen([1]),                    [[1, null], [1, null], [1]],            ],
            [   $gen([1]),                    [[1, null], [1, null], [1, null]],      ],
            [   $gen([1, null]),              [[1], [1], [1]],                        ],
            [   $gen([1, null]),              [[1], [1, null], [1]],                  ],
        ];
    }

    /**
     * @test         ExactlyN iterators true
     * @dataProvider dataProviderForIteratorsTrue
     * @param        \Iterator $data
     * @param        array<\Iterator> $anothers
     */
    public function testIteratorsTrue(\Iterator $data, array $anothers)
    {
        // Given: $data

        // When
        $result = Reduce::ExactlyN($data, ...$anothers);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForIteratorsTrue(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            //  data                    anothers
            [   $iter([]),                     [[]],                                       ],
            [   $iter([]),                     [$gen([])],                                 ],
            [   $iter([]),                     [$iter([])],                                ],
            [   $iter([]),                     [$trav([])],                                ],

            [   $iter([]),                     [[], []],                                   ],
            [   $iter([]),                     [$gen([]), []],                             ],
            [   $iter([]),                     [$iter([]), []],                            ],
            [   $iter([]),                     [$trav([]), []],                            ],
            [   $iter([]),                     [[], $gen([])],                             ],
            [   $iter([]),                     [[], $iter([])],                            ],
            [   $iter([]),                     [[], $trav([])],                            ],
            [   $iter([]),                     [$gen([]), $iter([])],                      ],
            [   $iter([]),                     [$iter([]), $trav([])],                     ],

            [   $iter([]),                     [[], [], [], []],                           ],
            [   $iter([]),                     [$gen([]), $iter([]), $trav([]), []],       ],
            [   $iter([]),                     [[], $gen([]), $iter([]), $trav([])],       ],

            [   $iter([1]),                    [[1]],                                      ],
            [   $iter([1]),                    [$gen([1])],                                ],
            [   $iter([1]),                    [$iter([1])],                               ],
            [   $iter([1]),                    [$trav([1])],                               ],

            [   $iter([1]),                    [[1], [1]],                                 ],
            [   $iter([1]),                    [$gen([1]), [1]],                           ],
            [   $iter([1]),                    [$iter([1]), [1]],                          ],
            [   $iter([1]),                    [$trav([1]), [1]],                          ],
            [   $iter([1]),                    [[1], $gen([1])],                           ],
            [   $iter([1]),                    [[1], $iter([1])],                          ],
            [   $iter([1]),                    [[1], $trav([1])],                          ],
            [   $iter([1]),                    [$gen([1]), $iter([1])],                    ],
            [   $iter([1]),                    [$iter([1]), $trav([1])],                   ],

            [   $iter([1, 2]),                 [[1, 2], [1, 2], [1, 2]],                   ],
            [   $iter([1, 2]),                 [[1, 2], $gen([1, 2]), [1, 2]],             ],
            [   $iter([1, 2]),                 [[1, 2], $iter([1, 2]), [1, 2]],            ],
            [   $iter([1, 2]),                 [[1, 2], $trav([1, 2]), [1, 2]],            ],
            [   $iter([1, 2]),                 [[1, 2], $gen([1, 2]), $iter([1, 2])],      ],
            [   $iter([1, 2]),                 [[1, 2], $iter([1, 2]), $trav([1, 2])],     ],

            [   $iter([1, 2, 3]),              [[1, 2, 3], [1, 2, 3], [1, 2, 3]],          ],
            [   $iter([1, 2, 3]),              [[1, 2, 3], $gen([1, 2, 3]), [1, 2, 3]],    ],
            [   $iter([1, 2, 3]),              [[1, 2, 3], $iter([1, 2, 3]), [1, 2, 3]],   ],
            [   $iter([1, 2, 3]),              [[1, 2, 3], $trav([1, 2, 3]), [1, 2, 3]],   ],

            [   $iter(['a', 2]),               [['a', 2], ['a', 2], ['a', 2]],             ],
            [   $iter(['a', 2]),               [$gen(['a', 2]), ['a', 2], ['a', 2]],       ],
            [   $iter(['a', 2]),               [$iter(['a', 2]), ['a', 2], ['a', 2]],      ],
            [   $iter(['a', 2]),               [$trav(['a', 2]), ['a', 2], ['a', 2]],      ],

            [   $iter([1, null]),              [[1, null], [1, null], [1, null]],          ],
            [   $iter([1, null]),              [[1, null], [1, null], $gen([1, null])],   ],
            [   $iter([1, null]),              [[1, null], [1, null], $iter([1, null])],   ],
            [   $iter([1, null]),              [[1, null], [1, null], $trav([1, null])],   ],
        ];
    }

    /**
     * @test         ExactlyN iterators true
     * @dataProvider dataProviderForGeneratorsFalse
     * @param        \Iterator $data
     * @param        array<\Iterator> $anothers
     */
    public function testIteratorsFalse(\Iterator $data, array $anothers)
    {
        // Given: $data

        // When
        $result = Reduce::ExactlyN($data, ...$anothers);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForIteratorsFalse(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            //  data                    anothers
            [   $gen([]),                     [[1]],                                  ],
            [   $gen([]),                     [$gen([1])],                            ],
            [   $gen([]),                     [$iter([1])],                           ],
            [   $gen([]),                     [$trav([1])],                           ],

            [   $gen([1]),                    [[]],                                   ],
            [   $gen([1]),                    [$gen([])],                             ],
            [   $gen([1]),                    [$iter([])],                            ],
            [   $gen([1]),                    [$trav([])],                            ],

            [   $gen(['1']),                  [[1], [1], [1]],                        ],
            [   $gen(['1']),                  [$gen([1]), [1], [1]],                  ],
            [   $gen(['1']),                  [[1], $iter([1]), [1]],                 ],
            [   $gen(['1']),                  [[1], [1], $trav([1])],                 ],

            [   $gen([1]),                    [['1']],                                ],
            [   $gen([1]),                    [$gen(['1'])],                          ],
            [   $gen([1]),                    [$iter(['1'])],                         ],
            [   $gen([1]),                    [$trav(['1'])],                         ],

            [   $gen([1]),                    [[2]],                                  ],
            [   $gen([1]),                    [$gen([2])],                            ],
            [   $gen([1]),                    [$iter([2])],                           ],
            [   $gen([1]),                    [$trav([2])],                           ],

            [   $gen([1]),                    [['1'], [1]],                           ],
            [   $gen([1]),                    [$gen(['1']), [1]],                     ],
            [   $gen([1]),                    [$iter(['1']), [1]],                    ],
            [   $gen([1]),                    [$trav(['1']), [1]],                    ],
            [   $gen([1]),                    [['1'], $gen([1])],                     ],
            [   $gen([1]),                    [['1'], $iter([1])],                    ],
            [   $gen([1]),                    [['1'], $trav([1])],                    ],
            [   $gen([1]),                    [$iter(['1']), $gen([1])],              ],
            [   $gen([1]),                    [$trav(['1']), $iter([1])],             ],
            [   $gen([1]),                    [$gen(['1']), $trav([1])],              ],

            [   $gen([1]),                    [[1], ['1']],                           ],
            [   $gen([1]),                    [[1], [2], [1]],                        ],
            [   $gen([1]),                    [[1], ['1'], [1]],                      ],
            [   $gen([1, 2, 3]),              [[1, 2, 3], [1, 2, 3], [1, 2, '3']],    ],
            [   $gen([1, 2, 3]),              [[1, 2, 3], [1, 2, 3], [1, 2, 1]],      ],
            [   $gen([1]),                    [[1, null], [1, null], [1]],            ],
            [   $gen([1]),                    [[1, null], [1, null], [1, null]],      ],
            [   $gen([1, null]),              [[1], [1], [1]],                        ],
            [   $gen([1, null]),              [[1], [1, null], [1]],                  ],
        ];
    }

    /**
     * @test         ExactlyN iterators true
     * @dataProvider dataProviderForTraversablesTrue
     * @param        \Traversable $data
     * @param        array<\Traversable> $anothers
     */
    public function testTraversablesTrue(\Traversable $data, array $anothers)
    {
        // Given: $data

        // When
        $result = Reduce::ExactlyN($data, ...$anothers);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForTraversablesTrue(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            //  data                    anothers
            [   $iter([]),                     [[]],                                       ],
            [   $iter([]),                     [$gen([])],                                 ],
            [   $iter([]),                     [$iter([])],                                ],
            [   $iter([]),                     [$trav([])],                                ],

            [   $iter([]),                     [[], []],                                   ],
            [   $iter([]),                     [$gen([]), []],                             ],
            [   $iter([]),                     [$iter([]), []],                            ],
            [   $iter([]),                     [$trav([]), []],                            ],
            [   $iter([]),                     [[], $gen([])],                             ],
            [   $iter([]),                     [[], $iter([])],                            ],
            [   $iter([]),                     [[], $trav([])],                            ],
            [   $iter([]),                     [$gen([]), $iter([])],                      ],
            [   $iter([]),                     [$iter([]), $trav([])],                     ],

            [   $iter([]),                     [[], [], [], []],                           ],
            [   $iter([]),                     [$gen([]), $iter([]), $trav([]), []],       ],
            [   $iter([]),                     [[], $gen([]), $iter([]), $trav([])],       ],

            [   $iter([1]),                    [[1]],                                      ],
            [   $iter([1]),                    [$gen([1])],                                ],
            [   $iter([1]),                    [$iter([1])],                               ],
            [   $iter([1]),                    [$trav([1])],                               ],

            [   $iter([1]),                    [[1], [1]],                                 ],
            [   $iter([1]),                    [$gen([1]), [1]],                           ],
            [   $iter([1]),                    [$iter([1]), [1]],                          ],
            [   $iter([1]),                    [$trav([1]), [1]],                          ],
            [   $iter([1]),                    [[1], $gen([1])],                           ],
            [   $iter([1]),                    [[1], $iter([1])],                          ],
            [   $iter([1]),                    [[1], $trav([1])],                          ],
            [   $iter([1]),                    [$gen([1]), $iter([1])],                    ],
            [   $iter([1]),                    [$iter([1]), $trav([1])],                   ],

            [   $iter([1, 2]),                 [[1, 2], [1, 2], [1, 2]],                   ],
            [   $iter([1, 2]),                 [[1, 2], $gen([1, 2]), [1, 2]],             ],
            [   $iter([1, 2]),                 [[1, 2], $iter([1, 2]), [1, 2]],            ],
            [   $iter([1, 2]),                 [[1, 2], $trav([1, 2]), [1, 2]],            ],
            [   $iter([1, 2]),                 [[1, 2], $gen([1, 2]), $iter([1, 2])],      ],
            [   $iter([1, 2]),                 [[1, 2], $iter([1, 2]), $trav([1, 2])],     ],

            [   $iter([1, 2, 3]),              [[1, 2, 3], [1, 2, 3], [1, 2, 3]],          ],
            [   $iter([1, 2, 3]),              [[1, 2, 3], $gen([1, 2, 3]), [1, 2, 3]],    ],
            [   $iter([1, 2, 3]),              [[1, 2, 3], $iter([1, 2, 3]), [1, 2, 3]],   ],
            [   $iter([1, 2, 3]),              [[1, 2, 3], $trav([1, 2, 3]), [1, 2, 3]],   ],

            [   $iter(['a', 2]),               [['a', 2], ['a', 2], ['a', 2]],             ],
            [   $iter(['a', 2]),               [$gen(['a', 2]), ['a', 2], ['a', 2]],       ],
            [   $iter(['a', 2]),               [$iter(['a', 2]), ['a', 2], ['a', 2]],      ],
            [   $iter(['a', 2]),               [$trav(['a', 2]), ['a', 2], ['a', 2]],      ],

            [   $iter([1, null]),              [[1, null], [1, null], [1, null]],          ],
            [   $iter([1, null]),              [[1, null], [1, null], $gen([1, null])],   ],
            [   $iter([1, null]),              [[1, null], [1, null], $iter([1, null])],   ],
            [   $iter([1, null]),              [[1, null], [1, null], $trav([1, null])],   ],
        ];
    }

    /**
     * @test         ExactlyN iterators true
     * @dataProvider dataProviderForTraversablesFalse
     * @param        \Traversable $data
     * @param        array<\Traversable> $anothers
     */
    public function testTraversablesFalse(\Traversable $data, array $anothers)
    {
        // Given: $data

        // When
        $result = Reduce::ExactlyN($data, ...$anothers);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForTraversablesFalse(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            //  data                    anothers
            [   $gen([]),                     [[1]],                                  ],
            [   $gen([]),                     [$gen([1])],                            ],
            [   $gen([]),                     [$iter([1])],                           ],
            [   $gen([]),                     [$trav([1])],                           ],

            [   $gen([1]),                    [[]],                                   ],
            [   $gen([1]),                    [$gen([])],                             ],
            [   $gen([1]),                    [$iter([])],                            ],
            [   $gen([1]),                    [$trav([])],                            ],

            [   $gen(['1']),                  [[1], [1], [1]],                        ],
            [   $gen(['1']),                  [$gen([1]), [1], [1]],                  ],
            [   $gen(['1']),                  [[1], $iter([1]), [1]],                 ],
            [   $gen(['1']),                  [[1], [1], $trav([1])],                 ],

            [   $gen([1]),                    [['1']],                                ],
            [   $gen([1]),                    [$gen(['1'])],                          ],
            [   $gen([1]),                    [$iter(['1'])],                         ],
            [   $gen([1]),                    [$trav(['1'])],                         ],

            [   $gen([1]),                    [[2]],                                  ],
            [   $gen([1]),                    [$gen([2])],                            ],
            [   $gen([1]),                    [$iter([2])],                           ],
            [   $gen([1]),                    [$trav([2])],                           ],

            [   $gen([1]),                    [['1'], [1]],                           ],
            [   $gen([1]),                    [$gen(['1']), [1]],                     ],
            [   $gen([1]),                    [$iter(['1']), [1]],                    ],
            [   $gen([1]),                    [$trav(['1']), [1]],                    ],
            [   $gen([1]),                    [['1'], $gen([1])],                     ],
            [   $gen([1]),                    [['1'], $iter([1])],                    ],
            [   $gen([1]),                    [['1'], $trav([1])],                    ],
            [   $gen([1]),                    [$iter(['1']), $gen([1])],              ],
            [   $gen([1]),                    [$trav(['1']), $iter([1])],             ],
            [   $gen([1]),                    [$gen(['1']), $trav([1])],              ],

            [   $gen([1]),                    [[1], ['1']],                           ],
            [   $gen([1]),                    [[1], [2], [1]],                        ],
            [   $gen([1]),                    [[1], ['1'], [1]],                      ],
            [   $gen([1, 2, 3]),              [[1, 2, 3], [1, 2, 3], [1, 2, '3']],    ],
            [   $gen([1, 2, 3]),              [[1, 2, 3], [1, 2, 3], [1, 2, 1]],      ],
            [   $gen([1]),                    [[1, null], [1, null], [1]],            ],
            [   $gen([1]),                    [[1, null], [1, null], [1, null]],      ],
            [   $gen([1, null]),              [[1], [1], [1]],                        ],
            [   $gen([1, null]),              [[1], [1, null], [1]],                  ],
        ];
    }
}
