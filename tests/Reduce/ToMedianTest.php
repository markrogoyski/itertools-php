<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToMedianTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test toMedian example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $grades = [100, 90, 95, 85, 94];

        // When
        $median = Reduce::toMedian($grades);

        // Then (statistics.median([100, 90, 95, 85, 94]))
        $this->assertSame(94, $median);
    }

    /**
     * @test         toMedian array
     * @dataProvider dataProviderForMedian
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testArray(array $data, $expected): void
    {
        // When
        $result = Reduce::toMedian($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toMedian generator
     * @dataProvider dataProviderForMedian
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testGenerator(array $data, $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toMedian($generator);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toMedian iterator
     * @dataProvider dataProviderForMedian
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testIterator(array $data, $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toMedian($iterator);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toMedian traversable
     * @dataProvider dataProviderForMedian
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testTraversable(array $data, $expected): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toMedian($traversable);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with:
     *   statistics.median(data)
     * The int-vs-float result type is the library's contract, not the oracle's.
     */
    public static function dataProviderForMedian(): array
    {
        return [
            // Single element
            [[42], 42],
            // Two elements
            [[10, 20], 15],
            [[20, 10], 15],
            // Odd length
            [[1, 2, 3], 2],
            // Odd length, unsorted (output invariant to order)
            [[3, 1, 2], 2],
            // Even length
            [[1, 2, 3, 4], 2.5],
            // Even length, unsorted (output invariant to order)
            [[4, 3, 2, 1], 2.5],
            // All-equal values
            [[5, 5, 5], 5],
            [[7, 7, 7, 7], 7],
            // Negative numbers
            [[-5, -1, -3], -3],
            [[-10, -20, -30, -40], -25],
            // Floats
            [[1.5, 2.5, 3.5], 2.5],
            [[0.1, 0.2, 0.3, 0.4], 0.25],
            // Mixed int and float
            [[1, 2.5, 4], 2.5],
            // Large N (1..1000 → median is average of 500 and 501)
            [\range(1, 1000), 500.5],
        ];
    }

    /**
     * @test         toMedian returns null on empty
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $data
     */
    public function testEmptyReturnsNull(iterable $data): void
    {
        // When
        $result = Reduce::toMedian($data);

        // Then
        $this->assertNull($result);
    }

    /**
     * @test toMedian is invariant to input order
     */
    public function testOrderInvariance(): void
    {
        // Given
        $sorted   = [1, 2, 3, 4, 5, 6];
        $unsorted = [4, 1, 6, 2, 5, 3];

        // When
        $fromSorted   = Reduce::toMedian($sorted);
        $fromUnsorted = Reduce::toMedian($unsorted);

        // Then (statistics.median([1, 2, 3, 4, 5, 6]))
        $this->assertSame(3.5, $fromSorted);
        $this->assertSame($fromSorted, $fromUnsorted);
    }

    /**
     * @test         toMedian does not overflow when the two middle values sum beyond PHP_FLOAT_MAX
     * @dataProvider dataProviderForHugeMiddleValues
     * @param        array $data
     * @param        float $expected
     */
    public function testEvenMedianOfHugeValuesDoesNotOverflow(array $data, float $expected): void
    {
        // When
        $result = Reduce::toMedian($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational midpoint, correctly rounded to a float):
     *   float((Fraction(a) + Fraction(b)) / 2)
     * statistics.median is not the oracle here: its (a + b) / 2 overflows to INF.
     */
    public static function dataProviderForHugeMiddleValues(): array
    {
        return [
            [[1e308, 1e308], 1e308],
            [[1e308, 1.5e308], 1.25e308],
            [[-1e308, -1e308], -1e308],
            [[-1.5e308, -1e308], -1.25e308],
            [[-1e308, 1e308], 0.0],
            [[-1.5e308, 1e308], -2.5e307],
        ];
    }

    /**
     * @test toMedian of the two integer extremes keeps the exactly representable half-unit result
     */
    public function testEvenMedianOfIntegerExtremesIsExact(): void
    {
        // Given the widest possible integer span, whose midpoint is exactly -0.5
        $data = [\PHP_INT_MIN, \PHP_INT_MAX];

        // When
        $result = Reduce::toMedian($data);

        // Then (float((Fraction(-2**63) + Fraction(2**63 - 1)) / 2) is -0.5, not 0.0)
        $this->assertSame(-0.5, $result);
    }

    /**
     * @test         toMedian of middle values -0.0 and 0.0 is 0.0 in either order, and -0.0 only when both are -0.0
     * @dataProvider dataProviderForSignedZeroMiddleValues
     * @param        array $data
     * @param        float $expectedReciprocal
     */
    public function testEvenMedianOfSignedZeros(array $data, float $expectedReciprocal): void
    {
        // When
        $result = Reduce::toMedian($data);

        // Then (assertSame() takes -0.0 and 0.0 as the same, so the sign is checked through 1 / ±0.0 = ±INF)
        $this->assertSame(0.0, $result);
        $this->assertSame($expectedReciprocal, \fdiv(1.0, $result));
    }

    /**
     * Test data generated with Python (the sign of the zero median, as the reciprocal ±INF):
     *   math.copysign(math.inf, statistics.median(data))
     */
    public static function dataProviderForSignedZeroMiddleValues(): array
    {
        return [
            [[-0.0, 0.0], \INF],
            [[0.0, -0.0], \INF],
            [[-0.0, -0.0], -\INF],
            [[0.0, 0.0], \INF],
            [[-0.0, 0], \INF],
            [[0, -0.0], \INF],
            [[1.0, -0.0, 0.0, -1.0], \INF],
            [[-1.0, 0.0, -0.0, 1.0], \INF],
            [[-0.0, -0.0, 1.0, -1.0], -\INF],
        ];
    }

    /**
     * @test         toMedian of an even count is the correctly rounded midpoint of the middle values
     * @dataProvider dataProviderForCorrectlyRoundedMidpoints
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testEvenMedianIsCorrectlyRounded(array $data, int|float $expected): void
    {
        // When
        $result = Reduce::toMedian($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational midpoint, correctly rounded to a float):
     *   float((Fraction(a) + Fraction(b)) / 2)
     * The int-vs-float result type is the library's contract, not the oracle's.
     */
    public static function dataProviderForCorrectlyRoundedMidpoints(): array
    {
        return [
            // Integers whose sum overflows a 32-bit integer (exact on 64-bit, a separate branch on 32-bit)
            [[1073741824, 1073741825], 1073741824.5],
            // Subnormals: 79 and 228 units of 2^-1074, so the midpoint 153.5 units rounds to even 154
            [[3.9E-322, 1.126E-321], 7.6E-322],
            // Floats far apart in magnitude
            [[3.769268241603519E+229, 2.4033014673755046E+243], 1.201650733687771E+243],
            // Floats whose sum overflows PHP_FLOAT_MAX
            [[7.130610204110192E+307, 1.6682355559978037E+308], 1.1906482882044114E+308],
            // A float operand keeps the result a float even when the midpoint is an integer
            [[2, 4.0], 3.0],
            [[2, 4], 3],
        ];
    }

    /**
     * @test         toMedian of an even count of 64-bit integers is the correctly rounded midpoint
     * @dataProvider dataProviderForCorrectlyRoundedSixtyFourBitMidpoints
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testEvenMedianOfSixtyFourBitIntegersIsCorrectlyRounded(array $data, int|float $expected): void
    {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toMedian($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational midpoint, correctly rounded to a float):
     *   float((Fraction(a) + Fraction(b)) / 2)
     * The int-vs-float result type is the library's contract, not the oracle's.
     */
    public static function dataProviderForCorrectlyRoundedSixtyFourBitMidpoints(): array
    {
        return [
            // Integers past 2^53: the true midpoint …993.5 lies between the floats …992 and …994
            [[9007199254740993, 9007199254740994], 9007199254740994.0],
            [[-9007199254740995, -9007199254740994], -9007199254740994.0],
            // Integers whose sum overflows the integer range
            [[-8111470270762753361, -6048612140903098404], -7.080041205832926E+18],
            [[6048612140903098405, 8111470270762753362], 7.080041205832926E+18],
            // An integer past 2^53 paired with an integral float
            [[9007199254740993, 9007199254740994.0], 9007199254740994.0],
            [[9007199254740998.0, 9007199254741541], 9007199254741270.0],
        ];
    }

    /**
     * @test         toMedian of two identical infinities is that infinity
     * @dataProvider dataProviderForIdenticalInfiniteMiddleValues
     * @param        array $data
     * @param        float $expected
     */
    public function testEvenMedianOfIdenticalInfinitiesIsThatInfinity(array $data, float $expected): void
    {
        // When
        $result = Reduce::toMedian($data);

        // Then (the midpoint of two equal values is that value, INF - INF notwithstanding)
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with:
     *   statistics.median(data)
     */
    public static function dataProviderForIdenticalInfiniteMiddleValues(): array
    {
        return [
            [[\INF, \INF], \INF],
            [[-\INF, -\INF], -\INF],
            // Sorts to [1, INF, INF, INF], so both middle values are INF
            [[1, \INF, \INF, \INF], \INF],
        ];
    }

    /**
     * @test         toMedian of an infinity and a finite value is that infinity
     * @dataProvider dataProviderForInfiniteAndFiniteMiddleValues
     * @param        array $data
     * @param        float $expected
     */
    public function testEvenMedianOfInfinityAndFiniteValueIsThatInfinity(array $data, float $expected): void
    {
        // When
        $result = Reduce::toMedian($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with:
     *   statistics.median(data)
     */
    public static function dataProviderForInfiniteAndFiniteMiddleValues(): array
    {
        return [
            // Same sign
            [[-\INF, -1], -\INF],
            [[-\INF, -1.5], -\INF],
            [[1, \INF], \INF],
            [[1.5, \INF], \INF],
            [[-\INF, -\PHP_FLOAT_MAX], -\INF],
            [[\PHP_FLOAT_MAX, \INF], \INF],
            // Opposite signs or a zero
            [[-\INF, 1], -\INF],
            [[-1, \INF], \INF],
            [[-\INF, 0], -\INF],
            [[0, \INF], \INF],
            // Sorts to [-INF, -INF, -2, 5], so the middle values are -INF and -2
            [[5, -2, -\INF, -\INF], -\INF],
        ];
    }

    /**
     * @test toMedian of opposite infinities is NAN
     */
    public function testEvenMedianOfOppositeInfinitiesIsNan(): void
    {
        // Given
        $data = [\INF, -\INF];

        // When
        $result = Reduce::toMedian($data);

        // Then (the midpoint of -INF and INF is undefined: statistics.median([INF, -INF]) is nan)
        $this->assertNan($result);
    }

    /**
     * @test toMedian of two identical middle values returns that value exactly
     */
    public function testEvenMedianOfIdenticalMiddleValuesIsExact(): void
    {
        // Given a value whose halving-and-summing is not exact in binary floating point
        $data = [0.1, 0.1];

        // When
        $result = Reduce::toMedian($data);

        // Then (statistics.median([0.1, 0.1]))
        $this->assertSame(0.1, $result);
    }

    /**
     * @test         toMedian of data holding NAN is NAN, array
     * @dataProvider dataProviderForNan
     * @param        array $data
     */
    public function testNanArray(array $data): void
    {
        // When
        $result = Reduce::toMedian($data);

        // Then
        $this->assertNan($result);
    }

    /**
     * @test         toMedian of data holding NAN is NAN, generator
     * @dataProvider dataProviderForNan
     * @param        array $data
     */
    public function testNanGenerator(array $data): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toMedian($generator);

        // Then
        $this->assertNan($result);
    }

    /**
     * @test         toMedian of data holding NAN is NAN, iterator
     * @dataProvider dataProviderForNan
     * @param        array $data
     */
    public function testNanIterator(array $data): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toMedian($iterator);

        // Then
        $this->assertNan($result);
    }

    /**
     * @test         toMedian of data holding NAN is NAN, traversable
     * @dataProvider dataProviderForNan
     * @param        array $data
     */
    public function testNanTraversable(array $data): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toMedian($traversable);

        // Then
        $this->assertNan($result);
    }

    /**
     * Test data generated with:
     *   numpy.median(data)
     * which is nan for every row, wherever the nan sits (scripts: tools/oracles/nan_median_percentile_quantile.py).
     * The rows cover odd and even lengths, a NAN first, in the middle and last, and a NAN among infinities.
     */
    public static function dataProviderForNan(): array
    {
        return [
            [[\NAN, 1, 2]],
            [[1, \NAN, 2]],
            [[1, 2, \NAN]],
            [[\NAN]],
            [[\NAN, \NAN]],
            [[1, 2, 3, \NAN]],
            [[\NAN, 4, 3, 2, 1]],
            [[1.5, \NAN, 2.5, 4.5]],
            [[-\INF, \NAN, \INF]],
            [[\NAN, 7, 7]],
        ];
    }

    /**
     * @test         toMedian array of ints beyond 2^53 and floats, ordered by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionArray(array $data, int|float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toMedian($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toMedian generator of ints beyond 2^53 and floats, ordered by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionGenerator(array $data, int|float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toMedian($iterable);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toMedian iterator of ints beyond 2^53 and floats, ordered by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionIterator(array $data, int|float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toMedian($iterable);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toMedian traversable of ints beyond 2^53 and floats, ordered by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionTraversable(array $data, int|float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toMedian($iterable);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which orders an int and a float by exact value
     * (tools/oracles/exact_order_percentile.py):
     *   s = sorted(data), n = len(s)
     *   a, b = s[n // 2 - 1], s[n // 2]
     *   s[n // 2] if n % 2 else (a if a == b else float((Fraction(a) + Fraction(b)) / 2))
     * The identical-middles branch is the library's contract (two identical middle values return that value).
     * Each int compares equal as a float to a float it is not equal to, so <=> orders them wrongly.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecision(): array
    {
        return [
            'odd: 2^62 + 500, 2.0^62, 2^62 + 1' => [
                [4611686018427388404, 4611686018427387904.0, 4611686018427387905],
                4611686018427387905,
            ],
            'even: identical int middles 2^53 + 1 around the float 2.0^53' => [
                [9007199254740993, 9007199254740992.0, 9007199254740993, 9007199254740995],
                9007199254740993,
            ],
        ];
    }

    /**
     * @test         toMedian array of two 32-bit integers whose sum overflows is an int for an even sum
     * @dataProvider dataProviderForEvenMedianOfAnOverflowingThirtyTwoBitSum
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testEvenMedianOfAnOverflowingThirtyTwoBitSumArray(array $data, int|float $expected): void
    {
        // Given (only on 32-bit PHP does the sum of the middle values overflow)
        if (\PHP_INT_SIZE !== 4) {
            $this->markTestSkipped('Requires 32-bit integers');
        }

        // When
        $result = Reduce::toMedian($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toMedian generator of two 32-bit integers whose sum overflows is an int for an even sum
     * @dataProvider dataProviderForEvenMedianOfAnOverflowingThirtyTwoBitSum
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testEvenMedianOfAnOverflowingThirtyTwoBitSumGenerator(array $data, int|float $expected): void
    {
        // Given (only on 32-bit PHP does the sum of the middle values overflow)
        if (\PHP_INT_SIZE !== 4) {
            $this->markTestSkipped('Requires 32-bit integers');
        }
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toMedian($iterable);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toMedian iterator of two 32-bit integers whose sum overflows is an int for an even sum
     * @dataProvider dataProviderForEvenMedianOfAnOverflowingThirtyTwoBitSum
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testEvenMedianOfAnOverflowingThirtyTwoBitSumIterator(array $data, int|float $expected): void
    {
        // Given (only on 32-bit PHP does the sum of the middle values overflow)
        if (\PHP_INT_SIZE !== 4) {
            $this->markTestSkipped('Requires 32-bit integers');
        }
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toMedian($iterable);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toMedian traversable of two 32-bit integers whose sum overflows is an int for an even sum
     * @dataProvider dataProviderForEvenMedianOfAnOverflowingThirtyTwoBitSum
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testEvenMedianOfAnOverflowingThirtyTwoBitSumTraversable(array $data, int|float $expected): void
    {
        // Given (only on 32-bit PHP does the sum of the middle values overflow)
        if (\PHP_INT_SIZE !== 4) {
            $this->markTestSkipped('Requires 32-bit integers');
        }
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toMedian($iterable);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python int arithmetic and fractions.Fraction
     * (tools/oracles/arithmetic_midpoint_oracles.py), for data = [lo, hi]:
     *   (lo + hi) // 2 if (lo + hi) % 2 == 0 else float(Fraction(lo + hi, 2))
     * keeping only rows with lo and hi in [-2^31, 2^31) whose sum is outside [-2^31, 2^31 - 1], so it
     * overflows on 32-bit PHP. -2^31 is written (-2147483647 - 1): the literal -2147483648 is a float there.
     * The oracle gives the exact value; its int-vs-float type is the library's contract, not the oracle's.
     */
    public static function dataProviderForEvenMedianOfAnOverflowingThirtyTwoBitSum(): array
    {
        return [
            'positive, even sum' => [[2147483645, 2147483647], 2147483646],
            'positive, odd sum' => [[2147483646, 2147483647], 2147483646.5],
            'positive, even sum, reversed' => [[2147483647, 2147483645], 2147483646],
            'positive, odd sum, reversed' => [[2147483647, 2147483646], 2147483646.5],
            'negative, even sum' => [[(-2147483647 - 1), -2147483646], -2147483647],
            'negative, odd sum' => [[(-2147483647 - 1), -2147483647], -2147483647.5],
            'negative, even sum, reversed' => [[-2147483646, (-2147483647 - 1)], -2147483647],
            'negative, odd sum, reversed' => [[-2147483647, (-2147483647 - 1)], -2147483647.5],
            '2^31 - 1 with itself' => [[2147483647, 2147483647], 2147483647],
            '-2^31 with itself' => [[(-2147483647 - 1), (-2147483647 - 1)], (-2147483647 - 1)],
            'smallest positive overflow, sum 2^31' => [[1073741824, 1073741824], 1073741824],
            'positive overflow, sum 2^31 + 1' => [[1073741824, 1073741825], 1073741824.5],
            'negative overflow, sum -2^31 - 1' => [[-1073741824, -1073741825], -1073741824.5],
            'negative overflow, sum -2^31 - 2' => [[-1073741825, -1073741825], -1073741825],
        ];
    }
}
