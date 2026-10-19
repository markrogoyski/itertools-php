<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToProductTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test         toProduct array
     * @dataProvider dataProviderForArray
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testArray(array $data, int|float $expected): void
    {
        // When
        $result = Reduce::toProduct($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational product, correctly rounded to a float):
     *   float(math.prod(map(Fraction, data)))
     * null and false are coerced to 0 and true to 1, as PHP arithmetic does.
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
                [null],
                0,
            ],
            [
                [false],
                0,
            ],
            [
                [null, null],
                0,
            ],
            [
                [null, false],
                0,
            ],
            [
                [true, false],
                0,
            ],
            [
                [false, true],
                0,
            ],
            [
                [0, null, false],
                0,
            ],
            [
                [1, null, false],
                0,
            ],
            [
                [1, null, true],
                0,
            ],
            [
                [2, 2, 3],
                12,
            ],
            [
                [1.1, 2.2, 3.3],
                7.986000000000001,
            ],
            [
                [1.1, 2, 3.3],
                7.26,
            ],
        ];
    }

    /**
     * @test         toProduct generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        int|float  $expected
     */
    public function testGenerators(\Generator $data, int|float $expected): void
    {
        // When
        $result = Reduce::toProduct($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational product, correctly rounded to a float):
     *   float(math.prod(map(Fraction, data)))
     * null and false are coerced to 0 and true to 1, as PHP arithmetic does.
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
                $gen([null]),
                0,
            ],
            [
                $gen([false]),
                0,
            ],
            [
                $gen([null, null]),
                0,
            ],
            [
                $gen([null, false]),
                0,
            ],
            [
                $gen([true, false]),
                0,
            ],
            [
                $gen([false, true]),
                0,
            ],
            [
                $gen([0, null, false]),
                0,
            ],
            [
                $gen([1, null, false]),
                0,
            ],
            [
                $gen([1, null, true]),
                0,
            ],
            [
                $gen([2, 2, 3]),
                12,
            ],
            [
                $gen([1.1, 2.2, 3.3]),
                7.986000000000001,
            ],
            [
                $gen([1.1, 2, 3.3]),
                7.26,
            ],
        ];
    }

    /**
     * @test         toProduct iterators
     * @dataProvider dataProviderForIterators
     * @param        \Iterator $data
     * @param        int|float $expected
     */
    public function testIterators(\Iterator $data, int|float $expected): void
    {
        // When
        $result = Reduce::toProduct($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational product, correctly rounded to a float):
     *   float(math.prod(map(Fraction, data)))
     * null and false are coerced to 0 and true to 1, as PHP arithmetic does.
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
                $iter([null]),
                0,
            ],
            [
                $iter([false]),
                0,
            ],
            [
                $iter([null, null]),
                0,
            ],
            [
                $iter([null, false]),
                0,
            ],
            [
                $iter([true, false]),
                0,
            ],
            [
                $iter([false, true]),
                0,
            ],
            [
                $iter([0, null, false]),
                0,
            ],
            [
                $iter([1, null, false]),
                0,
            ],
            [
                $iter([1, null, true]),
                0,
            ],
            [
                $iter([2, 2, 3]),
                12,
            ],
            [
                $iter([1.1, 2.2, 3.3]),
                7.986000000000001,
            ],
            [
                $iter([1.1, 2, 3.3]),
                7.26,
            ],
        ];
    }

    /**
     * @test         toProduct traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        int|float    $expected
     */
    public function testTraversables(\Traversable $data, int|float $expected): void
    {
        // When
        $result = Reduce::toProduct($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational product, correctly rounded to a float):
     *   float(math.prod(map(Fraction, data)))
     * null and false are coerced to 0 and true to 1, as PHP arithmetic does.
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
                $trav([null]),
                0,
            ],
            [
                $trav([false]),
                0,
            ],
            [
                $trav([null, null]),
                0,
            ],
            [
                $trav([null, false]),
                0,
            ],
            [
                $trav([true, false]),
                0,
            ],
            [
                $trav([false, true]),
                0,
            ],
            [
                $trav([0, null, false]),
                0,
            ],
            [
                $trav([1, null, false]),
                0,
            ],
            [
                $trav([1, null, true]),
                0,
            ],
            [
                $trav([2, 2, 3]),
                12,
            ],
            [
                $trav([1.1, 2.2, 3.3]),
                7.986000000000001,
            ],
            [
                $trav([1.1, 2, 3.3]),
                7.26,
            ],
        ];
    }

    /**
     * @test         toProduct returns null on empty
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $data
     */
    public function testEmptyReturnsNull(iterable $data): void
    {
        // When
        $result = Reduce::toProduct($data);

        // Then
        $this->assertNull($result);
    }
}
