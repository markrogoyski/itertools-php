<?php

declare(strict_types=1);

namespace IterTools\Tests\Util\Precision;

use IterTools\Util\Precision\ExactFloat;
use IterTools\Util\Precision\Ulp;

/**
 * ExactFloat against Python oracles (fractions.Fraction, math.fsum and int arithmetic), with every row inside
 * the documented preconditions of the method it tests, plus a consistency test of expansion and growExpansion.
 */
class ExactFloatTest extends \PHPUnit\Framework\TestCase
{
    private const SEED = 20261114;

    /**
     * @test         twoSum returns the float sum and its exact rounding error
     * @dataProvider dataProviderForTwoSum
     * @param        float $a
     * @param        float $b
     * @param        float $sum
     * @param        float $error
     */
    public function testTwoSumIsExactSumAndError(float $a, float $b, float $sum, float $error): void
    {
        // When
        $result = ExactFloat::twoSum($a, $b);

        // Then
        $this->assertSame([$sum, $error], $result);
        $this->assertSame(\fdiv(1.0, $sum), \fdiv(1.0, $result[0]));
    }

    /**
     * Test data generated with Python floats (IEEE binary64, round to nearest) and fractions.Fraction:
     *   hi = a + b
     *   lo = float(Fraction(a) + Fraction(b) - Fraction(hi))
     * keeping only finite a and b whose float sum a + b is finite. A zero lo has no sign in Fraction, so the
     * test checks the sign of a zero hi only.
     *
     * @return array<string, array{float, float, float, float}>
     */
    public static function dataProviderForTwoSum(): array
    {
        return [
            '+0 + +0' => [0.0, 0.0, 0.0, 0.0],
            '+0 + -0' => [0.0, -0.0, 0.0, 0.0],
            '-0 + +0' => [-0.0, 0.0, 0.0, 0.0],
            '-0 + -0' => [-0.0, -0.0, -0.0, 0.0],
            '1 + -1 cancels' => [1.0, -1.0, 0.0, 0.0],
            '-1 + 1 cancels' => [-1.0, 1.0, 0.0, 0.0],
            'x + -x cancels at 2^996' => [6.696928794914171e+299, -6.696928794914171e+299, 0.0, 0.0],
            '1 + 2^-53 ties to 1' => [1.0, 1.1102230246251565e-16, 1.0, 1.1102230246251565e-16],
            '1 + 3 * 2^-54 rounds up' => [1.0, 1.6653345369377348e-16, 1.0000000000000002, -5.551115123125783e-17],
            '2^-53 + 1, smaller first' => [1.1102230246251565e-16, 1.0, 1.0, 1.1102230246251565e-16],
            '2^53 + 1 ties to 2^53' => [9007199254740992.0, 1.0, 9007199254740992.0, 1.0],
            '2^53 + 2 plus 1 ties to 2^53 + 4' => [9007199254740994.0, 1.0, 9007199254740996.0, -1.0],
            '2^53 - 1 plus 0.5 ties to 2^53' => [9007199254740991.0, 0.5, 9007199254740992.0, -0.5],
            '-(2^53 + 2) + -1' => [-9007199254740994.0, -1.0, -9007199254740996.0, 1.0],
            '0.1 + 0.2' => [0.1, 0.2, 0.30000000000000004, -2.7755575615628914e-17],
            '1/3 + -0.1' => [0.3333333333333333, -0.1, 0.2333333333333333, 0.0],
            '1e300 + -1e-300' => [1e+300, -1e-300, 1e+300, -1e-300],
            '2^-1074 + 2^-1074' => [5e-324, 5e-324, 1e-323, 0.0],
            'PHP_FLOAT_MIN + -2^-1074' => [2.2250738585072014e-308, -5e-324, 2.225073858507201e-308, 0.0],
            '-PHP_FLOAT_MIN + (PHP_FLOAT_MIN - 2^-1074)' => [-2.2250738585072014e-308, 2.225073858507201e-308, -5e-324, 0.0],
            '2^63 + 1' => [9.223372036854776e+18, 1.0, 9.223372036854776e+18, 1.0],
            '-(2^64) + 3' => [-1.8446744073709552e+19, 3.0, -1.8446744073709552e+19, 3.0],
            '2^996 + 2^943 ties to 2^996' => [6.696928794914171e+299, 7.435084542388915e+283, 6.696928794914171e+299, 7.435084542388915e+283],
            '2^996 + 3 * 2^943 rounds up' => [6.696928794914171e+299, 2.2305253627166746e+284, 6.696928794914174e+299, -7.435084542388915e+283],
            'PHP_FLOAT_MAX + -PHP_FLOAT_MAX' => [1.7976931348623157e+308, -1.7976931348623157e+308, 0.0, 0.0],
            'PHP_FLOAT_MAX + -1' => [1.7976931348623157e+308, -1.0, 1.7976931348623157e+308, -1.0],
            '-PHP_FLOAT_MAX + 1' => [-1.7976931348623157e+308, 1.0, -1.7976931348623157e+308, 1.0],
            '1 + PHP_FLOAT_MAX' => [1.0, 1.7976931348623157e+308, 1.7976931348623157e+308, 1.0],
            'seeded close 0' => [-19.854508423983408, -23.749602195840467, -43.60411061982387, -3.552713678800501e-15],
            'seeded close 1' => [0.09357429319397867, 0.9223030964195628, 1.0158773896135413, 8.326672684688674e-17],
            'seeded close 2' => [0.43502234727412786, 22.730878430325998, 23.165900777600125, 1.6653345369377348e-16],
            'seeded close 3' => [1.69605769831727, -0.3637705418759474, 1.3322871564413226, 0.0],
            'seeded close 4' => [-0.7613343895321346, 0.9224693142841095, 0.16113492475197488, 0.0],
            'seeded close 5' => [-1.4808797569677326, -0.2167222457448545, -1.697602002712587, 5.551115123125783e-17],
            'seeded close 6' => [-3.967413300602617, -5.8295180413609735, -9.79693134196359, 0.0],
            'seeded close 7' => [-0.4074527953247643, 0.9270973558961123, 0.519644560571348, 0.0],
            'seeded wide 0' => [4.120991422132361e+23, -1.2281092081449503e-95, 4.120991422132361e+23, -1.2281092081449503e-95],
            'seeded wide 1' => [-2.1060204026968328e-215, 3.3186295807363415e-128, 3.3186295807363415e-128, -2.1060204026968328e-215],
            'seeded wide 2' => [7.206080270198308e+151, 1.2762043500478908e-162, 7.206080270198308e+151, 1.2762043500478908e-162],
            'seeded wide 3' => [2.641068949941252e-41, 7.509580267572307e-82, 2.641068949941252e-41, 7.509580267572307e-82],
            'seeded wide 4' => [5.6604039491604184e+222, -6.650876478465889e-84, 5.6604039491604184e+222, -6.650876478465889e-84],
            'seeded wide 5' => [-7.235230300228584e+182, 0.15068995443214986, -7.235230300228584e+182, 0.15068995443214986],
            'seeded wide 6' => [1.28852137710431e-103, -4.1001600412201e+193, -4.1001600412201e+193, 1.28852137710431e-103],
            'seeded wide 7' => [-1.1495046781575357e+160, 2.40166724285711e+171, 2.401667242845615e+171, 5.690031334848592e+154],
        ];
    }

    /**
     * @test         twoProduct returns the float product and its exact rounding error
     * @dataProvider dataProviderForTwoProduct
     * @param        float $a
     * @param        float $b
     * @param        float $product
     * @param        float $error
     */
    public function testTwoProductIsExactProductAndError(float $a, float $b, float $product, float $error): void
    {
        // When
        $result = ExactFloat::twoProduct($a, $b);

        // Then
        $this->assertSame([$product, $error], $result);
        $this->assertSame(\fdiv(1.0, $product), \fdiv(1.0, $result[0]));
    }

    /**
     * Test data generated with Python floats (IEEE binary64, round to nearest) and fractions.Fraction:
     *   hi = a * b
     *   lo = float(Fraction(a) * Fraction(b) - Fraction(hi))
     * keeping only finite a and b with |a|, |b| <= 2^996, a finite a * b and |Fraction(a) * Fraction(b)| <=
     * 2^1020, and an exact product that is 0, at least 2^-968 in magnitude, subnormal with an integer-valued
     * factor, or normal with a power-of-two factor; each row also has Fraction(hi) + Fraction(lo) equal to the
     * exact product. A zero lo has no sign in Fraction, so the test checks the sign of a zero hi only.
     *
     * @return array<string, array{float, float, float, float}>
     */
    public static function dataProviderForTwoProduct(): array
    {
        return [
            '+0 * +0' => [0.0, 0.0, 0.0, 0.0],
            '-0 * 3' => [-0.0, 3.0, -0.0, 0.0],
            '+0 * -0' => [0.0, -0.0, -0.0, 0.0],
            '-0 * -0' => [-0.0, -0.0, 0.0, 0.0],
            '1 * 1' => [1.0, 1.0, 1.0, 0.0],
            '-1.5 * 2' => [-1.5, 2.0, -3.0, 0.0],
            '(1 + 2^-52) squared' => [1.0000000000000002, 1.0000000000000002, 1.0000000000000004, 4.930380657631324e-32],
            '(1 + 2^-52) * -(1 - 2^-53)' => [1.0000000000000002, -0.9999999999999999, -1.0, -1.1102230246251563e-16],
            '0.1 * 0.1' => [0.1, 0.1, 0.010000000000000002, -8.326672684688674e-19],
            '1/3 * 3' => [0.3333333333333333, 3.0, 1.0, -5.551115123125783e-17],
            '(2^53 - 1) squared' => [9007199254740991.0, 9007199254740991.0, 8.112963841460666e+31, 1.0],
            '(2^53 + 2) * (2^53 - 2)' => [9007199254740994.0, 9007199254740990.0, 8.112963841460668e+31, -4.0],
            '134217729 squared' => [134217729.0, 134217729.0, 1.801439877791744e+16, 1.0],
            '2^996 * 1.5' => [6.696928794914171e+299, 1.5, 1.0045393192371256e+300, 0.0],
            '2^996 * -1.5' => [6.696928794914171e+299, -1.5, -1.0045393192371256e+300, 0.0],
            '(2^996 - 2^943) * 3' => [6.69692879491417e+299, 3.0, 2.009078638474251e+300, 7.435084542388915e+283],
            '2^996 * 2^20' => [6.696928794914171e+299, 1048576.0, 7.022238808055922e+305, 0.0],
            '1e153 * 1.1e154, near 2^1020' => [1e+153, 1.1e+154, 1.1000000000000001e+307, -4.1665482512636546e+290],
            '1e-145 * 1e-145, near 2^-968' => [1e-145, 1e-145, 9.999999999999998e-291, 4.550249051668567e-307],
            '3 * 5 * 2^-1074, an integer factor and a subnormal product' => [3.0, 2.5e-323, 7.4e-323, 0.0],
            '7 * -2^-1074' => [7.0, -5e-324, -3.5e-323, 0.0],
            '(PHP_FLOAT_MIN / 4) * 3, subnormal times an integer' => [5.562684646268003e-309, 3.0, 1.668805393880401e-308, 0.0],
            '2^-1000 * 1.1, a power of two and a normal product' => [9.332636185032189e-302, 1.1, 1.0265899803535408e-301, 0.0],
            '2^-1074 * 2^60, a power of two and a normal product' => [5e-324, 1.152921504606847e+18, 5.696189077778436e-306, 0.0],
            '-2^-1074 * (1 + 2^-52) * 2^52' => [-5e-324, 4503599627370497.0, -2.225073858507202e-308, 0.0],
            'seeded close 0' => [1.3703646835623167, 12.531216488298012, 17.17233651763739, 4.849211562389906e-16],
            'seeded close 1' => [-0.8372080793955422, 7.298690903639521, -6.110522993537758, 4.3252127108960815e-16],
            'seeded close 2' => [-7.549595607603324, 7.850592858859027, -59.26880136432413, -2.0488086627816037e-15],
            'seeded close 3' => [31.394377217364816, 3.6313353254604883, 114.0035110102488, 4.417464116004917e-15],
            'seeded close 4' => [0.11036385132183418, -0.4925530923004479, -0.0543600562467563, -3.2527945125557125e-18],
            'seeded close 5' => [0.17909186349479114, -9.885137325234544, -1.7703476644781697, 3.721448552415611e-19],
            'seeded close 6' => [-0.7593723042041541, -1.507859391050185, 1.1450266601976518, -9.335913902656148e-17],
            'seeded close 7' => [1.9768569342926647, -0.24022972480215798, -0.47489979729836457, 8.859156110759457e-18],
            'seeded wide 0' => [1.816361958610511e-136, 1.6496718530232174e-08, 2.996401198021882e-144, 1.567005089193846e-160],
            'seeded wide 1' => [-1.0595978552003132e-127, -2.358574266307425e+121, 2.4991402339099998e-06, 1.1893878369906186e-22],
            'seeded wide 2' => [-1.489839359249915e+104, -925634.5512564887, 1.3790467867435495e+110, 4.4283001974760465e+93],
            'seeded wide 3' => [3.0534458030460626e+49, -8.000306988044976e+26, -2.4428503795726018e+76, 1.5793262243212206e+60],
            'seeded wide 4' => [5.901198039078332e-84, -6.162055876237519e-30, -3.636351205354396e-113, -2.2238992089788023e-129],
            'seeded wide 5' => [-1.1525064410280116e-51, 8.300357694396728e+58, -95662157.05628645, 2.3314486815064222e-09],
            'seeded wide 6' => [13782832828460.895, 9.480987974321968e+90, 1.3067487229872778e+104, -7.486471618761788e+87],
            'seeded wide 7' => [-8.652523984382259e-89, 4.5796472998839126e+78, -3.9625508102257e-10, -2.5031918509300501e-26],
        ];
    }

    /**
     * @test         expansion of the terms minus their exact sum, as f + r, has sign 0
     * @dataProvider dataProviderForExactSums
     * @param        list<float> $terms
     * @param        float       $sum
     * @param        float       $rest
     * @param        int         $sign
     */
    public function testExpansionCancelsExactSum(array $terms, float $sum, float $rest, int $sign): void
    {
        // When
        $expansion = ExactFloat::expansion([...$terms, -$sum, -$rest]);

        // Then
        $this->assertSame(0, ExactFloat::expansionSign($expansion));
    }

    /**
     * @test         the sign of expansion(terms) is the sign of the exact sum of the terms
     * @dataProvider dataProviderForExactSums
     * @param        list<float> $terms
     * @param        float       $sum
     * @param        float       $rest
     * @param        int         $sign
     */
    public function testExpansionSignIsSignOfExactSum(array $terms, float $sum, float $rest, int $sign): void
    {
        // When
        $expansion = ExactFloat::expansion($terms);

        // Then
        $this->assertSame($sign, ExactFloat::expansionSign($expansion));
    }

    /**
     * @test         expansion has nonzero components in increasing magnitude that do not overlap
     * @dataProvider dataProviderForExactSums
     * @param        list<float> $terms
     * @param        float       $sum
     * @param        float       $rest
     * @param        int         $sign
     */
    public function testExpansionComponentsIncreaseAndDoNotOverlap(
        array $terms,
        float $sum,
        float $rest,
        int $sign
    ): void {
        // When
        $expansion = ExactFloat::expansion($terms);
        $cancelled = ExactFloat::expansion([...$terms, -$sum, -$rest]);

        // Then
        $this->assertSame([], self::orderViolations($expansion));
        $this->assertSame([], self::orderViolations($cancelled));
    }

    /**
     * @test         a growExpansion fold of the terms minus their exact sum, as f + r, has sign 0
     * @dataProvider dataProviderForExactSums
     * @param        list<float> $terms
     * @param        float       $sum
     * @param        float       $rest
     * @param        int         $sign
     */
    public function testFoldedGrowExpansionCancelsExactSum(array $terms, float $sum, float $rest, int $sign): void
    {
        // When
        $folded = self::growExpansionFold([...$terms, -$sum, -$rest]);

        // Then
        $this->assertSame(0, ExactFloat::expansionSign($folded));
    }

    /**
     * @test         the sign of a growExpansion fold of the terms is the sign of their exact sum
     * @dataProvider dataProviderForExactSums
     * @param        list<float> $terms
     * @param        float       $sum
     * @param        float       $rest
     * @param        int         $sign
     */
    public function testFoldedGrowExpansionSignIsSignOfExactSum(
        array $terms,
        float $sum,
        float $rest,
        int $sign
    ): void {
        // When
        $folded = self::growExpansionFold($terms);

        // Then
        $this->assertSame($sign, ExactFloat::expansionSign($folded));
    }

    /**
     * @test         a growExpansion fold has nonzero components in increasing magnitude that do not overlap
     * @dataProvider dataProviderForExactSums
     * @param        list<float> $terms
     * @param        float       $sum
     * @param        float       $rest
     * @param        int         $sign
     */
    public function testFoldedGrowExpansionComponentsIncreaseAndDoNotOverlap(
        array $terms,
        float $sum,
        float $rest,
        int $sign
    ): void {
        // When
        $folded = self::growExpansionFold($terms);
        $cancelled = self::growExpansionFold([...$terms, -$sum, -$rest]);

        // Then
        $this->assertSame([], self::orderViolations($folded));
        $this->assertSame([], self::orderViolations($cancelled));
    }

    /**
     * Test data generated with Python fractions.Fraction and math.fsum:
     *   exact = sum(map(Fraction, terms))
     *   f = math.fsum(terms)
     *   r = float(exact - Fraction(f))
     *   sign = (exact > 0) - (exact < 0)
     * keeping only rows with Fraction(f) + Fraction(r) == exact, finite terms, and sum(abs(t) for t in terms)
     * + abs(f) + abs(r) <= 2^1020, so that no partial sum of the terms, or of the terms with -f and -r, overflows.
     *
     * @return array<string, array{list<float>, float, float, int}>
     */
    public static function dataProviderForExactSums(): array
    {
        return [
            'no terms' => [[], 0.0, 0.0, 0],
            '+0' => [[0.0], 0.0, 0.0, 0],
            '-0' => [[-0.0], 0.0, 0.0, 0],
            '+0 and -0' => [[0.0, -0.0], 0.0, 0.0, 0],
            'one' => [[1.0], 1.0, 0.0, 1],
            '-2^-1074' => [[-5e-324], -5e-324, 0.0, -1],
            '1 + 1' => [[1.0, 1.0], 2.0, 0.0, 1],
            '1 - 1 cancels' => [[1.0, -1.0], 0.0, 0.0, 0],
            '1 + 2^-53, a tie' => [[1.0, 1.1102230246251565e-16], 1.0, 1.1102230246251565e-16, 1],
            '2^-53 + 1, smaller first' => [[1.1102230246251565e-16, 1.0], 1.0, 1.1102230246251565e-16, 1],
            '1 + 2^-53 - 1' => [[1.0, 1.1102230246251565e-16, -1.0], 1.1102230246251565e-16, 0.0, 1],
            '1 + 2^-53 + 2^-53' => [[1.0, 1.1102230246251565e-16, 1.1102230246251565e-16], 1.0000000000000002, 0.0, 1],
            '2^53 + 1 - 2^53' => [[9007199254740992.0, 1.0, -9007199254740992.0], 1.0, 0.0, 1],
            '2^53 + 1 + 1' => [[9007199254740992.0, 1.0, 1.0], 9007199254740994.0, 0.0, 1],
            '2^53 + 2 plus 1' => [[9007199254740994.0, 1.0], 9007199254740996.0, -1.0, 1],
            '-(2^53) - 1' => [[-9007199254740992.0, -1.0], -9007199254740992.0, -1.0, -1],
            '(2^53 - 1) + 0.5 + 0.25' => [[9007199254740991.0, 0.5, 0.25], 9007199254740992.0, -0.25, 1],
            '0.1 + 0.2 + 0.3 - 0.6' => [[0.1, 0.2, 0.3, -0.6], 2.7755575615628914e-17, 0.0, 1],
            '0.1 ten times' => [[0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1], 1.0, 5.551115123125783e-17, 1],
            '1/3 three times' => [[0.3333333333333333, 0.3333333333333333, 0.3333333333333333], 1.0, -5.551115123125783e-17, 1],
            '1e300 - 1e300 + 1e-300' => [[1e+300, -1e+300, 1e-300], 1e-300, 0.0, 1],
            '2^-1074 - 2^-1074 cancels' => [[5e-324, -5e-324], 0.0, 0.0, 0],
            'PHP_FLOAT_MIN - (PHP_FLOAT_MIN - 2^-1074)' => [[2.2250738585072014e-308, -2.225073858507201e-308], 5e-324, 0.0, 1],
            '(PHP_FLOAT_MIN - 2^-1074) + 2^-1074 - PHP_FLOAT_MIN' => [[2.225073858507201e-308, 5e-324, -2.2250738585072014e-308], 0.0, 0.0, 0],
            '2^996 + 2^-1074' => [[6.696928794914171e+299, 5e-324], 6.696928794914171e+299, 5e-324, 1],
            '2^996 - 2^996 + 2^-1074' => [[6.696928794914171e+299, -6.696928794914171e+299, 5e-324], 5e-324, 0.0, 1],
            '2^996 + 2^943, a tie' => [[6.696928794914171e+299, 7.435084542388915e+283], 6.696928794914171e+299, 7.435084542388915e+283, 1],
            '2^996 + 2^944 - 2^996 + 1' => [[6.696928794914171e+299, 1.487016908477783e+284, -6.696928794914171e+299, 1.0], 1.487016908477783e+284, 1.0, 1],
            '-(2^996) - 1.5' => [[-6.696928794914171e+299, -1.5], -6.696928794914171e+299, -1.5, -1],
            '1 + 0 + 2^-60 - 0 - 1' => [[1.0, 0.0, 8.673617379884035e-19, -0.0, -1.0], 8.673617379884035e-19, 0.0, 1],
            '2^63 - 2^64 + 2^63 cancels' => [[9.223372036854776e+18, -1.8446744073709552e+19, 9.223372036854776e+18], 0.0, 0.0, 0],
            'seeded close 0' => [[-0.10218489351817216, 1.2046177398572073, -5.090947162719217, -0.10419492769038426, 0.09883994872207955], -3.9938692953484862, 2.7755575615628914e-17, -1],
            'seeded close 1' => [[1.274628077271464, -6.5027862773283465, -0.48019084289185465, -23.72217261333605], -29.430521656284785, -8.881784197001252e-16, -1],
            'seeded close 2' => [[-0.39294991339757374, 5.694417797173856, -0.9392767775641965, 0.4189941453059302, -0.10598357776367068], 4.675201673754346, -3.885780586188048e-16, 1],
            'seeded close 3' => [[8.586689504005147, 2.1439179752750843], 10.73060747928023, 4.440892098500626e-16, 1],
            'seeded close 4' => [[0.8710146076189336, -0.5624625598034629, -1.1217091647369108, -0.31543523621072483], -1.128592353132165, 5.551115123125783e-17, -1],
            'seeded close 5' => [[8.770660911830413, -4.0008510990927, -2.5252146613594744, 8.542608674545848, -1.388050779061281], 9.399153046862805, 4.440892098500626e-16, 1],
            'seeded close 6' => [[12.346648569224383, -0.07838196639801075, -0.1399833236035508, -1.0631711628725007, -6.038589752287341], 5.026522364062981, -4.3021142204224816e-16, 1],
            'seeded close 7' => [[0.21908699084349773, 3.7909305717221784, -6.135062712705656], -2.1250451501399796, -8.326672684688674e-17, -1],
            'seeded close 8' => [[0.16024254233454857, 0.16324725739550244, 8.997596158229186, 7.0106155322682095, -2.911190905813393], 13.420510584414053, 2.7755575615628914e-17, 1],
            'seeded close 9' => [[-0.9542835990936233, -2.3050927866153983, -0.41076799746328113, 3.0347313800237536], -0.635413003148549, -5.551115123125783e-17, -1],
            'seeded wide pair 0' => [[-9.905200478748545e+98, -3.702864165522221e+30], -9.905200478748545e+98, -3.702864165522221e+30, -1],
            'seeded wide pair 1' => [[2.610704617205739e+96, -5.690525008621974e+194], -5.690525008621974e+194, 2.610704617205739e+96, -1],
            'seeded wide pair 2' => [[-2.045893106763021e-186, 1.371730506891432e-243], -2.045893106763021e-186, 1.371730506891432e-243, -1],
            'seeded wide pair 3' => [[5.0523799391501535e+166, -2.1960110632220975e-215], 5.0523799391501535e+166, -2.1960110632220975e-215, 1],
            'seeded wide pair 4' => [[66.52458691585545, -1.5227181034086846e-76], 66.52458691585545, -1.5227181034086846e-76, 1],
            'seeded wide pair 5' => [[-6.209384318382265e+85, 1.5791835755544882e+187], 1.5791835755544882e+187, -6.209384318382265e+85, 1],
            'seeded wide pair 6' => [[2.816616950165475e-135, 1.314211552253883e+212], 1.314211552253883e+212, 2.816616950165475e-135, 1],
            'seeded wide pair 7' => [[2.7205506048040665e+246, 1.2737444296778453e+229], 2.7205506048040665e+246, 1.2737444296778453e+229, 1],
            'seeded cancel 0' => [[4.252585761099679e-06, 1.1639728288059607e-13, -4.252585761099679e-06], 1.1639728288059607e-13, 0.0, 1],
            'seeded cancel 1' => [[-290905.03348105145, -2.802072219600223e-23, 290905.03348105145], -2.802072219600223e-23, 0.0, -1],
            'seeded cancel 2' => [[31.564125133092134, 4.5577359401735556e-17, -31.564125133092134], 4.5577359401735556e-17, 0.0, 1],
            'seeded cancel 3' => [[5105.601036207333, -3.6407309112586815e-18, -5105.601036207333], -3.6407309112586815e-18, 0.0, -1],
            'seeded cancel 4' => [[-3165.4633934915037, 2.684225220249848e-13, 3165.4633934915037], 2.684225220249848e-13, 0.0, 1],
            'seeded cancel 5' => [[-2565138.7225928507, -1.2642694347664932e-14, 2565138.7225928507], -1.2642694347664932e-14, 0.0, -1],
            'seeded wide 0' => [[2220578748829327.8, -1.6124721783448481e-12, -2.5535480996734202e-12], 2220578748829327.8, -4.166020278018268e-12, 1],
            'seeded wide 2' => [[-44582800235607.445, -2.7199150087673374e-13, 26293441009719.64], -18289359225887.805, -2.7199150087673374e-13, -1],
            'seeded wide 3' => [[334.24871024037617, 5.525497839515419e-14, 1.3174489730434495e-14], 334.2487102403762, 1.158604926478067e-14, 1],
            'seeded wide 5' => [[214389648.55560353, -17871317.58980495, 1.6574912519660928e+18], 1.6574912521626112e+18, -69.03420141711831, 1],
        ];
    }

    /**
     * @test         roundsTo answers whether x plus the offset rounds to x, to nearest with ties to even
     * @dataProvider dataProviderForRoundsTo
     * @param        float       $x
     * @param        list<float> $offset
     * @param        bool        $expected
     */
    public function testRoundsToMatchesCorrectRounding(float $x, array $offset, bool $expected): void
    {
        // When
        $result = ExactFloat::roundsTo($x, $offset);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python fractions.Fraction (float() of a Fraction is correctly rounded):
     *   float(Fraction(x) + sum(map(Fraction, offset))) == x
     * keeping only finite x with |x| <= 2^1000, and offsets in the form expansion() builds: nonzero finite
     * components in increasing magnitude with 2 * abs(c[i]) below the lowest set bit of c[i + 1], and a sum of
     * magnitudes at most 2^1000.
     *
     * @return array<string, array{float, list<float>, bool}>
     */
    public static function dataProviderForRoundsTo(): array
    {
        return [
            '1, no offset' => [1.0, [], true],
            '+0, no offset' => [0.0, [], true],
            '-0, no offset' => [-0.0, [], true],
            '-0 + 2^-1074' => [-0.0, [5e-324], false],
            '+0 - 2^-1074' => [0.0, [-5e-324], false],
            '1 + 2^-53 ties to even 1' => [1.0, [1.1102230246251565e-16], true],
            '1 + 2^-53 + 2^-106 passes the tie' => [1.0, [1.232595164407831e-32, 1.1102230246251565e-16], false],
            '1 + 2^-53 - 2^-106 stays below the tie' => [1.0, [-1.232595164407831e-32, 1.1102230246251565e-16], true],
            '1 - 2^-54 ties to even 1, toward 0 from a power of two' => [1.0, [-5.551115123125783e-17], true],
            '1 - 2^-54 - 2^-107 passes the tie below 1' => [1.0, [-6.162975822039155e-33, -5.551115123125783e-17], false],
            '1 - 2^-54 + 2^-107 stays above the tie below 1' => [1.0, [6.162975822039155e-33, -5.551115123125783e-17], true],
            '1 - 2^-53 rounds to the float below 1' => [1.0, [-1.1102230246251565e-16], false],
            '1 + 2^-52 rounds to the next float' => [1.0, [2.220446049250313e-16], false],
            '(1 + 2^-52) + 2^-53 ties to even, away from x' => [1.0000000000000002, [1.1102230246251565e-16], false],
            '(1 + 2^-52) - 2^-53 ties to even, away from x' => [1.0000000000000002, [-1.1102230246251565e-16], false],
            '(1 + 2^-52) + 2^-54 rounds back' => [1.0000000000000002, [5.551115123125783e-17], true],
            '-1 - 2^-53 ties to even -1' => [-1.0, [-1.1102230246251565e-16], true],
            '-1 + 2^-54 ties to even -1, toward 0' => [-1.0, [5.551115123125783e-17], true],
            '-1 + 2^-54 + 2^-107 passes the tie toward 0' => [-1.0, [6.162975822039155e-33, 5.551115123125783e-17], false],
            '2^53 + 1 ties to even 2^53' => [9007199254740992.0, [1.0], true],
            '2^53 - 0.5 ties to even 2^53, toward 0' => [9007199254740992.0, [-0.5], true],
            '2^53 - 0.5 - 2^-60 passes the tie below 2^53' => [9007199254740992.0, [-8.673617379884035e-19, -0.5], false],
            '2^53 + 1 + 2^-60 passes the tie' => [9007199254740992.0, [8.673617379884035e-19, 1.0], false],
            '(2^53 - 1) + 0.5 ties to even 2^53' => [9007199254740991.0, [0.5], false],
            '(2^53 - 1) - 0.5 ties to even 2^53 - 2' => [9007199254740991.0, [-0.5], false],
            '(2^53 - 2) + 0.5 ties to even 2^53 - 2' => [9007199254740990.0, [0.5], true],
            '0.1 + a third of its spacing' => [0.1, [1.1564823173178713e-18], true],
            '2^-1074 + 2^-1074, a subnormal moves' => [5e-324, [5e-324], false],
            '(PHP_FLOAT_MIN - 2^-1074) + 2^-1074' => [2.225073858507201e-308, [5e-324], false],
            'PHP_FLOAT_MIN - 2^-1074' => [2.2250738585072014e-308, [-5e-324], false],
            '2^-1021 - 2^-1074' => [4.450147717014403e-308, [-5e-324], false],
            '2^-1020 - 2^-1074 ties to even 2^-1020' => [8.900295434028806e-308, [-5e-324], true],
            '2^-1020 + 2^-1074 rounds back' => [8.900295434028806e-308, [5e-324], true],
            '2^1000 + 2^947 ties to even 2^1000' => [1.0715086071862673e+301, [1.1896135267822265e+285], true],
            '2^1000 - 2^946 ties to even 2^1000, toward 0' => [1.0715086071862673e+301, [-5.948067633911132e+284], true],
            '2^1000 - 2^946 - 2^-1074 passes the tie' => [1.0715086071862673e+301, [-5e-324, -5.948067633911132e+284], false],
            '-(2^1000) + 2^947 - 1 rounds back' => [-1.0715086071862673e+301, [-1.0, 1.1896135267822265e+285], false],
            'seeded 0' => [4.8829694564017096e-05, [3.3881317890172014e-21], false],
            'seeded 1' => [-2393387518866.9526, [-0.0001220703125], true],
            'seeded 2' => [298518.9291180039, [-2.9103830456733704e-11], true],
            'seeded 3' => [-9.021335538212021, [-4.440892098500626e-16], true],
            'seeded 4' => [-0.0009449107989938833, [-3.503246160812043e-46, 2.710505431213761e-20], true],
            'seeded 5' => [0.0002182711444814227, [-2.0328790734103208e-20], false],
            'seeded 6' => [8.570797299167047e-08, [-1.1754943508222875e-38, 3.308722450212111e-24], true],
            'seeded 7' => [6.78451757150132e-10, [4.591774807899561e-41, 5.169878828456423e-26], false],
            'seeded 8' => [338561.6256392623, [2.9103830456733704e-11], false],
            'seeded 9' => [24776410670.639645, [7.52316384526264e-37, -1.9073486328125e-06], true],
        ];
    }

    /**
     * @test         intToFloats splits an int exactly into its float and the rest
     * @dataProvider dataProviderForIntToFloats
     * @param        int   $n
     * @param        float $float
     * @param        float $rest
     */
    public function testIntToFloatsSplitsIntExactly(int $n, float $float, float $rest): void
    {
        // When
        $result = ExactFloat::intToFloats($n);

        // Then
        $this->assertSame([$float, $rest], $result);
        $this->assertSame(self::reciprocals([$float, $rest]), self::reciprocals($result));
    }

    /**
     * Test data generated with Python int arithmetic:
     *   [float(n), float(n - int(float(n)))]
     * for ints n with |n| <= 2^31 - 1, whose float is exact and rest 0.0 on 32-bit PHP too.
     *
     * @return array<string, array{int, float, float}>
     */
    public static function dataProviderForIntToFloats(): array
    {
        return [
            'zero' => [0, 0.0, 0.0],
            'one' => [1, 1.0, 0.0],
            'minus one' => [-1, -1.0, 0.0],
            '12345' => [12345, 12345.0, 0.0],
            '2^31 - 1' => [2147483647, 2147483647.0, 0.0],
            '-(2^31 - 1)' => [-2147483647, -2147483647.0, 0.0],
        ];
    }

    /**
     * @test         intToFloats splits a 64-bit int exactly into its float and the rest
     * @dataProvider dataProviderForIntToFloatsSixtyFourBit
     * @param        int|float $n
     * @param        float     $float
     * @param        float     $rest
     */
    public function testIntToFloatsSplitsIntExactlySixtyFourBit(int|float $n, float $float, float $rest): void
    {
        // Given (on 32-bit PHP these literals parse as floats)
        if (\PHP_INT_SIZE !== 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = ExactFloat::intToFloats((int)$n);

        // Then
        $this->assertSame([$float, $rest], $result);
        $this->assertSame(self::reciprocals([$float, $rest]), self::reciprocals($result));
    }

    /**
     * Test data generated with Python int arithmetic:
     *   [float(n), float(n - int(float(n)))]
     * for 64-bit ints n, -2**63 <= n <= 2**63 - 1, past the 32-bit range.
     *
     * @return array<string, array{int, float, float}>
     */
    public static function dataProviderForIntToFloatsSixtyFourBit(): array
    {
        return [
            '2^53' => [9007199254740992, 9007199254740992.0, 0.0],
            '2^53 + 1 ties to 2^53' => [9007199254740993, 9007199254740992.0, 1.0],
            '2^53 + 3 ties to 2^53 + 4' => [9007199254740995, 9007199254740996.0, -1.0],
            '-(2^53 + 1) ties to -(2^53)' => [-9007199254740993, -9007199254740992.0, -1.0],
            '2^62 + 2^9 ties to 2^62' => [4611686018427388416, 4.611686018427388e+18, 512.0],
            '2^62 + 3 * 2^9 ties to 2^62 + 2^11' => [4611686018427389440, 4.61168601842739e+18, -512.0],
            '2^62 + 2^9 + 1 rounds up' => [4611686018427388417, 4.611686018427389e+18, -511.0],
            '2^63 - 512 ties to 2^63' => [9223372036854775296, 9.223372036854776e+18, -512.0],
            '2^63 - 513 rounds to 2^63 - 1024' => [9223372036854775295, 9.223372036854775e+18, 511.0],
            '2^63 - 1024' => [9223372036854774784, 9.223372036854775e+18, 0.0],
            'PHP_INT_MAX rounds up to 2^63' => [\PHP_INT_MAX, 9.223372036854776e+18, -1.0],
            'PHP_INT_MIN' => [\PHP_INT_MIN, -9.223372036854776e+18, 0.0],
            'PHP_INT_MIN + 1' => [-9223372036854775807, -9.223372036854776e+18, 1.0],
            'PHP_INT_MIN + 512 ties to PHP_INT_MIN' => [-9223372036854775296, -9.223372036854776e+18, 512.0],
        ];
    }

    /**
     * @test         exactIntSpan returns high - low exactly as a sum of floats
     * @dataProvider dataProviderForExactIntSpan
     * @param        int         $low
     * @param        int         $high
     * @param        list<float> $expected
     */
    public function testExactIntSpanIsExactDifference(int $low, int $high, array $expected): void
    {
        // When
        $result = ExactFloat::exactIntSpan($low, $high);

        // Then
        $this->assertSame($expected, $result);
        $this->assertSame(self::reciprocals($expected), self::reciprocals($result));
    }

    /**
     * Test data generated with Python int arithmetic:
     *   span = high - low
     *   [float(span), float(span - int(float(span)))]
     * for ints with -(2^31 - 1) <= low <= high <= 2^31 - 1 and span <= 2^31 - 1, so 32-bit PHP gives the same.
     *
     * @return array<string, array{int, int, list<float>}>
     */
    public static function dataProviderForExactIntSpan(): array
    {
        return [
            '0 to 0' => [0, 0, [0.0, 0.0]],
            '-1 to 1' => [-1, 1, [2.0, 0.0]],
            '-5 to 7' => [-5, 7, [12.0, 0.0]],
            '3 to 3' => [3, 3, [0.0, 0.0]],
            '-(2^30) to 2^30 - 1' => [-1073741824, 1073741823, [2147483647.0, 0.0]],
            '0 to 2^31 - 1' => [0, 2147483647, [2147483647.0, 0.0]],
            '-(2^31 - 1) to 0' => [-2147483647, 0, [2147483647.0, 0.0]],
        ];
    }

    /**
     * @test         exactIntSpan returns high - low exactly as a sum of floats, past the int range as 2^63 + excess
     * @dataProvider dataProviderForExactIntSpanSixtyFourBit
     * @param        int|float   $low
     * @param        int|float   $high
     * @param        list<float> $expected
     */
    public function testExactIntSpanIsExactDifferenceSixtyFourBit(int|float $low, int|float $high, array $expected): void
    {
        // Given (on 32-bit PHP these literals parse as floats)
        if (\PHP_INT_SIZE !== 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = ExactFloat::exactIntSpan((int)$low, (int)$high);

        // Then
        $this->assertSame($expected, $result);
        $this->assertSame(self::reciprocals($expected), self::reciprocals($result));
    }

    /**
     * Test data generated with Python int arithmetic:
     *   span = high - low
     *   [float(span), float(span - int(float(span)))] if span <= 2**63 - 1, else
     *   [2.0**63, float(e), float(e - int(float(e)))] with e = span - 2**63
     * for 64-bit ints with -2**63 <= low <= high <= 2**63 - 1, past the 32-bit range.
     *
     * @return array<string, array{int, int, list<float>}>
     */
    public static function dataProviderForExactIntSpanSixtyFourBit(): array
    {
        return [
            'PHP_INT_MIN to PHP_INT_MAX' => [\PHP_INT_MIN, \PHP_INT_MAX, [9.223372036854776e+18, 9.223372036854776e+18, -1.0]],
            'PHP_INT_MIN to -1' => [\PHP_INT_MIN, -1, [9.223372036854776e+18, -1.0]],
            'PHP_INT_MIN to 0' => [\PHP_INT_MIN, 0, [9.223372036854776e+18, 0.0, 0.0]],
            'PHP_INT_MIN to 1' => [\PHP_INT_MIN, 1, [9.223372036854776e+18, 1.0, 0.0]],
            '-1 to PHP_INT_MAX' => [-1, \PHP_INT_MAX, [9.223372036854776e+18, 0.0, 0.0]],
            'PHP_INT_MIN to PHP_INT_MIN' => [\PHP_INT_MIN, \PHP_INT_MIN, [0.0, 0.0]],
            'PHP_INT_MAX to PHP_INT_MAX' => [\PHP_INT_MAX, \PHP_INT_MAX, [0.0, 0.0]],
            '0 to PHP_INT_MAX' => [0, \PHP_INT_MAX, [9.223372036854776e+18, -1.0]],
            '-(2^62) to 2^62' => [-4611686018427387904, 4611686018427387904, [9.223372036854776e+18, 0.0, 0.0]],
            '-(2^62) to 2^62 - 1' => [-4611686018427387904, 4611686018427387903, [9.223372036854776e+18, -1.0]],
            '-(2^62) - 1 to 2^62' => [-4611686018427387905, 4611686018427387904, [9.223372036854776e+18, 1.0, 0.0]],
            'PHP_INT_MIN to 2^53 + 1' => [\PHP_INT_MIN, 9007199254740993, [9.223372036854776e+18, 9007199254740992.0, 1.0]],
            'PHP_INT_MIN + 1 to PHP_INT_MAX' => [-9223372036854775807, \PHP_INT_MAX, [9.223372036854776e+18, 9.223372036854776e+18, -2.0]],
            '-(2^53 + 1) to 2^53 + 2' => [-9007199254740993, 9007199254740994, [1.8014398509481988e+16, -1.0]],
            '1 to 2^53 + 2' => [1, 9007199254740994, [9007199254740992.0, 1.0]],
            'PHP_INT_MIN + 512 to PHP_INT_MAX - 511' => [-9223372036854775296, 9223372036854775296, [9.223372036854776e+18, 9.223372036854775e+18, 0.0]],
        ];
    }

    /**
     * @test         negated negates each term, signed zeros included
     * @dataProvider dataProviderForNegated
     * @param        list<float> $terms
     * @param        list<float> $expected
     */
    public function testNegatedNegatesEachTerm(array $terms, array $expected): void
    {
        // When
        $result = ExactFloat::negated($terms);

        // Then
        $this->assertSame($expected, $result);
        $this->assertSame(self::reciprocals($expected), self::reciprocals($result));
    }

    /**
     * Test data generated with Python floats (IEEE negation):
     *   [-t for t in terms]
     * for finite terms.
     *
     * @return array<string, array{list<float>, list<float>}>
     */
    public static function dataProviderForNegated(): array
    {
        return [
            'no terms' => [[], []],
            '+0' => [[0.0], [-0.0]],
            '-0' => [[-0.0], [0.0]],
            '+0 and -0' => [[0.0, -0.0], [-0.0, 0.0]],
            'mixed signs and magnitudes' => [[5e-324, -1.5, 6.696928794914171e+299], [-5e-324, 1.5, -6.696928794914171e+299]],
            '-(PHP_FLOAT_MAX) and 0.1' => [[-1.7976931348623157e+308, 0.1], [1.7976931348623157e+308, -0.1]],
        ];
    }

    /**
     * @test         expansionSign is the sign of the exact sum of the components
     * @dataProvider dataProviderForExpansionSign
     * @param        list<float> $expansion
     * @param        int         $sign
     */
    public function testExpansionSignOfComponents(array $expansion, int $sign): void
    {
        // When
        $result = ExactFloat::expansionSign($expansion);

        // Then
        $this->assertSame($sign, $result);
    }

    /**
     * Test data generated with Python fractions.Fraction:
     *   s = sum(map(Fraction, e))
     *   (s > 0) - (s < 0)
     * keeping only finite e whose nonzero components strictly increase in magnitude, each below the lowest set
     * bit of the next; zero components may appear anywhere.
     *
     * @return array<string, array{list<float>, int}>
     */
    public static function dataProviderForExpansionSign(): array
    {
        return [
            'no components' => [[], 0],
            '+0' => [[0.0], 0],
            '-0' => [[-0.0], 0],
            '+0 and +0' => [[0.0, 0.0], 0],
            'one' => [[1.0], 1],
            'minus one' => [[-1.0], -1],
            '2^-1074 below -1' => [[5e-324, -1.0], -1],
            '-2^-1074 below 1' => [[-5e-324, 1.0], 1],
            '-3 then a trailing +0' => [[-3.0, 0.0], -1],
            '1 then a trailing -0' => [[1.0, -0.0], 1],
            '2^-60 below 1' => [[8.673617379884035e-19, 1.0], 1],
            '1.5 below 2' => [[1.5, 2.0], 1],
            '-2^-60 below 1' => [[-8.673617379884035e-19, 1.0], 1],
            'a leading 0, -2^-1074, 0 and 2^996' => [[0.0, -5e-324, 0.0, 6.696928794914171e+299], 1],
            '-1, 0 and a trailing -0' => [[-1.0, 0.0, -0.0], -1],
        ];
    }
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
     *
     * @test         a growExpansion fold from [] over the nonzero terms matches expansion(terms) bit for bit
     * @dataProvider dataProviderForTerms
     * @param        list<float> $terms
     */
    public function testGrowExpansionFoldMatchesExpansion(array $terms): void
    {
        // Given
        if (\PHP_INT_SIZE !== 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

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
        if (\PHP_INT_SIZE !== 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
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
     * A growExpansion fold from [] over the nonzero terms.
     *
     * @param list<float> $terms
     *
     * @return list<float>
     */
    private static function growExpansionFold(array $terms): array
    {
        $folded = [];
        foreach ($terms as $term) {
            if ($term !== 0.0) {
                $folded = ExactFloat::growExpansion($folded, $term);
            }
        }

        return $folded;
    }

    /**
     * Each consecutive pair of nonzero components x, y with |x| >= |y|, or with |x| at or above the lowest set
     * bit of y: y is then not a multiple of the least power of two above |x|. Zero components are skipped.
     *
     * @param list<float> $expansion
     *
     * @return list<string>
     */
    private static function orderViolations(array $expansion): array
    {
        $nonzero = \array_values(\array_filter($expansion, static fn (float $x): bool => $x !== 0.0));
        $violations = [];
        for ($i = 0; $i + 1 < \count($nonzero); ++$i) {
            [$x, $y] = [$nonzero[$i], $nonzero[$i + 1]];
            if (!(\abs($x) < \abs($y)) || \fmod($y, self::leastPowerOfTwoAbove(\abs($x))) !== 0.0) {
                $violations[] = \sprintf('%.17g then %.17g', $x, $y);
            }
        }

        return $violations;
    }

    /**
     * The least power of two above a nonnegative finite float, by exact doubling from 2^-1074 (INF past 2^1023).
     */
    private static function leastPowerOfTwoAbove(float $x): float
    {
        $power = 2.0 ** -1074;
        while ($power <= $x) {
            $power *= 2.0;
        }

        return $power;
    }

    /**
     * 1/x for each float, so assertSame tells -0.0 (-INF) from 0.0 (INF).
     *
     * @param list<float> $floats
     *
     * @return list<float>
     */
    private static function reciprocals(array $floats): array
    {
        return \array_map(static fn (float $x): float => \fdiv(1.0, $x), $floats);
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
