<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class IsReversedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         isReversed array true
     * @dataProvider dataProviderForArrayTrue
     * @param        array $data
     */
    public function testArrayTrue(array $data)
    {
        // When
        $result = Reduce::isReversed($data);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForArrayTrue(): array
    {
        return [
            [
                []
            ],
            [
                [0]
            ],
            [
                [null]
            ],
            [
                [null, null]
            ],
            [
                [1, null]
            ],
            [
                [-1, null]
            ],
            [
                [0, 0]
            ],
            [
                [1, 1]
            ],
            [
                [1, 0]
            ],
            [
                [3, 2, 1]
            ],
            [
                [2, 2, 2]
            ],
            [
                [['a', 'a'], ['b']]
            ],
        ];
    }

    /**
     * @test         isReversed array false
     * @dataProvider dataProviderForArrayFalse
     * @param        array $data
     */
    public function testArrayFalse(array $data)
    {
        // When
        $result = Reduce::isReversed($data);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForArrayFalse(): array
    {
        return [
            [
                [0, 1]
            ],
            [
                [null, 1]
            ],
            [
                [1, 2, 3]
            ],
            [
                [2, 2, 3]
            ],
            [
                [2, 3, 1]
            ],
            [
                ['a', 'b', 'c']
            ],
            [
                ['b', 'a', 'c']
            ],
            [
                [['a'], ['b'], ['c']]
            ],
            [
                [['b'], ['a'], ['c']]
            ],
            [
                [['b'], ['a', 'a']]
            ],
            [
                [['bb'], ['a', 'a']]
            ],
        ];
    }

    /**
     * @test         isReversed generators true
     * @dataProvider dataProviderForGeneratorsTrue
     * @param        \Generator $data
     */
    public function testGeneratorsTrue(\Generator $data)
    {
        // When
        $result = Reduce::isReversed($data);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForGeneratorsTrue(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [
                $gen([])
            ],
            [
                $gen([0])
            ],
            [
                $gen([null])
            ],
            [
                $gen([null, null])
            ],
            [
                $gen([1, null])
            ],
            [
                $gen([-1, null])
            ],
            [
                $gen([0, 0])
            ],
            [
                $gen([1, 1])
            ],
            [
                $gen([1, 0])
            ],
            [
                $gen([3, 2, 1])
            ],
            [
                $gen([2, 2, 2])
            ],
            [
                $gen([['a', 'a'], ['b']])
            ],
        ];
    }

    /**
     * @test         isReversed generators false
     * @dataProvider dataProviderForGeneratorsFalse
     * @param        \Generator $data
     */
    public function testGeneratorsFalse(\Generator $data)
    {
        // When
        $result = Reduce::isReversed($data);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForGeneratorsFalse(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [
                $gen([0, 1])
            ],
            [
                $gen([null, 1])
            ],
            [
                $gen([1, 2, 3])
            ],
            [
                $gen([2, 2, 3])
            ],
            [
                $gen([2, 3, 1])
            ],
            [
                $gen(['a', 'b', 'c'])
            ],
            [
                $gen(['b', 'a', 'c'])
            ],
            [
                $gen([['a'], ['b'], ['c']])
            ],
            [
                $gen([['b'], ['a'], ['c']])
            ],
            [
                $gen([['b'], ['a', 'a']])
            ],
            [
                $gen([['bb'], ['a', 'a']])
            ],
        ];
    }

    /**
     * @test         isReversed iterators true
     * @dataProvider dataProviderForIteratorsTrue
     * @param        \Generator $data
     */
    public function testIteratorsTrue(\Iterator $data)
    {
        // When
        $result = Reduce::isReversed($data);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForIteratorsTrue(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [
                $iter([])
            ],
            [
                $iter([0])
            ],
            [
                $iter([null])
            ],
            [
                $iter([null, null])
            ],
            [
                $iter([1, null])
            ],
            [
                $iter([-1, null])
            ],
            [
                $iter([0, 0])
            ],
            [
                $iter([1, 1])
            ],
            [
                $iter([1, 0])
            ],
            [
                $iter([3, 2, 1])
            ],
            [
                $iter([2, 2, 2])
            ],
            [
                $iter([['a', 'a'], ['b']])
            ],
        ];
    }

    /**
     * @test         isReversed iterators false
     * @dataProvider dataProviderForIteratorsFalse
     * @param        \Generator $data
     */
    public function testIteratorsFalse(\Iterator $data)
    {
        // When
        $result = Reduce::isReversed($data);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForIteratorsFalse(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [
                $iter([0, 1])
            ],
            [
                $iter([null, 1])
            ],
            [
                $iter([1, 2, 3])
            ],
            [
                $iter([2, 2, 3])
            ],
            [
                $iter([2, 3, 1])
            ],
            [
                $iter(['a', 'b', 'c'])
            ],
            [
                $iter(['b', 'a', 'c'])
            ],
            [
                $iter([['a'], ['b'], ['c']])
            ],
            [
                $iter([['b'], ['a'], ['c']])
            ],
            [
                $iter([['b'], ['a', 'a']])
            ],
            [
                $iter([['bb'], ['a', 'a']])
            ],
        ];
    }

    /**
     * @test         isReversed traversables
     * @dataProvider dataProviderForTraversablesTrue
     * @param        \Traversable $data
     */
    public function testTraversablesTrue(\Traversable $data)
    {
        // When
        $result = Reduce::isReversed($data);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForTraversablesTrue(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                $trav([])
            ],
            [
                $trav([0])
            ],
            [
                $trav([null])
            ],
            [
                $trav([null, null])
            ],
            [
                $trav([1, null])
            ],
            [
                $trav([-1, null])
            ],
            [
                $trav([0, 0])
            ],
            [
                $trav([1, 1])
            ],
            [
                $trav([1, 0])
            ],
            [
                $trav([3, 2, 1])
            ],
            [
                $trav([2, 2, 2])
            ],
            [
                $trav([['a', 'a'], ['b']])
            ],
        ];
    }

    /**
     * @test         isReversed traversables
     * @dataProvider dataProviderForTraversablesFalse
     * @param        \Traversable $data
     */
    public function testTraversablesFalse(\Traversable $data)
    {
        // When
        $result = Reduce::isReversed($data);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForTraversablesFalse(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                $trav([0, 1])
            ],
            [
                $trav([null, 1])
            ],
            [
                $trav([1, 2, 3])
            ],
            [
                $trav([2, 2, 3])
            ],
            [
                $trav([2, 3, 1])
            ],
            [
                $trav(['a', 'b', 'c'])
            ],
            [
                $trav(['b', 'a', 'c'])
            ],
            [
                $trav([['a'], ['b'], ['c']])
            ],
            [
                $trav([['b'], ['a'], ['c']])
            ],
            [
                $trav([['b'], ['a', 'a']])
            ],
            [
                $trav([['bb'], ['a', 'a']])
            ],
        ];
    }
}
