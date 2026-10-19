<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToRangeTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test         toRange array
     * @dataProvider dataProviderForArray
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testArray(array $data, int|float $expected): void
    {
        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     * Numeric strings are converted as PHP does: integer strings to int, the rest to float.
     * The int-vs-float result type is the library's contract, not the oracle's.
     */
    public static function dataProviderForArray(): array
    {
        return [
            [
                [0],
                0,
            ],
            [
                [1],
                0,
            ],
            [
                [-1],
                0,
            ],
            [
                [-1, -3, -5],
                4,
            ],
            [
                [3, 1, 2, -3, -1, -2],
                6,
            ],
            [
                [2.2, 3.3, 1.1],
                2.1999999999999997,
            ],
            [
                [2, 3.3, 1.1],
                2.1999999999999997,
            ],
            [
                [2.2, -3.3, -1.1, 2.2, 5.5],
                8.8,
            ],
            [
                ['2.2', '-3.3', '-1.1', '2.2', '5.5'],
                8.8,
            ],
            [
                ['3', '4', '1'],
                3,
            ],
            [
                [2, -3.3, '-1.1', 2.2, '5'],
                8.3,
            ],
        ];
    }

    /**
     * @test         toRange generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        int|float  $expected
     */
    public function testGenerators(\Generator $data, int|float $expected): void
    {
        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     * Numeric strings are converted as PHP does: integer strings to int, the rest to float.
     * The int-vs-float result type is the library's contract, not the oracle's.
     */
    public static function dataProviderForGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [
                $gen([0]),
                0,
            ],
            [
                $gen([1]),
                0,
            ],
            [
                $gen([-1]),
                0,
            ],
            [
                $gen([-1, -3, -5]),
                4,
            ],
            [
                $gen([3, 1, 2, -3, -1, -2]),
                6,
            ],
            [
                $gen([2.2, 3.3, 1.1]),
                2.1999999999999997,
            ],
            [
                $gen([2, 3.3, 1.1]),
                2.1999999999999997,
            ],
            [
                $gen([2.2, -3.3, -1.1, 2.2, 5.5]),
                8.8,
            ],
            [
                $gen(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                8.8,
            ],
            [
                $gen(['3', '4', '1']),
                3,
            ],
            [
                $gen([2, -3.3, '-1.1', 2.2, '5']),
                8.3,
            ],
        ];
    }

    /**
     * @test         toRange iterators
     * @dataProvider dataProviderForIterators
     * @param        \Iterator $data
     * @param        int|float $expected
     */
    public function testIterators(\Iterator $data, int|float $expected): void
    {
        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     * Numeric strings are converted as PHP does: integer strings to int, the rest to float.
     * The int-vs-float result type is the library's contract, not the oracle's.
     */
    public static function dataProviderForIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [
                $iter([0]),
                0,
            ],
            [
                $iter([1]),
                0,
            ],
            [
                $iter([-1]),
                0,
            ],
            [
                $iter([-1, -3, -5]),
                4,
            ],
            [
                $iter([3, 1, 2, -3, -1, -2]),
                6,
            ],
            [
                $iter([2.2, 3.3, 1.1]),
                2.1999999999999997,
            ],
            [
                $iter([2, 3.3, 1.1]),
                2.1999999999999997,
            ],
            [
                $iter([2.2, -3.3, -1.1, 2.2, 5.5]),
                8.8,
            ],
            [
                $iter(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                8.8,
            ],
            [
                $iter(['3', '4', '1']),
                3,
            ],
            [
                $iter([2, -3.3, '-1.1', 2.2, '5']),
                8.3,
            ],
        ];
    }

    /**
     * @test         toRange traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        int|float    $expected
     */
    public function testTraversables(\Traversable $data, int|float $expected): void
    {
        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     * Numeric strings are converted as PHP does: integer strings to int, the rest to float.
     * The int-vs-float result type is the library's contract, not the oracle's.
     */
    public static function dataProviderForTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                $trav([0]),
                0,
            ],
            [
                $trav([1]),
                0,
            ],
            [
                $trav([-1]),
                0,
            ],
            [
                $trav([-1, -3, -5]),
                4,
            ],
            [
                $trav([3, 1, 2, -3, -1, -2]),
                6,
            ],
            [
                $trav([2.2, 3.3, 1.1]),
                2.1999999999999997,
            ],
            [
                $trav([2, 3.3, 1.1]),
                2.1999999999999997,
            ],
            [
                $trav([2.2, -3.3, -1.1, 2.2, 5.5]),
                8.8,
            ],
            [
                $trav(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                8.8,
            ],
            [
                $trav(['3', '4', '1']),
                3,
            ],
            [
                $trav([2, -3.3, '-1.1', 2.2, '5']),
                8.3,
            ],
        ];
    }

    /**
     * @test         toRange returns zero on empty
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $data
     */
    public function testEmptyReturnsZero(iterable $data): void
    {
        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame(0, $result);
    }

    /**
     * @test         toRange array of an int beyond 2^53 and a float, which compare equal or subtract wrongly as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionArray
     * @param        array        $data
     * @param        float        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionArray(array $data, float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact comparison and rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     * NAN is skipped, and integer strings are converted to int, as PHP does.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionArray(): array
    {
        return [
            'int 2^53 + 1 below float 2.0^53 + 2' => [
                [9007199254740993, 9007199254740994.0],
                1.0,
            ],
            'int 2^53 + 1 above float 2.0^53, equal as floats' => [
                [9007199254740993, 9007199254740992.0],
                1.0,
            ],
            'float 2.0^53 first, below int 2^53 + 1, equal as floats' => [
                [9007199254740992.0, 9007199254740993],
                1.0,
            ],
            'minimum is the float that equals the last int as floats' => [
                [9007199254740994, 9007199254740992.0, 9007199254740993],
                2.0,
            ],
            'minimum is the first float, maximum the last int' => [
                [9007199254740992.0, 9007199254740993, 9007199254740994],
                2.0,
            ],
            'negative int -(2^53 + 1) below float -2.0^53' => [
                [-9007199254740993, -9007199254740992.0],
                1.0,
            ],
            'int 2^62 + 1 below float 2.0^62 + 1024' => [
                [4611686018427387905, 4611686018427388928.0],
                1023.0,
            ],
            'NAN skipped' => [
                [9007199254740993, \NAN, 9007199254740992.0],
                1.0,
            ],
            'integer string 2^53 + 1 above float 2.0^53' => [
                ['9007199254740993', 9007199254740992.0],
                1.0,
            ],
        ];
    }

    /**
     * @test         toRange generators of an int beyond 2^53 and a float, which compare equal or subtract wrongly as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionGenerators
     * @param        \Generator   $data
     * @param        float        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionGenerators(\Generator $data, float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact comparison and rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     * NAN is skipped, and integer strings are converted to int, as PHP does.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            'int 2^53 + 1 below float 2.0^53 + 2' => [
                $gen([9007199254740993, 9007199254740994.0]),
                1.0,
            ],
            'int 2^53 + 1 above float 2.0^53, equal as floats' => [
                $gen([9007199254740993, 9007199254740992.0]),
                1.0,
            ],
            'float 2.0^53 first, below int 2^53 + 1, equal as floats' => [
                $gen([9007199254740992.0, 9007199254740993]),
                1.0,
            ],
            'minimum is the float that equals the last int as floats' => [
                $gen([9007199254740994, 9007199254740992.0, 9007199254740993]),
                2.0,
            ],
            'minimum is the first float, maximum the last int' => [
                $gen([9007199254740992.0, 9007199254740993, 9007199254740994]),
                2.0,
            ],
            'negative int -(2^53 + 1) below float -2.0^53' => [
                $gen([-9007199254740993, -9007199254740992.0]),
                1.0,
            ],
            'int 2^62 + 1 below float 2.0^62 + 1024' => [
                $gen([4611686018427387905, 4611686018427388928.0]),
                1023.0,
            ],
            'NAN skipped' => [
                $gen([9007199254740993, \NAN, 9007199254740992.0]),
                1.0,
            ],
            'integer string 2^53 + 1 above float 2.0^53' => [
                $gen(['9007199254740993', 9007199254740992.0]),
                1.0,
            ],
        ];
    }

    /**
     * @test         toRange iterators of an int beyond 2^53 and a float, which compare equal or subtract wrongly as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionIterators
     * @param        \Iterator    $data
     * @param        float        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionIterators(\Iterator $data, float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact comparison and rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     * NAN is skipped, and integer strings are converted to int, as PHP does.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            'int 2^53 + 1 below float 2.0^53 + 2' => [
                $iter([9007199254740993, 9007199254740994.0]),
                1.0,
            ],
            'int 2^53 + 1 above float 2.0^53, equal as floats' => [
                $iter([9007199254740993, 9007199254740992.0]),
                1.0,
            ],
            'float 2.0^53 first, below int 2^53 + 1, equal as floats' => [
                $iter([9007199254740992.0, 9007199254740993]),
                1.0,
            ],
            'minimum is the float that equals the last int as floats' => [
                $iter([9007199254740994, 9007199254740992.0, 9007199254740993]),
                2.0,
            ],
            'minimum is the first float, maximum the last int' => [
                $iter([9007199254740992.0, 9007199254740993, 9007199254740994]),
                2.0,
            ],
            'negative int -(2^53 + 1) below float -2.0^53' => [
                $iter([-9007199254740993, -9007199254740992.0]),
                1.0,
            ],
            'int 2^62 + 1 below float 2.0^62 + 1024' => [
                $iter([4611686018427387905, 4611686018427388928.0]),
                1023.0,
            ],
            'NAN skipped' => [
                $iter([9007199254740993, \NAN, 9007199254740992.0]),
                1.0,
            ],
            'integer string 2^53 + 1 above float 2.0^53' => [
                $iter(['9007199254740993', 9007199254740992.0]),
                1.0,
            ],
        ];
    }

    /**
     * @test         toRange traversables of an int beyond 2^53 and a float, which compare equal or subtract wrongly as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionTraversables
     * @param        \Traversable $data
     * @param        float        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionTraversables(\Traversable $data, float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact comparison and rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     * NAN is skipped, and integer strings are converted to int, as PHP does.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            'int 2^53 + 1 below float 2.0^53 + 2' => [
                $trav([9007199254740993, 9007199254740994.0]),
                1.0,
            ],
            'int 2^53 + 1 above float 2.0^53, equal as floats' => [
                $trav([9007199254740993, 9007199254740992.0]),
                1.0,
            ],
            'float 2.0^53 first, below int 2^53 + 1, equal as floats' => [
                $trav([9007199254740992.0, 9007199254740993]),
                1.0,
            ],
            'minimum is the float that equals the last int as floats' => [
                $trav([9007199254740994, 9007199254740992.0, 9007199254740993]),
                2.0,
            ],
            'minimum is the first float, maximum the last int' => [
                $trav([9007199254740992.0, 9007199254740993, 9007199254740994]),
                2.0,
            ],
            'negative int -(2^53 + 1) below float -2.0^53' => [
                $trav([-9007199254740993, -9007199254740992.0]),
                1.0,
            ],
            'int 2^62 + 1 below float 2.0^62 + 1024' => [
                $trav([4611686018427387905, 4611686018427388928.0]),
                1023.0,
            ],
            'NAN skipped' => [
                $trav([9007199254740993, \NAN, 9007199254740992.0]),
                1.0,
            ],
            'integer string 2^53 + 1 above float 2.0^53' => [
                $trav(['9007199254740993', 9007199254740992.0]),
                1.0,
            ],
        ];
    }
}
