<?php

declare(strict_types=1);

namespace IterTools\Tests\Util\Precision;

use IterTools\Util\Precision\ExactFloat;
use IterTools\Util\Precision\Ulp;

/**
 * Consistency test, not an oracle test: ExactFloat::expansion and a fold of ExactFloat::growExpansion from []
 * over the same nonzero terms run the same TwoSum operations in the same order, so their results must be
 * identical bit for bit (so -0.0 and 0.0 differ, and the result arrays must match in length, keys and order).
 *
 * The inputs come from a corpus of specials and fixed-seed pseudo-random values, kept inside both methods'
 * documented preconditions: finite floats, never NAN or INF, with |x| < 2^997, and at most 8 terms per list,
 * so neither an exact sum nor a partial sum overflows. A fold from [] gives growExpansion the nonoverlapping
 * expansion in increasing magnitude it needs, and skips both signed zeros, as expansion does.
 *
 * Bit patterns come from Ulp::floatBits, so the test runs on 64-bit PHP only.
 */
class ExactFloatTest extends \PHPUnit\Framework\TestCase
{
    private const SEED = 20261114;

    protected function setUp(): void
    {
        if (\PHP_INT_SIZE !== 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
    }

    /**
     * @test         a growExpansion fold from [] over the nonzero terms matches expansion(terms) bit for bit
     * @dataProvider dataProviderForTerms
     * @param        list<float> $terms
     */
    public function testGrowExpansionFoldMatchesExpansion(array $terms): void
    {
        // When
        $folded = [];
        foreach ($terms as $term) {
            if ($term !== 0.0) {
                $folded = ExactFloat::growExpansion($folded, $term);
            }
        }
        $expansion = ExactFloat::expansion($terms);

        // Then
        $this->assertSame(self::bits($expansion), self::bits($folded));
    }

    /**
     * @test the corpus holds the required special values and the term provider has enough rows
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

        // When
        $missing = [];
        foreach ($required as $value) {
            if (!\in_array(Ulp::floatBits($value), $floatBits, true)) {
                $missing[] = \sprintf('%.17g', $value);
            }
        }

        // Then
        $this->assertSame([], $missing);
        $this->assertGreaterThanOrEqual(60, \count(self::dataProviderForTerms()));
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
     * @param list<float> $floats
     *
     * @return array<int>
     */
    private static function bits(array $floats): array
    {
        return \array_map(static fn (float $x): int => Ulp::floatBits($x), $floats);
    }
}
