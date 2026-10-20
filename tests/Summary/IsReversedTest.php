<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
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
        $result = Summary::isReversed($data);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForArrayTrue(): array
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
                [NAN]
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
        $result = Summary::isReversed($data);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForArrayFalse(): array
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
            [
                [3, NAN, 1]
            ],
            [
                [NAN, 2, 1]
            ],
            [
                [3, 2, NAN]
            ],
            [
                [NAN, NAN]
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
        $result = Summary::isReversed($data);

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
        $result = Summary::isReversed($data);

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
            [
                $gen([3, NAN, 1])
            ],
            [
                $gen([NAN, 2, 1])
            ],
            [
                $gen([3, 2, NAN])
            ],
            [
                $gen([NAN, NAN])
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
        $result = Summary::isReversed($data);

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
        $result = Summary::isReversed($data);

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
            [
                $iter([3, NAN, 1])
            ],
            [
                $iter([NAN, 2, 1])
            ],
            [
                $iter([3, 2, NAN])
            ],
            [
                $iter([NAN, NAN])
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
        $result = Summary::isReversed($data);

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
        $result = Summary::isReversed($data);

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
            [
                $trav([3, NAN, 1])
            ],
            [
                $trav([NAN, 2, 1])
            ],
            [
                $trav([3, 2, NAN])
            ],
            [
                $trav([NAN, NAN])
            ],
        ];
    }

    /**
     * @test         isReversed array of an int beyond 2^53 and a float, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionArray
     * @param        array $data
     * @param        bool $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionArray(array $data, bool $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Summary::isReversed($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which compares int and float by exact value:
     *   all(a >= b for a, b in pairwise(data))
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionArray(): array
    {
        return [
            'float 2.0^53 then int 2^53 + 1' => [
                [9007199254740992.0, 9007199254740993],
                false,
            ],
            'int 2^53 + 1 then float 2.0^53' => [
                [9007199254740993, 9007199254740992.0],
                true,
            ],
            'int 2^53 + 1 then float 2.0^53 + 2' => [
                [9007199254740993, 9007199254740994.0],
                false,
            ],
            'float 2.0^53 + 2 then int 2^53 + 1' => [
                [9007199254740994.0, 9007199254740993],
                true,
            ],
            'int -(2^53 + 1) then float -2.0^53' => [
                [-9007199254740993, -9007199254740992.0],
                false,
            ],
            'float -2.0^53 then int -(2^53 + 1)' => [
                [-9007199254740992.0, -9007199254740993],
                true,
            ],
            'int PHP_INT_MAX then float 2.0^63' => [
                [9223372036854775807, 9.223372036854776e+18],
                false,
            ],
            'float 2.0^63 then int PHP_INT_MAX' => [
                [9.223372036854776e+18, 9223372036854775807],
                true,
            ],
            'exact tie int 2^53 and float 2.0^53' => [
                [9007199254740992, 9007199254740992.0],
                true,
            ],
            'floats and ints rising by 1 beyond 2^53' => [
                [9007199254740992.0, 9007199254740993, 9007199254740994, 9007199254740995, 9007199254740996.0],
                false,
            ],
            'float 2.0^53 between ints that equal it as floats' => [
                [9007199254740993, 9007199254740992.0, 9007199254740993],
                false,
            ],
        ];
    }

    /**
     * @test         isReversed generators of an int beyond 2^53 and a float, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionGenerators
     * @param        \Generator $data
     * @param        bool $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionGenerators(\Generator $data, bool $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Summary::isReversed($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which compares int and float by exact value:
     *   all(a >= b for a, b in pairwise(data))
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            'float 2.0^53 then int 2^53 + 1' => [
                $gen([9007199254740992.0, 9007199254740993]),
                false,
            ],
            'int 2^53 + 1 then float 2.0^53' => [
                $gen([9007199254740993, 9007199254740992.0]),
                true,
            ],
            'int 2^53 + 1 then float 2.0^53 + 2' => [
                $gen([9007199254740993, 9007199254740994.0]),
                false,
            ],
            'float 2.0^53 + 2 then int 2^53 + 1' => [
                $gen([9007199254740994.0, 9007199254740993]),
                true,
            ],
            'int -(2^53 + 1) then float -2.0^53' => [
                $gen([-9007199254740993, -9007199254740992.0]),
                false,
            ],
            'float -2.0^53 then int -(2^53 + 1)' => [
                $gen([-9007199254740992.0, -9007199254740993]),
                true,
            ],
            'int PHP_INT_MAX then float 2.0^63' => [
                $gen([9223372036854775807, 9.223372036854776e+18]),
                false,
            ],
            'float 2.0^63 then int PHP_INT_MAX' => [
                $gen([9.223372036854776e+18, 9223372036854775807]),
                true,
            ],
            'exact tie int 2^53 and float 2.0^53' => [
                $gen([9007199254740992, 9007199254740992.0]),
                true,
            ],
            'floats and ints rising by 1 beyond 2^53' => [
                $gen([9007199254740992.0, 9007199254740993, 9007199254740994, 9007199254740995, 9007199254740996.0]),
                false,
            ],
            'float 2.0^53 between ints that equal it as floats' => [
                $gen([9007199254740993, 9007199254740992.0, 9007199254740993]),
                false,
            ],
        ];
    }

    /**
     * @test         isReversed iterators of an int beyond 2^53 and a float, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionIterators
     * @param        \Iterator $data
     * @param        bool $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionIterators(\Iterator $data, bool $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Summary::isReversed($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which compares int and float by exact value:
     *   all(a >= b for a, b in pairwise(data))
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            'float 2.0^53 then int 2^53 + 1' => [
                $iter([9007199254740992.0, 9007199254740993]),
                false,
            ],
            'int 2^53 + 1 then float 2.0^53' => [
                $iter([9007199254740993, 9007199254740992.0]),
                true,
            ],
            'int 2^53 + 1 then float 2.0^53 + 2' => [
                $iter([9007199254740993, 9007199254740994.0]),
                false,
            ],
            'float 2.0^53 + 2 then int 2^53 + 1' => [
                $iter([9007199254740994.0, 9007199254740993]),
                true,
            ],
            'int -(2^53 + 1) then float -2.0^53' => [
                $iter([-9007199254740993, -9007199254740992.0]),
                false,
            ],
            'float -2.0^53 then int -(2^53 + 1)' => [
                $iter([-9007199254740992.0, -9007199254740993]),
                true,
            ],
            'int PHP_INT_MAX then float 2.0^63' => [
                $iter([9223372036854775807, 9.223372036854776e+18]),
                false,
            ],
            'float 2.0^63 then int PHP_INT_MAX' => [
                $iter([9.223372036854776e+18, 9223372036854775807]),
                true,
            ],
            'exact tie int 2^53 and float 2.0^53' => [
                $iter([9007199254740992, 9007199254740992.0]),
                true,
            ],
            'floats and ints rising by 1 beyond 2^53' => [
                $iter([9007199254740992.0, 9007199254740993, 9007199254740994, 9007199254740995, 9007199254740996.0]),
                false,
            ],
            'float 2.0^53 between ints that equal it as floats' => [
                $iter([9007199254740993, 9007199254740992.0, 9007199254740993]),
                false,
            ],
        ];
    }

    /**
     * @test         isReversed traversables of an int beyond 2^53 and a float, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionTraversables
     * @param        \Traversable $data
     * @param        bool $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionTraversables(\Traversable $data, bool $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Summary::isReversed($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which compares int and float by exact value:
     *   all(a >= b for a, b in pairwise(data))
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            'float 2.0^53 then int 2^53 + 1' => [
                $trav([9007199254740992.0, 9007199254740993]),
                false,
            ],
            'int 2^53 + 1 then float 2.0^53' => [
                $trav([9007199254740993, 9007199254740992.0]),
                true,
            ],
            'int 2^53 + 1 then float 2.0^53 + 2' => [
                $trav([9007199254740993, 9007199254740994.0]),
                false,
            ],
            'float 2.0^53 + 2 then int 2^53 + 1' => [
                $trav([9007199254740994.0, 9007199254740993]),
                true,
            ],
            'int -(2^53 + 1) then float -2.0^53' => [
                $trav([-9007199254740993, -9007199254740992.0]),
                false,
            ],
            'float -2.0^53 then int -(2^53 + 1)' => [
                $trav([-9007199254740992.0, -9007199254740993]),
                true,
            ],
            'int PHP_INT_MAX then float 2.0^63' => [
                $trav([9223372036854775807, 9.223372036854776e+18]),
                false,
            ],
            'float 2.0^63 then int PHP_INT_MAX' => [
                $trav([9.223372036854776e+18, 9223372036854775807]),
                true,
            ],
            'exact tie int 2^53 and float 2.0^53' => [
                $trav([9007199254740992, 9007199254740992.0]),
                true,
            ],
            'floats and ints rising by 1 beyond 2^53' => [
                $trav([9007199254740992.0, 9007199254740993, 9007199254740994, 9007199254740995, 9007199254740996.0]),
                false,
            ],
            'float 2.0^53 between ints that equal it as floats' => [
                $trav([9007199254740993, 9007199254740992.0, 9007199254740993]),
                false,
            ],
        ];
    }
}
