<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class IsSortedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         isSorted array true
     * @dataProvider dataProviderForArrayTrue
     * @param        array $data
     */
    public function testArrayTrue(array $data)
    {
        // When
        $result = Summary::isSorted($data);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForArrayTrue(): array
    {
        return [
            [
                [],
            ],
            [
                [0],
            ],
            [
                [null],
            ],
            [
                [NAN],
            ],
            [
                [0, 1],
            ],
            [
                [null, null],
            ],
            [
                [null, 1],
            ],
            [
                [0, 0],
            ],
            [
                [1, 1],
            ],
            [
                [1, 2, 3],
            ],
            [
                [2, 2, 2],
            ],
            [
                [2, 2, 3],
            ],
            [
                ['a', 'b', 'c'],
            ],
            [
                [['a'], ['b'], ['c']],
            ],
            [
                [['b'], ['a', 'a']],
            ],
            [
                [['bb'], ['a', 'a']],
            ],
        ];
    }

    /**
     * @test         isSorted array false
     * @dataProvider dataProviderForArrayFalse
     * @param        array $data
     */
    public function testArrayFalse(array $data)
    {
        // When
        $result = Summary::isSorted($data);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForArrayFalse(): array
    {
        return [
            [
                [1, null],
            ],
            [
                [-1, null],
            ],
            [
                [1, 0],
            ],
            [
                [3, 2, 1],
            ],
            [
                [2, 3, 1],
            ],
            [
                ['b', 'a', 'c'],
            ],
            [
                [['b'], ['a'], ['c']],
            ],
            [
                [['a', 'a'], ['b']],
            ],
            [
                [1, NAN, 3],
            ],
            [
                [NAN, 1, 2],
            ],
            [
                [1, 2, NAN],
            ],
            [
                [NAN, NAN],
            ],
        ];
    }

    /**
     * @test         isSorted generators true
     * @dataProvider dataProviderForGeneratorsTrue
     * @param        \Generator $data
     */
    public function testGeneratorsTrue(\Generator $data)
    {
        // When
        $result = Summary::isSorted($data);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForGeneratorsTrue(): array
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
                $gen([NAN])
            ],
            [
                $gen([0, 1])
            ],
            [
                $gen([null, null])
            ],
            [
                $gen([null, 1])
            ],
            [
                $gen([0, 0])
            ],
            [
                $gen([1, 1])
            ],
            [
                $gen([1, 2, 3])
            ],
            [
                $gen([2, 2, 2])
            ],
            [
                $gen([2, 2, 3])
            ],
            [
                $gen(['a', 'b', 'c'])
            ],
            [
                $gen([['a'], ['b'], ['c']])
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
     * @test         isSorted generators false
     * @dataProvider dataProviderForGeneratorsFalse
     * @param        \Generator $data
     */
    public function testGeneratorsFalse(\Generator $data)
    {
        // When
        $result = Summary::isSorted($data);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForGeneratorsFalse(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [
                $gen([1, null])
            ],
            [
                $gen([-1, null])
            ],
            [
                $gen([1, 0])
            ],
            [
                $gen([3, 2, 1])
            ],
            [
                $gen([2, 3, 1])
            ],
            [
                $gen(['b', 'a', 'c'])
            ],
            [
                $gen([['b'], ['a'], ['c']])
            ],
            [
                $gen([['a', 'a'], ['b']])
            ],
            [
                $gen([1, NAN, 3])
            ],
            [
                $gen([NAN, 1, 2])
            ],
            [
                $gen([1, 2, NAN])
            ],
            [
                $gen([NAN, NAN])
            ],
        ];
    }

    /**
     * @test         isSorted iterators true
     * @dataProvider dataProviderForIteratorsTrue
     * @param        \Generator $data
     */
    public function testIteratorsTrue(\Iterator $data)
    {
        // When
        $result = Summary::isSorted($data);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForIteratorsTrue(): array
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
                $iter([NAN])
            ],
            [
                $iter([0, 1])
            ],
            [
                $iter([null, null])
            ],
            [
                $iter([null, 1])
            ],
            [
                $iter([0, 0])
            ],
            [
                $iter([1, 1])
            ],
            [
                $iter([1, 2, 3])
            ],
            [
                $iter([2, 2, 2])
            ],
            [
                $iter([2, 2, 3])
            ],
            [
                $iter(['a', 'b', 'c'])
            ],
            [
                $iter([['a'], ['b'], ['c']])
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
     * @test         isSorted iterators false
     * @dataProvider dataProviderForIteratorsFalse
     * @param        \Generator $data
     */
    public function testIteratorsFalse(\Iterator $data)
    {
        // When
        $result = Summary::isSorted($data);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForIteratorsFalse(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [
                $iter([1, null])
            ],
            [
                $iter([-1, null])
            ],
            [
                $iter([1, 0])
            ],
            [
                $iter([3, 2, 1])
            ],
            [
                $iter([2, 3, 1])
            ],
            [
                $iter(['b', 'a', 'c'])
            ],
            [
                $iter([['b'], ['a'], ['c']])
            ],
            [
                $iter([['a', 'a'], ['b']])
            ],
            [
                $iter([1, NAN, 3])
            ],
            [
                $iter([NAN, 1, 2])
            ],
            [
                $iter([1, 2, NAN])
            ],
            [
                $iter([NAN, NAN])
            ],
        ];
    }

    /**
     * @test         isSorted traversables true
     * @dataProvider dataProviderForTraversablesTrue
     * @param        \Traversable $data
     */
    public function testTraversablesTrue(\Traversable $data)
    {
        // When
        $result = Summary::isSorted($data);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForTraversablesTrue(): array
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
                $trav([NAN])
            ],
            [
                $trav([0, 1])
            ],
            [
                $trav([null, null])
            ],
            [
                $trav([null, 1])
            ],
            [
                $trav([0, 0])
            ],
            [
                $trav([1, 1])
            ],
            [
                $trav([1, 2, 3])
            ],
            [
                $trav([2, 2, 2])
            ],
            [
                $trav([2, 2, 3])
            ],
            [
                $trav(['a', 'b', 'c'])
            ],
            [
                $trav([['a'], ['b'], ['c']])
            ],
            [
                $trav([['b'], ['a', 'a']])
            ],
            [
                $trav([['bb'], ['a', 'a']])
            ],
        ];
    }

    /**
     * @test         isSorted traversables false
     * @dataProvider dataProviderForTraversablesFalse
     * @param        \Traversable $data
     */
    public function testTraversablesFalse(\Traversable $data)
    {
        // When
        $result = Summary::isSorted($data);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForTraversablesFalse(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                $trav([1, null])
            ],
            [
                $trav([-1, null])
            ],
            [
                $trav([1, 0])
            ],
            [
                $trav([3, 2, 1])
            ],
            [
                $trav([2, 3, 1])
            ],
            [
                $trav(['b', 'a', 'c'])
            ],
            [
                $trav([['b'], ['a'], ['c']])
            ],
            [
                $trav([['a', 'a'], ['b']])
            ],
            [
                $trav([1, NAN, 3])
            ],
            [
                $trav([NAN, 1, 2])
            ],
            [
                $trav([1, 2, NAN])
            ],
            [
                $trav([NAN, NAN])
            ],
        ];
    }
}
