<?php

declare(strict_types=1);

namespace IterTools\Tests\Util\Precision;

use IterTools\Reduce;
use IterTools\Single;
use IterTools\Util\Precision\ExactFloat;
use IterTools\Util\Precision\Ulp;

/**
 * Equivalence test, not an oracle test: each row runs an existing private exact-float helper and the ExactFloat
 * method meant to replace it on the same inputs, and asserts the results are identical bit for bit (so -0.0 and
 * 0.0 differ, and the result arrays must match in length, keys and order). The rows for the replaced helpers are
 * temporary and go away with them.
 *
 * The inputs come from one shared corpus of specials and fixed-seed pseudo-random values, kept inside every
 * compared method's documented preconditions:
 *  - Floats are finite, never NAN or INF, with |x| < 2^997, so no sum, difference or TwoSum intermediate
 *    overflows. PHP rounds to nearest. This is the domain of ExactFloat::twoSum, Reduce::twoSumError and
 *    Single::exactFloatDifference; the operands need no order of magnitude.
 *  - Single::exactFloatDifference documents no order of its operands, so both orders are compared.
 *  - Products take only the pairs inside both documented exactness domains: ExactFloat::twoProduct (no factor
 *    past 2^996, where the split overflows, and no product overflow or underflow), and Reduce::exactProduct and
 *    Reduce::twoProductError (one factor integer-valued or a power of two, and nothing overflowing). So each
 *    factor is at most 2^996, a factor is integer-valued or a power of two, and the product is zero from a zero
 *    factor or normal and at most 2^1000. Subnormal products are left out even with an integer factor.
 *  - Ints are any int, which Reduce::toFloatWithResidual and ExactFloat::intToFloats both accept.
 *  - Expansions sum at most 8 corpus terms, so neither their exact sum nor a partial sum overflows, as
 *    ExactFloat::expansion requires. Reduce::growExpansion needs a nonoverlapping expansion in increasing
 *    magnitude, which folding it from [] provides; the fold skips both signed zeros, as expansion does.
 *
 * Bit patterns come from Ulp::floatBits, so the test runs on 64-bit PHP only.
 */
class ExactFloatEquivalenceTest extends \PHPUnit\Framework\TestCase
{
    private const SEED = 20261114;

    protected function setUp(): void
    {
        if (\PHP_INT_SIZE !== 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
    }

    /**
     * @test         exactFloatDifference(h, l) matches twoSum(-l, h) bit for bit
     * @dataProvider dataProviderForPairs
     * @param        float $high
     * @param        float $low
     */
    public function testExactFloatDifferenceMatchesTwoSum(float $high, float $low): void
    {
        // Given
        $method = new \ReflectionMethod(Single::class, 'exactFloatDifference');

        // When
        $old = $method->invoke(null, $high, $low);
        $new = ExactFloat::twoSum(-$low, $high);

        // Then
        $this->assertSame(self::bits($old), self::bits($new));
    }

    /**
     * @test         [x + y, twoSumError(x, y, x + y)] matches twoSum(x, y) bit for bit
     * @dataProvider dataProviderForPairs
     * @param        float $x
     * @param        float $y
     */
    public function testTwoSumErrorMatchesTwoSum(float $x, float $y): void
    {
        // Given
        $method = new \ReflectionMethod(Reduce::class, 'twoSumError');
        $sum = $x + $y;

        // When
        $old = [$sum, $method->invoke(null, $x, $y, $sum)];
        $new = ExactFloat::twoSum($x, $y);

        // Then
        $this->assertSame(self::bits($old), self::bits($new));
    }

    /**
     * @test         exactProduct(a, b) matches twoProduct(a, b) bit for bit
     * @dataProvider dataProviderForProducts
     * @param        float $a
     * @param        float $b
     */
    public function testExactProductMatchesTwoProduct(float $a, float $b): void
    {
        // Given
        $method = new \ReflectionMethod(Reduce::class, 'exactProduct');

        // When
        $old = $method->invoke(null, $a, $b);
        $new = ExactFloat::twoProduct($a, $b);

        // Then
        $this->assertSame(self::bits($old), self::bits($new));
    }

    /**
     * @test         [a * b, twoProductError(a, b, a * b)] matches twoProduct(a, b) bit for bit
     * @dataProvider dataProviderForProducts
     * @param        float $a
     * @param        float $b
     */
    public function testTwoProductErrorMatchesTwoProduct(float $a, float $b): void
    {
        // Given
        $method = new \ReflectionMethod(Reduce::class, 'twoProductError');
        $product = $a * $b;

        // When
        $old = [$product, $method->invoke(null, $a, $b, $product)];
        $new = ExactFloat::twoProduct($a, $b);

        // Then
        $this->assertSame(self::bits($old), self::bits($new));
    }

    /**
     * @test         toFloatWithResidual(n), its residual cast to float, matches intToFloats(n) bit for bit
     * @dataProvider dataProviderForInts
     * @param        int $n
     */
    public function testToFloatWithResidualMatchesIntToFloats(int $n): void
    {
        // Given
        $method = new \ReflectionMethod(Reduce::class, 'toFloatWithResidual');

        // When
        $result = $method->invoke(null, $n);
        $new = ExactFloat::intToFloats($n);

        // Then
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertIsInt($result[1]);
        $old = [$result[0], (float)$result[1]];
        $this->assertSame(self::bits($old), self::bits($new));
    }

    /**
     * @test         a growExpansion fold from [] over the nonzero terms matches expansion(terms) bit for bit
     * @dataProvider dataProviderForTerms
     * @param        list<float> $terms
     */
    public function testGrowExpansionFoldMatchesExpansion(array $terms): void
    {
        // Given
        $method = new \ReflectionMethod(Reduce::class, 'growExpansion');

        // When
        $old = [];
        foreach ($terms as $term) {
            if ($term !== 0.0) {
                $old = $method->invoke(null, $old, $term);
            }
        }
        $new = ExactFloat::expansion($terms);

        // Then
        $this->assertSame(self::bits($old), self::bits($new));
    }

    /**
     * @test the shared corpus holds the required special values and every provider has rows
     */
    public function testCorpusCoversRequiredValues(): void
    {
        // Given
        $floatBits = \array_map(static fn (float $x): int => Ulp::floatBits($x), self::floatCorpus());
        $required = [
            0.0,
            -0.0,
            2.0 ** -1074,
            -(2.0 ** -1074),
            \PHP_FLOAT_MIN - 2.0 ** -1074,
            \PHP_FLOAT_MIN,
            -\PHP_FLOAT_MIN,
            \PHP_FLOAT_MIN + 2.0 ** -1074,
            1.0,
            2.0 ** 53 - 2.0,
            2.0 ** 53 - 1.0,
            2.0 ** 53,
            2.0 ** 53 + 2.0,
            2.0 ** 53 + 4.0,
            2.0 ** 996,
            -(2.0 ** 996),
        ];
        $products = self::dataProviderForProducts();
        $subnormalFactor = false;
        $nearLimitFactor = false;
        foreach ($products as [$a, $b]) {
            $subnormalFactor = $subnormalFactor || ($a !== 0.0 && \abs($a) < \PHP_FLOAT_MIN && $b !== 0.0);
            $nearLimitFactor = $nearLimitFactor || ($b !== 0.0 && \abs($a) >= 2.0 ** 995);
        }

        // When
        $missing = [];
        foreach ($required as $value) {
            if (!\in_array(Ulp::floatBits($value), $floatBits, true)) {
                $missing[] = \sprintf('%.17g', $value);
            }
        }
        $ints = self::intCorpus();

        // Then
        $this->assertSame([], $missing);
        $this->assertContains(\PHP_INT_MAX, $ints);
        $this->assertContains(\PHP_INT_MIN, $ints);
        $this->assertTrue($subnormalFactor, 'a product row with a subnormal factor');
        $this->assertTrue($nearLimitFactor, 'a product row with a factor near 2^996');
        $this->assertGreaterThanOrEqual(4000, \count(self::dataProviderForPairs()));
        $this->assertGreaterThanOrEqual(800, \count($products));
        $this->assertGreaterThanOrEqual(40, \count(self::dataProviderForInts()));
        $this->assertGreaterThanOrEqual(60, \count(self::dataProviderForTerms()));
    }

    /**
     * Every ordered pair of corpus floats.
     *
     * @return array<string, array{float, float}>
     */
    public static function dataProviderForPairs(): array
    {
        $corpus = self::floatCorpus();
        $pairs = [];
        foreach ($corpus as $i => $x) {
            foreach ($corpus as $j => $y) {
                $pairs[self::label([$i, $j], [$x, $y])] = [$x, $y];
            }
        }

        return $pairs;
    }

    /**
     * The ordered pairs of corpus floats inside the exactness domains of every compared product method: each
     * factor at most 2^996, one integer-valued or a power of two, and a zero factor or a normal product of at
     * most 2^1000.
     *
     * @return array<string, array{float, float}>
     */
    public static function dataProviderForProducts(): array
    {
        $corpus = self::floatCorpus();
        $pairs = [];
        foreach ($corpus as $i => $a) {
            foreach ($corpus as $j => $b) {
                if (\abs($a) > 2.0 ** 996 || \abs($b) > 2.0 ** 996) {
                    continue;
                }
                if (!self::isIntegerOrPowerOfTwo($a) && !self::isIntegerOrPowerOfTwo($b)) {
                    continue;
                }
                $product = \abs($a * $b);
                if ($a !== 0.0 && $b !== 0.0 && ($product < \PHP_FLOAT_MIN || $product > 2.0 ** 1000)) {
                    continue;
                }
                $pairs[self::label([$i, $j], [$a, $b])] = [$a, $b];
            }
        }

        return $pairs;
    }

    /**
     * @return array<string, array{int}>
     */
    public static function dataProviderForInts(): array
    {
        $rows = [];
        foreach (self::intCorpus() as $i => $n) {
            $rows["#$i $n"] = [$n];
        }

        return $rows;
    }

    /**
     * Hand-written term lists, windows of 5 consecutive corpus floats, and seeded lists of 2 to 8 corpus floats.
     *
     * @return array<string, array{list<float>}>
     */
    public static function dataProviderForTerms(): array
    {
        $tiny = 2.0 ** -1074;
        $lists = [
            [],
            [0.0],
            [-0.0],
            [0.0, -0.0],
            [-0.0, 0.0],
            [1.0],
            [1.0, 1.0],
            [1.0, -1.0],
            [-1.0, 1.0],
            [1.0, 2.0 ** -53],
            [2.0 ** -53, 1.0],
            [1.0, 2.0 ** -53, -1.0],
            [1.0, 2.0 ** -53, 2.0 ** -53],
            [2.0 ** 53, 1.0, -(2.0 ** 53)],
            [2.0 ** 53, 1.0, 1.0, -(2.0 ** 53)],
            [0.1, 0.2, 0.3, -0.6],
            [1.0 / 3.0, -0.1, 1e-300, -12345.678],
            [\PHP_FLOAT_MIN, -(\PHP_FLOAT_MIN - $tiny)],
            [\PHP_FLOAT_MIN - $tiny, $tiny, -\PHP_FLOAT_MIN],
            [$tiny, -$tiny],
            [2.0 ** 996, -(2.0 ** 996), $tiny],
            [2.0 ** 996, $tiny, -(2.0 ** 996)],
            [2.0 ** 996, 2.0 ** 944, -(2.0 ** 996), 1.0],
            [1.0, 0.0, 2.0 ** -60, -0.0, -1.0],
            [0.0, 1e300, -0.0, -1e300, 0.0],
        ];
        $rows = [];
        foreach ($lists as $i => $terms) {
            $rows[self::label(['hand', $i], $terms)] = [$terms];
        }

        $corpus = self::floatCorpus();
        $count = \count($corpus);
        for ($i = 0; $i + 5 <= $count; ++$i) {
            $terms = \array_slice($corpus, $i, 5);
            $rows[self::label(['window', $i], $terms)] = [$terms];
        }

        $randomizer = new \Random\Randomizer(new \Random\Engine\Mt19937(self::SEED + 1));
        for ($i = 0; $i < 30; ++$i) {
            $terms = [];
            $length = $randomizer->getInt(2, 8);
            for ($k = 0; $k < $length; ++$k) {
                $terms[] = $corpus[$randomizer->getInt(0, $count - 1)];
            }
            $rows[self::label(['seeded', $i], $terms)] = [$terms];
        }

        return $rows;
    }

    /**
     * Finite floats below 2^997 in magnitude: signed zeros, subnormals and the normal boundary, powers of two,
     * 2^53 ± k, values near 2^996, mixed signs and magnitudes, and fixed-seed pseudo-random values.
     *
     * @return list<float>
     */
    private static function floatCorpus(): array
    {
        $tiny = 2.0 ** -1074;
        $corpus = [
            0.0,
            -0.0,
            $tiny,
            -$tiny,
            3.0 * $tiny,
            \PHP_FLOAT_MIN - $tiny,
            \PHP_FLOAT_MIN,
            -\PHP_FLOAT_MIN,
            \PHP_FLOAT_MIN + $tiny,
            2.0 ** -1060,
            2.0 ** -53,
            2.0 ** -52,
            0.1,
            -0.1,
            1.0 / 3.0,
            0.5,
            1.0 - 2.0 ** -53,
            1.0,
            -1.0,
            1.0 + 2.0 ** -52,
            2.0,
            3.0,
            -7.0,
            1e-300,
            -12345.678,
            2.0 ** 52,
            2.0 ** 53 - 2.0,
            2.0 ** 53 - 1.0,
            2.0 ** 53,
            -(2.0 ** 53),
            2.0 ** 53 + 2.0,
            2.0 ** 53 + 4.0,
            -(2.0 ** 53 + 2.0),
            2.0 ** 63,
            -(2.0 ** 64),
            1e300,
            -1e300,
            2.0 ** 990,
            2.0 ** 996 - 2.0 ** 943,
            2.0 ** 996,
            -(2.0 ** 996),
            2.0 ** 996 + 2.0 ** 944,
            3.0 * 2.0 ** 994,
        ];

        $randomizer = new \Random\Randomizer(new \Random\Engine\Mt19937(self::SEED));
        // Wide: a random 53-bit significand at a random exponent, subnormals included.
        for ($i = 0; $i < 10; ++$i) {
            $corpus[] = self::randomSign($randomizer) * self::randomSignificand($randomizer)
                * 2.0 ** $randomizer->getInt(-1074, 995);
        }
        // Narrow: close magnitudes, so that sums of pairs round and cancel.
        for ($i = 0; $i < 10; ++$i) {
            $corpus[] = self::randomSign($randomizer) * self::randomSignificand($randomizer)
                * 2.0 ** $randomizer->getInt(-4, 4);
        }
        // Integer-valued.
        for ($i = 0; $i < 6; ++$i) {
            $corpus[] = (float)$randomizer->getInt(-(1 << 30), 1 << 30);
        }

        return $corpus;
    }

    /**
     * Ints around 2^53, 2^54 (ties to even) and the int limits (ties that round to ±2^63), and seeded ints.
     *
     * @return list<int>
     */
    private static function intCorpus(): array
    {
        $corpus = [
            0,
            1,
            -1,
            2147483647,
            4294967297,
            9007199254740991,
            9007199254740992,
            9007199254740993,
            9007199254740995,
            -9007199254740993,
            -9007199254740995,
            18014398509481985,
            18014398509481986,
            18014398509481990,
            4611686018427387905,
            \PHP_INT_MAX,
            \PHP_INT_MAX - 1,
            \PHP_INT_MAX - 511,
            \PHP_INT_MAX - 512,
            \PHP_INT_MAX - 1023,
            -\PHP_INT_MAX,
            \PHP_INT_MIN,
            \PHP_INT_MIN + 1,
            \PHP_INT_MIN + 512,
            \PHP_INT_MIN + 513,
        ];

        $randomizer = new \Random\Randomizer(new \Random\Engine\Mt19937(self::SEED + 2));
        for ($i = 0; $i < 16; ++$i) {
            $corpus[] = $randomizer->getInt(\PHP_INT_MIN, \PHP_INT_MAX);
        }
        for ($i = 0; $i < 8; ++$i) {
            $corpus[] = $randomizer->getInt(-(\PHP_INT_MAX >> 8), \PHP_INT_MAX >> 8);
        }

        return $corpus;
    }

    /**
     * A significand in [1, 2) with 52 random fraction bits, built from 25- and 27-bit draws.
     */
    private static function randomSignificand(\Random\Randomizer $randomizer): float
    {
        $fraction = $randomizer->getInt(0, (1 << 25) - 1) * 2.0 ** 27 + $randomizer->getInt(0, (1 << 27) - 1);

        return 1.0 + $fraction * 2.0 ** -52;
    }

    private static function randomSign(\Random\Randomizer $randomizer): float
    {
        return $randomizer->getInt(0, 1) === 0 ? 1.0 : -1.0;
    }

    /**
     * Whether $x is integer-valued (zero included) or ± a power of two (subnormal ones included).
     */
    private static function isIntegerOrPowerOfTwo(float $x): bool
    {
        if (\floor($x) === $x) {
            return true;
        }
        $m = \abs($x);
        while ($m >= 2.0) {
            $m /= 2.0;
        }
        while ($m < 1.0) {
            $m *= 2.0;
        }

        return $m === 1.0;
    }

    /**
     * @param list<int|string> $indices
     * @param list<float>      $values
     */
    private static function label(array $indices, array $values): string
    {
        $formatted = \array_map(static fn (float $x): string => \sprintf('%.17g', $x), $values);

        return '#' . \implode('/', $indices) . ' [' . \implode(', ', $formatted) . ']';
    }

    /**
     * Each element's bit pattern, keys kept, so assertSame compares length, keys, order and sign of zero.
     *
     * @param mixed $floats
     *
     * @return array<int>
     */
    private static function bits(mixed $floats): array
    {
        self::assertIsArray($floats);

        return \array_map(static fn (float $x): int => Ulp::floatBits($x), $floats);
    }
}
