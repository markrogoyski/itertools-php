<?php

declare(strict_types=1);

namespace IterTools\Tests\Util\Precision;

use IterTools\Util\Precision\ExactFloat;
use IterTools\Util\Precision\Interpolation;

class InterpolationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test rounding an expansion that is not nonadjacent throws rather than looping without end
     */
    public function testNonConvergingRoundingThrows(): void
    {
        // Given nonoverlapping components whose float sum 2^-52 is far from their value 2^-54 + 2^-53
        $expansion = [2 ** -54, -(1 - 2 ** -53), 1.0];

        // When
        try {
            Interpolation::roundExpansionDividedBy($expansion, 1.0);
            $this->fail('Expected a LogicException');
        } catch (\LogicException $e) {
            // Then
            $this->assertSame('Rounding an interpolated value did not converge.', $e->getMessage());
        }
    }

    /**
     * @test percentileRank finds the index, both weights, the exact smaller weight and which weight it is
     * @dataProvider dataProviderForPercentileRank
     * @param list<float> $expectedExactWeight
     */
    public function testPercentileRankFindsTheIndexAndWeights(
        int $last,
        float $fraction,
        float $scale,
        int $expectedIndex,
        float $expectedAbove,
        float $expectedBelow,
        array $expectedExactWeight,
        bool $expectedFromLo
    ): void {
        // When
        $rank = Interpolation::percentileRank($last, $fraction, $scale);

        // Then
        $this->assertRank($rank, $expectedIndex, $expectedAbove, $expectedBelow, $expectedExactWeight, $expectedFromLo);
    }

    /**
     * @test percentileRank finds the index, both weights, the exact smaller weight and which weight it is, past 2^31
     * @dataProvider dataProviderForPercentileRankSixtyFourBit
     * @param int|float   $last
     * @param int|float   $expectedIndex
     * @param list<float> $expectedExactWeight
     */
    public function testPercentileRankFindsTheIndexAndWeightsSixtyFourBit(
        int|float $last,
        float $fraction,
        float $scale,
        int|float $expectedIndex,
        float $expectedAbove,
        float $expectedBelow,
        array $expectedExactWeight,
        bool $expectedFromLo
    ): void {
        // Given (on 32-bit PHP these literals parse as floats and the floats are out of int range)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $rank = Interpolation::percentileRank($last, $fraction, $scale);

        // Then
        $this->assertRank($rank, $expectedIndex, $expectedAbove, $expectedBelow, $expectedExactWeight, $expectedFromLo);
    }

    /**
     * @test percentileRank gives two weights of exactly S / 2 at an exact midpoint, either of them the smaller
     * @dataProvider dataProviderForPercentileRankAtAnExactMidpoint
     */
    public function testPercentileRankAtAnExactMidpointGivesEqualWeights(
        int $last,
        float $fraction,
        float $scale,
        int $expectedIndex,
        float $expectedHalf
    ): void {
        // When
        $rank = Interpolation::percentileRank($last, $fraction, $scale);

        // Then
        $this->assertMidpointRank($rank, $expectedIndex, $expectedHalf);
    }

    /**
     * @test percentileRank gives two weights of exactly S / 2 at an exact midpoint past 2^31
     * @dataProvider dataProviderForPercentileRankAtAnExactMidpointSixtyFourBit
     * @param int|float $last
     * @param int|float $expectedIndex
     */
    public function testPercentileRankAtAnExactMidpointGivesEqualWeightsSixtyFourBit(
        int|float $last,
        float $fraction,
        float $scale,
        int|float $expectedIndex,
        float $expectedHalf
    ): void {
        // Given (on 32-bit PHP these literals parse as floats and the floats are out of int range)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $rank = Interpolation::percentileRank($last, $fraction, $scale);

        // Then
        $this->assertMidpointRank($rank, $expectedIndex, $expectedHalf);
    }

    /**
     * @test interpolateIntegers is the correctly rounded interpolation between two integers
     * @dataProvider dataProviderForInterpolateIntegers
     * @param int|float   $lo
     * @param int|float   $hi
     * @param list<float> $weight
     */
    public function testInterpolateIntegersIsCorrectlyRounded(
        int|float $lo,
        int|float $hi,
        array $weight,
        bool $fromLo,
        float $scale,
        float $expected
    ): void {
        // Given (on 32-bit PHP these literals parse as floats, and the rounding steps by 64-bit float bits)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Interpolation::interpolateIntegers($lo, $hi, $weight, $fromLo, $scale);

        // Then
        $this->assertSame($expected, $result);
        $this->assertSame(\fdiv(1.0, $expected), \fdiv(1.0, $result));
    }

    /**
     * @test roundExpansionDividedBy is the correctly rounded quotient of an expansion's value by the scale
     * @dataProvider dataProviderForRoundExpansionDividedBy
     * @param list<float> $expansion
     */
    public function testRoundExpansionDividedByIsCorrectlyRounded(array $expansion, float $scale, float $expected): void
    {
        // Given (the rounding steps by 64-bit float bits, which needs 64-bit integers)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Interpolation::roundExpansionDividedBy($expansion, $scale);

        // Then
        $this->assertSame($expected, $result);
        $this->assertSame(\fdiv(1.0, $expected), \fdiv(1.0, $result));
    }

    /**
     * @param array{int, float, float, list<float>, bool} $rank
     * @param list<float>                                 $expectedExactWeight
     */
    private function assertRank(
        array $rank,
        int $expectedIndex,
        float $expectedAbove,
        float $expectedBelow,
        array $expectedExactWeight,
        bool $expectedFromLo
    ): void {
        [$index, $above, $below, $exactWeight, $fromLo] = $rank;

        $this->assertSame($expectedIndex, $index);
        $this->assertSame($expectedAbove, $above);
        $this->assertSame($expectedBelow, $below);
        $this->assertSame($expectedFromLo, $fromLo);
        // The returned floats sum exactly to the oracle's smaller weight.
        $difference = ExactFloat::expansion([...$exactWeight, ...ExactFloat::negated($expectedExactWeight)]);
        $this->assertSame(0, ExactFloat::expansionSign($difference));
    }

    /**
     * @param array{int, float, float, list<float>, bool} $rank
     */
    private function assertMidpointRank(array $rank, int $expectedIndex, float $expectedHalf): void
    {
        [$index, $above, $below, $exactWeight, $fromLo] = $rank;

        $this->assertSame($expectedIndex, $index);
        $this->assertSame($expectedHalf, $above);
        $this->assertSame($expectedHalf, $below);
        // At a tie either weight is the smaller one, but the flag must be a bool that names one of them.
        $this->assertIsBool($fromLo);
        $this->assertSame($expectedHalf, $fromLo ? $above : $below);
        $difference = ExactFloat::expansion([...$exactWeight, -$expectedHalf]);
        $this->assertSame(0, ExactFloat::expansionSign($difference));
    }

    /**
     * Test data generated with:
     *   P = Fraction(last) * Fraction(fraction); S = Fraction(scale)
     *   j = P // S; A = P - S * j; B = S - A
     *   [j, float(A), float(B), floats whose exact sum is min(A, B), A < B]
     *
     * @return array<string, array{int, float, float, int, float, float, list<float>, bool}>
     */
    public static function dataProviderForPercentileRank(): array
    {
        return [
            'fraction 0, S=1' => [10, 0.0, 1.0, 0, 0.0, 1.0, [0.0], true],
            'fraction 0, last 1, S=1' => [1, 0.0, 1.0, 0, 0.0, 1.0, [0.0], true],
            'fraction S, S=1' => [10, 1.0, 1.0, 10, 0.0, 1.0, [0.0], true],
            'fraction S, last 1, S=1' => [1, 1.0, 1.0, 1, 0.0, 1.0, [0.0], true],
            'fraction S, last 2^31-1, S=1' => [2147483647, 1.0, 1.0, 2147483647, 0.0, 1.0, [0.0], true],
            'fraction below S, S=1' => [10, 0.9999999999999999, 1.0, 9, 0.9999999999999989, 1.1102230246251565e-15, [1.1102230246251565e-15], false],
            'fraction below S, last 1, S=1' => [1, 0.9999999999999999, 1.0, 0, 0.9999999999999999, 1.1102230246251565e-16, [1.1102230246251565e-16], false],
            'fraction below S, last 2^31-1, S=1' => [2147483647, 0.9999999999999999, 1.0, 2147483646, 0.999999761581421, 2.384185789905402e-07, [2.384185789905402e-07], false],
            'fraction below S/2, S=1' => [1, 0.49999999999999994, 1.0, 0, 0.49999999999999994, 0.5, [0.49999999999999994], true],
            'fraction above S/2, S=1' => [1, 0.5000000000000001, 1.0, 0, 0.5000000000000001, 0.4999999999999999, [0.4999999999999999], false],
            'fraction below S/2, last 3, S=1' => [3, 0.49999999999999994, 1.0, 1, 0.49999999999999983, 0.5000000000000002, [0.49999999999999983], true],
            'fraction above S/2, last 3, S=1' => [3, 0.5000000000000001, 1.0, 1, 0.5000000000000003, 0.49999999999999967, [0.49999999999999967], false],
            'smallest subnormal fraction, S=1' => [1, 5e-324, 1.0, 0, 5e-324, 1.0, [5e-324], true],
            'smallest subnormal fraction, last 2^31-1, S=1' => [2147483647, 5e-324, 1.0, 0, 1.060997895e-314, 1.0, [1.060997895e-314], true],
            'subnormal fraction, S=1' => [7, 2.37e-322, 1.0, 0, 1.66e-321, 1.0, [1.66e-321], true],
            'smallest normal fraction, S=1' => [12345, 2.2250738585072014e-308, 1.0, 0, 2.74685367832714e-304, 1.0, [2.74685367832714e-304], true],
            'fraction 1e-300, S=1' => [1, 1e-300, 1.0, 0, 1e-300, 1.0, [1e-300], true],
            'fraction 1e-300, last 999, S=1' => [999, 1e-300, 1.0, 0, 9.99e-298, 1.0, [9.99e-298, 1.309669277e-314], true],
            'fraction 0, S=100' => [10, 0.0, 100.0, 0, 0.0, 100.0, [0.0], true],
            'fraction 0, last 1, S=100' => [1, 0.0, 100.0, 0, 0.0, 100.0, [0.0], true],
            'fraction S, S=100' => [10, 100.0, 100.0, 10, 0.0, 100.0, [0.0], true],
            'fraction S, last 1, S=100' => [1, 100.0, 100.0, 1, 0.0, 100.0, [0.0], true],
            'fraction S, last 2^31-1, S=100' => [2147483647, 100.0, 100.0, 2147483647, 0.0, 100.0, [0.0], true],
            'fraction below S, S=100' => [10, 99.99999999999999, 100.0, 9, 99.99999999999986, 1.4210854715202004e-13, [1.4210854715202004e-13], false],
            'fraction below S, last 1, S=100' => [1, 99.99999999999999, 100.0, 0, 99.99999999999999, 1.4210854715202004e-14, [1.4210854715202004e-14], false],
            'fraction below S, last 2^31-1, S=100' => [2147483647, 99.99999999999999, 100.0, 2147483646, 99.99996948242189, 3.0517578110789145e-05, [3.0517578110789145e-05], false],
            'fraction below S/2, S=100' => [1, 49.99999999999999, 100.0, 0, 49.99999999999999, 50.00000000000001, [49.99999999999999], true],
            'fraction above S/2, S=100' => [1, 50.00000000000001, 100.0, 0, 50.00000000000001, 49.99999999999999, [49.99999999999999], false],
            'fraction below S/2, last 3, S=100' => [3, 49.99999999999999, 100.0, 1, 49.99999999999998, 50.00000000000002, [49.99999999999998], true],
            'fraction above S/2, last 3, S=100' => [3, 50.00000000000001, 100.0, 1, 50.00000000000002, 49.99999999999998, [49.99999999999998], false],
            'smallest subnormal fraction, S=100' => [1, 5e-324, 100.0, 0, 5e-324, 100.0, [5e-324], true],
            'smallest subnormal fraction, last 2^31-1, S=100' => [2147483647, 5e-324, 100.0, 0, 1.060997895e-314, 100.0, [1.060997895e-314], true],
            'subnormal fraction, S=100' => [7, 2.37e-322, 100.0, 0, 1.66e-321, 100.0, [1.66e-321], true],
            'smallest normal fraction, S=100' => [12345, 2.2250738585072014e-308, 100.0, 0, 2.74685367832714e-304, 100.0, [2.74685367832714e-304], true],
            'fraction 1e-300, S=100' => [1, 1e-300, 100.0, 0, 1e-300, 100.0, [1e-300], true],
            'fraction 1e-300, last 999, S=100' => [999, 1e-300, 100.0, 0, 9.99e-298, 100.0, [9.99e-298, 1.309669277e-314], true],
            'integer rank 10 * 0.1 just above 1' => [10, 0.1, 1.0, 1, 5.551115123125783e-17, 1.0, [5.551115123125783e-17], true],
            'integer rank 3 * 0.1 just above 0.3' => [3, 0.1, 1.0, 0, 0.30000000000000004, 0.7, [0.30000000000000004, -2.7755575615628914e-17], true],
            'rank just below 1' => [3, 0.3333333333333333, 1.0, 0, 1.0, 5.551115123125783e-17, [5.551115123125783e-17], false],
            'exact integer rank, S=1' => [4, 0.5, 1.0, 2, 0.0, 1.0, [0.0], true],
            'exact integer rank, S=100' => [4, 25.0, 100.0, 1, 0.0, 100.0, [0.0], true],
            'exact integer rank 7, S=100' => [10, 70.0, 100.0, 7, 0.0, 100.0, [0.0], true],
            'quarter, S=1' => [1, 0.25, 1.0, 0, 0.25, 0.75, [0.25], true],
            'quarter, S=100' => [1, 75.0, 100.0, 0, 75.0, 25.0, [25.0], false],
            'percentile 90 of 11' => [10, 90.0, 100.0, 9, 0.0, 100.0, [0.0], true],
            'percentile 33.3 of 4' => [3, 33.3, 100.0, 0, 99.89999999999999, 0.10000000000000853, [0.10000000000000853], false],
            'quantile 0.999 of 2^31' => [2147483647, 0.999, 1.0, 2145336163, 0.35299999809265137, 0.6470000019073486, [0.35299999809265137], true],
            'percentile 0.001 of 2^31' => [2147483647, 0.001, 100.0, 21474, 83.6470000000447, 16.352999999955298, [16.352999999955298, -1.2221126888256606e-15], false],
            'random 0' => [177, 0.5869517693631843, 1.0, 103, 0.8904631772836162, 0.10953682271638376, [0.10953682271638376], false],
            'random 1' => [52018, 12.896270675353193, 100.0, 6708, 38.20799052240747, 61.79200947759253, [38.20799052240747], true],
            'random 2' => [12119, 0.8446054923265126, 1.0, 10235, 0.7739615050062961, 0.22603849499370388, [0.22603849499370388], false],
            'random 3' => [1158089270, 15.380261113135784, 100.0, 178117153, 64.92080780066803, 35.07919219933196, [35.07919219933196], false],
            'random 4' => [58718452, 65.35400656639642, 100.0, 38374860, 97.57663308075729, 2.42336691924271, [2.42336691924271], false],
            'random 5' => [5075, 27.11228446686573, 100.0, 1375, 94.84366934358377, 5.15633065641623, [5.15633065641623], false],
            'random 6' => [17652, 0.6544736991649575, 1.0, 11552, 0.7697376598290213, 0.23026234017097869, [0.23026234017097869], false],
            'random 7' => [181109104, 0.8449384933828535, 1.0, 153026053, 0.47167853347262856, 0.5283214665273714, [0.47167853347262856], true],
            'random 8' => [4818, 0.48087968149077787, 1.0, 2316, 0.8783054225677587, 0.12169457743224132, [0.12169457743224132], false],
            'random 9' => [943847, 0.9837066424168598, 1.0, 928468, 0.563325225861176, 0.43667477413882405, [0.43667477413882405], false],
            'random 10' => [48148740, 45.73797407876368, 100.0, 22022258, 22.045131839500556, 77.95486816049944, [22.045131839500556], true],
            'random 11' => [835182, 62.981058017227454, 100.0, 526006, 45.996944059583114, 54.003055940416886, [45.996944059583114], true],
            'random 12' => [93602942, 0.18352297032516007, 1.0, 17178289, 0.947013679455986, 0.052986320544013976, [0.052986320544013976], false],
            'random 13' => [81804, 96.36017504298354, 100.0, 78826, 47.75921622566739, 52.24078377433261, [47.75921622566739], true],
            'random 14' => [2206779, 81.50117109724457, 100.0, 1798550, 72.85280626510732, 27.14719373489268, [27.14719373489268], false],
            'random 15' => [424, 14.91496516457429, 100.0, 63, 23.945229779499243, 76.05477022050076, [23.945229779499243], true],
            'random 16' => [41079850, 22.57637022430191, 100.0, 9274339, 2.358788862446204, 97.6412111375538, [2.358788862446204], true],
            'random 17' => [25, 0.4773805775128249, 1.0, 11, 0.934514437820623, 0.06548556217937695, [0.06548556217937695], false],
            'random 18' => [2130, 60.405973313305005, 100.0, 1286, 64.72315733965964, 35.276842660340364, [35.276842660340364], false],
            'random 19' => [1269, 0.3534659639416805, 1.0, 448, 0.5483082419925615, 0.45169175800743855, [0.45169175800743855], false],
            'random 20' => [21208409, 95.72691634708572, 100.0, 20302155, 94.19777989497295, 5.802220105027047, [5.802220105027047], false],
            'random 21' => [40109, 15.438309551437001, 100.0, 6192, 15.157798586682432, 84.84220141331757, [15.157798586682432], true],
            'random 22' => [29773391, 80.24956710201727, 100.0, 23893017, 38.90909696445411, 61.09090303554589, [38.90909696445411], true],
            'random 23' => [184, 43.81566756846833, 100.0, 80, 62.08283259817233, 37.91716740182767, [37.91716740182767], false],
            'decimal fraction 0' => [521, 20.8, 100.0, 108, 36.80000000000037, 63.19999999999963, [36.80000000000037, 3.552713678800501e-15], true],
            'decimal fraction 1' => [932, 61.2, 100.0, 570, 38.40000000000265, 61.59999999999735, [38.40000000000265], true],
            'decimal fraction 2' => [1358, 0.028, 1.0, 38, 0.02400000000000079, 0.9759999999999992, [0.02400000000000079], true],
            'decimal fraction 3' => [668, 19.5, 100.0, 130, 26.0, 74.0, [26.0], true],
            'decimal fraction 4' => [1387, 83.3, 100.0, 1155, 37.09999999999606, 62.90000000000394, [37.09999999999606], true],
            'decimal fraction 5' => [317, 92.7, 100.0, 293, 85.9000000000009, 14.099999999999099, [14.099999999999099], false],
            'decimal fraction 6' => [1096, 83.5, 100.0, 915, 16.0, 84.0, [16.0], true],
            'decimal fraction 7' => [761, 17.6, 100.0, 133, 93.60000000000107, 6.3999999999989186, [6.3999999999989186], false],
            'decimal fraction 8' => [225, 0.414, 1.0, 93, 0.1499999999999953, 0.8500000000000048, [0.1499999999999953], true],
            'decimal fraction 9' => [1348, 0.449, 1.0, 605, 0.25200000000001377, 0.7479999999999862, [0.25200000000001377], true],
            'decimal fraction 10' => [240, 12.7, 100.0, 30, 47.99999999999983, 52.00000000000017, [47.99999999999983], true],
            'decimal fraction 11' => [902, 0.888, 1.0, 800, 0.9760000000000104, 0.023999999999989585, [0.023999999999989585], false],
            'decimal fraction 12' => [1063, 0.211, 1.0, 224, 0.2929999999999927, 0.7070000000000073, [0.2929999999999927, -2.7755575615628914e-17], true],
            'decimal fraction 13' => [1041, 0.628, 1.0, 653, 0.7480000000000028, 0.2519999999999972, [0.2519999999999972], false],
            'decimal fraction 14' => [1845, 75.4, 100.0, 1391, 13.000000000010488, 86.99999999998951, [13.000000000010488], true],
            'decimal fraction 15' => [922, 94.3, 100.0, 869, 44.59999999999738, 55.40000000000262, [44.59999999999738], true],
            'decimal fraction 16' => [1314, 0.902, 1.0, 1185, 0.2280000000000315, 0.7719999999999685, [0.2280000000000315], true],
            'decimal fraction 17' => [59, 0.3, 1.0, 17, 0.6999999999999993, 0.30000000000000066, [0.30000000000000066], false],
            'decimal fraction 18' => [220, 45.4, 100.0, 99, 87.99999999999969, 12.000000000000313, [12.000000000000313], false],
            'decimal fraction 19' => [1899, 15.1, 100.0, 286, 74.89999999999932, 25.100000000000676, [25.100000000000676, -1.7763568394002505e-15], false],
            'decimal fraction 20' => [1347, 0.882, 1.0, 1188, 0.054000000000008375, 0.9459999999999916, [0.054000000000008375], true],
            'decimal fraction 21' => [1767, 0.88, 1.0, 1554, 0.9600000000000078, 0.03999999999999215, [0.03999999999999215], false],
            'decimal fraction 22' => [81, 82.6, 100.0, 66, 90.59999999999954, 9.40000000000046, [9.40000000000046], false],
            'decimal fraction 23' => [1568, 0.499, 1.0, 782, 0.4319999999999986, 0.5680000000000014, [0.4319999999999986], true],
        ];
    }

    /**
     * Test data generated with:
     *   P = Fraction(last) * Fraction(fraction); S = Fraction(scale)
     *   j = P // S; A = P - S * j; B = S - A
     *   [j, float(A), float(B), floats whose exact sum is min(A, B), A < B]
     *
     * @return array<string, array{int|float, float, float, int|float, float, float, list<float>, bool}>
     */
    public static function dataProviderForPercentileRankSixtyFourBit(): array
    {
        return [
            'last 2^31, fraction 0.3, S=1' => [2147483648, 0.3, 1.0, 644245094, 0.3999999761581421, 0.6000000238418579, [0.3999999761581421], true],
            'last 2^40+7, S=1' => [1099511627783, 0.123456789, 1.0, 135742175034, 0.25236585796093747, 0.7476341420390625, [0.25236585796093747, 1.3877787807814457e-17], true],
            'last 2^53-1, fraction 0, S=1' => [9007199254740991, 0.0, 1.0, 0, 0.0, 1.0, [0.0], true],
            'last 2^53-1, fraction S, S=1' => [9007199254740991, 1.0, 1.0, 9007199254740991, 0.0, 1.0, [0.0], true],
            'last 2^53-1, fraction below S, S=1' => [9007199254740991, 0.9999999999999999, 1.0, 9007199254740990, 1.1102230246251565e-16, 0.9999999999999999, [1.1102230246251565e-16], true],
            'last 2^53-1, smallest subnormal fraction, S=1' => [9007199254740991, 5e-324, 1.0, 0, 4.4501477170144023e-308, 1.0, [4.4501477170144023e-308], true],
            'last 2^31, fraction 0.3, S=100' => [2147483648, 30.0, 100.0, 644245094, 40.0, 60.0, [40.0], true],
            'last 2^40+7, S=100' => [1099511627783, 12.3456789, 100.0, 135742175034, 25.236158549999995, 74.76384145, [25.236158549999995], true],
            'last 2^53-1, fraction 0, S=100' => [9007199254740991, 0.0, 100.0, 0, 0.0, 100.0, [0.0], true],
            'last 2^53-1, smallest subnormal fraction, S=100' => [9007199254740991, 5e-324, 100.0, 0, 4.4501477170144023e-308, 100.0, [4.4501477170144023e-308], true],
            'last 2^53-1, fraction 0.1' => [9007199254740991, 0.1, 1.0, 900719925474099, 0.15, 0.85, [0.15], true],
            'last 2^53-1, fraction above S/2' => [9007199254740991, 0.5000000000000001, 1.0, 4503599627370496, 0.4999999999999999, 0.5000000000000001, [0.4999999999999999], true],
            'last 2^53-1, fraction below S/2' => [9007199254740991, 0.49999999999999994, 1.0, 4503599627370495, 5.551115123125783e-17, 1.0, [5.551115123125783e-17], true],
            'last 2^45+3, percentile 99.9' => [35184372088835, 99.9, 100.0, 35149187716746, 16.700000000000017, 83.29999999999998, [16.700000000000017], true],
            'last 2^46, percentile 12.5' => [70368744177664, 12.5, 100.0, 8796093022208, 0.0, 100.0, [0.0], true],
            'random 0, S=1' => [3427875168246407, 0.30578303060930145, 1.0, 1048186057496755, 0.4427035498614933, 0.5572964501385067, [0.4427035498614933], true],
            'random 1, S=1' => [3300561078621, 0.29251877632838486, 1.0, 965476087915, 0.30895981748564283, 0.6910401825143572, [0.30895981748564283], true],
            'random 2, S=1' => [14388994385162, 0.43300976007134184, 1.0, 6230575006386, 0.8825543886794551, 0.11744561132054487, [0.11744561132054487], false],
            'random 3, S=1' => [555262702146, 0.9528340129086733, 1.0, 529073188704, 0.2865879176046968, 0.7134120823953032, [0.2865879176046968], true],
            'random 4, S=1' => [98773489381136, 0.7248154603163662, 1.0, 71592552172841, 0.8019683502174697, 0.1980316497825303, [0.1980316497825303], false],
            'random 5, S=1' => [54992951464499, 0.6529037480054609, 1.0, 35905104125053, 0.7979029300054825, 0.2020970699945175, [0.2020970699945175], false],
            'random 6, S=1' => [2400064402, 0.5669268780291306, 1.0, 1360661018, 0.49471216234222437, 0.5052878376577756, [0.49471216234222437], true],
            'random 7, S=1' => [131180639995, 0.16357642236186676, 1.0, 21458059773, 0.5221107818631681, 0.47788921813683194, [0.47788921813683194], false],
            'random 8, S=1' => [31052098841, 0.37699239837728504, 1.0, 11706405216, 0.7171031606287258, 0.2828968393712742, [0.2828968393712742], false],
            'random 9, S=1' => [19113235441786, 0.23595704677493812, 1.0, 4509902589157, 0.9042128306792234, 0.09578716932077658, [0.09578716932077658], false],
            'random 10, S=1' => [12041622401, 0.4032757061011446, 1.0, 4856093776, 0.3666352383278487, 0.6333647616721513, [0.3666352383278487], true],
            'random 11, S=1' => [34438576230514, 0.5777518395423032, 1.0, 19896950768397, 0.30313129243898174, 0.6968687075610183, [0.30313129243898174], true],
            'random 12, S=1' => [48645871939, 0.0344148297088136, 1.0, 1674139398, 0.8174389985553621, 0.18256100144463794, [0.18256100144463794], false],
            'random 13, S=1' => [137460293704, 0.5292095527646389, 1.0, 72745300553, 0.9897455930974575, 0.010254406902542534, [0.010254406902542534], false],
            'random 14, S=1' => [56237726360032, 0.7502176550711636, 1.0, 42190535196356, 0.9694840769549025, 0.030515923045097537, [0.030515923045097537], false],
            'random 15, S=1' => [15226653619778, 0.3730385519297943, 1.0, 5680128817058, 0.5457006025900291, 0.45429939740997094, [0.45429939740997094], false],
            'random 16, S=1' => [1320507285628472, 0.4107529988484663, 1.0, 542402327573143, 0.08922768429872274, 0.9107723157012773, [0.08922768429872274], true],
            'random 17, S=1' => [206594220237, 0.40588396508765356, 1.0, 83853281273, 0.98551947182672, 0.01448052817328005, [0.01448052817328005], false],
            'random 18, S=1' => [207106588570905, 0.6244478850994567, 1.0, 129327271223264, 0.9378330069584536, 0.06216699304154638, [0.06216699304154638], false],
            'random 19, S=1' => [941414940209037, 0.9207634341687807, 1.0, 866820453324670, 0.2617145892567103, 0.7382854107432897, [0.2617145892567103], true],
            'random 0, S=100' => [32851377357722, 48.2635829761814, 100.0, 15855251769862, 62.50913072933736, 37.49086927066264, [37.49086927066264], false],
            'random 1, S=100' => [21941328913580, 50.23826716889259, 100.0, 11022943440009, 79.85672193675015, 20.143278063249852, [20.143278063249852], false],
            'random 2, S=100' => [1910598624491, 39.35630960151509, 100.0, 751941109896, 96.67354491282491, 3.3264550871750913, [3.3264550871750913], false],
            'random 3, S=100' => [12476358959, 45.902234631482095, 100.0, 5726927562, 82.6116998966587, 17.3883001033413, [17.3883001033413], false],
            'random 4, S=100' => [9973896844514, 10.25885207401388, 100.0, 1023207323293, 42.94649782133287, 57.05350217866713, [42.94649782133287], true],
            'random 5, S=100' => [19906613732669, 97.49070765050742, 100.0, 19407098597232, 9.692814847603913, 90.30718515239609, [9.692814847603913], true],
            'random 6, S=100' => [2422509788694, 56.56669493303761, 100.0, 1370333721893, 50.90076927103388, 49.09923072896612, [49.09923072896612], false],
            'random 7, S=100' => [11273927144, 37.659390044016405, 100.0, 4245692196, 43.7199018250227, 56.2800981749773, [43.7199018250227], true],
            'random 8, S=100' => [2306407793, 4.55802870293629, 100.0, 105126729, 21.16994080264474, 78.83005919735527, [21.16994080264474, -8.881784197001252e-16], true],
            'random 9, S=100' => [4603375699, 22.979800751373926, 100.0, 1057846563, 46.73667065527599, 53.26332934472401, [46.73667065527599, 3.552713678800501e-15], true],
            'random 10, S=100' => [4537917605, 61.732098713117324, 100.0, 2801351775, 43.85294899149514, 56.14705100850486, [43.85294899149514], true],
            'random 11, S=100' => [17015308870, 2.639726357183758, 100.0, 449157592, 99.76158815164034, 0.23841184835966533, [0.23841184835966533], false],
            'random 0, S=100, last past 2^47' => [357603870286983, 49.3, 100.0, 176298708051482, 60.88362867075155, 39.11637132924845, [39.11637132924845], false],
            'random 1, S=100, last past 2^47' => [236617706500629, 54.4, 100.0, 128720032336342, 17.263746014987525, 82.73625398501247, [17.263746014987525], true],
            'random 2, S=100, last past 2^47' => [509028231433875, 28.1, 100.0, 143036933032918, 88.22337262428431, 11.776627375715698, [11.776627375715698], false],
            'random 4, S=100, last past 2^47' => [275968682243033, 96.2, 100.0, 265481872317797, 75.3843501698603, 24.615649830139702, [24.615649830139702], false],
            'random 5, S=100, last past 2^47' => [702480307524465, 9.8, 100.0, 68843070137397, 57.499142279526026, 42.500857720473974, [42.500857720473974, -3.552713678800501e-15], false],
        ];
    }

    /**
     * Test data generated with:
     *   P = Fraction(last) * Fraction(fraction); S = Fraction(scale)
     *   j = P // S; A = P - S * j  (rows where A == S - A)
     *   [j, float(A)]
     *
     * @return array<string, array{int, float, float, int, float}>
     */
    public static function dataProviderForPercentileRankAtAnExactMidpoint(): array
    {
        return [
            'exact midpoint, last 1, S=1' => [1, 0.5, 1.0, 0, 0.5],
            'exact midpoint, last 3, S=1' => [3, 0.5, 1.0, 1, 0.5],
            'exact midpoint, last 1, S=100' => [1, 50.0, 100.0, 0, 50.0],
            'exact midpoint, last 3, S=100' => [3, 50.0, 100.0, 1, 50.0],
            'exact midpoint 499.5' => [999, 0.5, 1.0, 499, 0.5],
            'exact midpoint 2.5, S=100' => [5, 50.0, 100.0, 2, 50.0],
        ];
    }

    /**
     * Test data generated with:
     *   P = Fraction(last) * Fraction(fraction); S = Fraction(scale)
     *   j = P // S; A = P - S * j  (rows where A == S - A)
     *   [j, float(A)]
     *
     * @return array<string, array{int|float, float, float, int|float, float}>
     */
    public static function dataProviderForPercentileRankAtAnExactMidpointSixtyFourBit(): array
    {
        return [
            'last 2^31, exact midpoint, S=1' => [2147483649, 0.5, 1.0, 1073741824, 0.5],
            'last 2^53-1, exact midpoint, S=1' => [9007199254740991, 0.5, 1.0, 4503599627370495, 0.5],
            'last 2^31, exact midpoint, S=100' => [2147483649, 50.0, 100.0, 1073741824, 50.0],
            'exact midpoint from the far side' => [8425836098384796, 0.625, 1.0, 5266147561490497, 0.5],
        ];
    }

    /**
     * Test data generated with:
     *   W = sum(map(Fraction, weight)); w = W if fromLo else Fraction(scale) - W
     *   float(Fraction(lo) + (Fraction(hi) - Fraction(lo)) * w / Fraction(scale))
     *
     * @return array<string, array{int|float, int|float, list<float>, bool, float, float}>
     */
    public static function dataProviderForInterpolateIntegers(): array
    {
        return [
            'lo == hi past 2^53, a tie to even, S=1' => [9007199254740993, 9007199254740993, [0.30000000000000004, -2.7755575615628914e-17], true, 1.0, 9007199254740992.0],
            'lo == hi == PHP_INT_MAX, S=1' => [\PHP_INT_MAX, \PHP_INT_MAX, [0.333, 1.3877787807814457e-17], false, 1.0, 9.223372036854776e+18],
            'lo == hi == PHP_INT_MIN, S=1' => [\PHP_INT_MIN, \PHP_INT_MIN, [0.5, 0.0], true, 1.0, -9.223372036854776e+18],
            'PHP_INT_MIN to PHP_INT_MAX at S/2, S=1' => [\PHP_INT_MIN, \PHP_INT_MAX, [0.5, 0.0], true, 1.0, -0.5],
            'PHP_INT_MIN to PHP_INT_MAX at S/2 from hi, S=1' => [\PHP_INT_MIN, \PHP_INT_MAX, [0.5, 0.0], false, 1.0, -0.5],
            'PHP_INT_MIN to PHP_INT_MAX, small weight, S=1' => [\PHP_INT_MIN, \PHP_INT_MAX, [0.14, -1.0408340855860843e-17], true, 1.0, -6.640827866535438e+18],
            'PHP_INT_MIN to PHP_INT_MAX, small weight from hi, S=1' => [\PHP_INT_MIN, \PHP_INT_MAX, [0.14, -1.0408340855860843e-17], false, 1.0, 6.640827866535438e+18],
            'PHP_INT_MAX-1 to PHP_INT_MAX at S/2, S=1' => [9223372036854775806, \PHP_INT_MAX, [0.5, 0.0], true, 1.0, 9.223372036854776e+18],
            'PHP_INT_MAX-1 to PHP_INT_MAX, three components, S=1' => [9223372036854775806, \PHP_INT_MAX, [0.33330000000000004, 0.0, -2.7755575615628914e-17], false, 1.0, 9.223372036854776e+18],
            '2^53 to 2^53+2 at S/2, a tie to even, S=1' => [9007199254740992, 9007199254740994, [0.5, 0.0], true, 1.0, 9007199254740992.0],
            '2^53+2 to 2^53+4 at S/2, a tie to even, S=1' => [9007199254740994, 9007199254740996, [0.5, 0.0], false, 1.0, 9007199254740996.0],
            'hi below lo, S=1' => [\PHP_INT_MAX, \PHP_INT_MIN, [0.33, -8.673617379884035e-18], true, 1.0, 3.1359464925306235e+18],
            'hi below lo, from hi, S=1' => [1152921504606846976, -1152921504606846969, [0.4, 0.0], false, 1.0, -2.3058430092136934e+17],
            'negative endpoints, S=1' => [-4611686018427387909, -36028797018963965, [0.10004882812500004, 0.0], true, 1.0, -4.153896875523781e+18],
            'negative endpoints, three components, S=1' => [-4611686018427387909, -36028797018963965, [0.10004882812500004, 0.0, -0.0], false, 1.0, -4.938179399225706e+17],
            'weight 0 from lo, S=1' => [9007199254740995, \PHP_INT_MAX, [0.0, 0.0], true, 1.0, 9007199254740996.0],
            'weight 0 from hi, S=1' => [\PHP_INT_MIN, 9007199254740995, [0.0, 0.0], false, 1.0, 9007199254740996.0],
            'exact zero at S/2, S=1' => [-1152921504606846976, 1152921504606846976, [0.5, 0.0], true, 1.0, 0.0],
            'exact zero at weight 0, S=1' => [0, 1152921504606846976, [0.0, 0.0], true, 1.0, 0.0],
            'exactly representable quarter, S=1' => [4611686018427387904, 4611686018427392000, [0.25, 0.0], true, 1.0, 4.611686018427389e+18],
            'exactly representable from hi, S=1' => [-4611686018427387904, -4611686018426339328, [0.125, 0.0], false, 1.0, -4.6116860184264704e+18],
            'smallest weight past 2^62, S=1' => [4611686018427387905, \PHP_INT_MAX, [5e-324, 0.0], true, 1.0, 4.611686018427388e+18],
            'smallest weight past 2^62 from hi, S=1' => [4611686018427387905, \PHP_INT_MAX, [5e-324, 0.0], false, 1.0, 9.223372036854776e+18],
            'tiny weight past 2^62, S=1' => [-4611686018427387905, 4611686018427387905, [9.332636185032189e-302, 0.0], true, 1.0, -4.611686018427388e+18],
            'small endpoints, S=1' => [0, 10, [0.3, 0.0], true, 1.0, 3.0],
            'small endpoints from hi, S=1' => [-3, 4, [0.30000000000000004, 0.0, -5.551115123125783e-17], false, 1.0, 1.9000000000000001],
            'lo == hi past 2^53, a tie to even, S=100' => [9007199254740993, 9007199254740993, [0.30000000000000004, -2.7755575615628914e-17], true, 100.0, 9007199254740992.0],
            'lo == hi == PHP_INT_MAX, S=100' => [\PHP_INT_MAX, \PHP_INT_MAX, [33.300000000000004, 8.881784197001252e-16], false, 100.0, 9.223372036854776e+18],
            'lo == hi == PHP_INT_MIN, S=100' => [\PHP_INT_MIN, \PHP_INT_MIN, [50.0, 0.0], true, 100.0, -9.223372036854776e+18],
            'PHP_INT_MIN to PHP_INT_MAX at S/2, S=100' => [\PHP_INT_MIN, \PHP_INT_MAX, [50.0, 0.0], true, 100.0, -0.5],
            'PHP_INT_MIN to PHP_INT_MAX at S/2 from hi, S=100' => [\PHP_INT_MIN, \PHP_INT_MAX, [50.0, 0.0], false, 100.0, -0.5],
            'PHP_INT_MIN to PHP_INT_MAX, small weight, S=100' => [\PHP_INT_MIN, \PHP_INT_MAX, [14.0, 0.0], true, 100.0, -6.640827866535438e+18],
            'PHP_INT_MIN to PHP_INT_MAX, small weight from hi, S=100' => [\PHP_INT_MIN, \PHP_INT_MAX, [14.0, 0.0], false, 100.0, 6.640827866535438e+18],
            'PHP_INT_MAX-1 to PHP_INT_MAX at S/2, S=100' => [9223372036854775806, \PHP_INT_MAX, [50.0, 0.0], true, 100.0, 9.223372036854776e+18],
            'PHP_INT_MAX-1 to PHP_INT_MAX, three components, S=100' => [9223372036854775806, \PHP_INT_MAX, [33.33, 0.0, 5.329070518200751e-15], false, 100.0, 9.223372036854776e+18],
            '2^53 to 2^53+2 at S/2, a tie to even, S=100' => [9007199254740992, 9007199254740994, [50.0, 0.0], true, 100.0, 9007199254740992.0],
            '2^53+2 to 2^53+4 at S/2, a tie to even, S=100' => [9007199254740994, 9007199254740996, [50.0, 0.0], false, 100.0, 9007199254740996.0],
            'hi below lo, S=100' => [\PHP_INT_MAX, \PHP_INT_MIN, [33.0, 1.8318679906315083e-15], true, 100.0, 3.1359464925306235e+18],
            'hi below lo, from hi, S=100' => [1152921504606846976, -1152921504606846969, [40.0, 0.0], false, 100.0, -2.3058430092136938e+17],
            'negative endpoints, S=100' => [-4611686018427387909, -36028797018963965, [10.0, 0.0], true, 100.0, -4.1541202962865454e+18],
            'negative endpoints, three components, S=100' => [-4611686018427387909, -36028797018963965, [10.0, 0.0, -0.0], false, 100.0, -4.9359451915980634e+17],
            'weight 0 from lo, S=100' => [9007199254740995, \PHP_INT_MAX, [0.0, 0.0], true, 100.0, 9007199254740996.0],
            'weight 0 from hi, S=100' => [\PHP_INT_MIN, 9007199254740995, [0.0, 0.0], false, 100.0, 9007199254740996.0],
            'exact zero at S/2, S=100' => [-1152921504606846976, 1152921504606846976, [50.0, 0.0], true, 100.0, 0.0],
            'exact zero at weight 0, S=100' => [0, 1152921504606846976, [0.0, 0.0], true, 100.0, 0.0],
            'exactly representable quarter, S=100' => [4611686018427387904, 4611686018427392000, [25.0, 0.0], true, 100.0, 4.611686018427389e+18],
            'exactly representable from hi, S=100' => [-4611686018427387904, -4611686018426339328, [12.5, 0.0], false, 100.0, -4.6116860184264704e+18],
            'smallest weight past 2^62, S=100' => [4611686018427387905, \PHP_INT_MAX, [5e-324, 0.0], true, 100.0, 4.611686018427388e+18],
            'smallest weight past 2^62 from hi, S=100' => [4611686018427387905, \PHP_INT_MAX, [5e-324, 0.0], false, 100.0, 9.223372036854776e+18],
            'tiny weight past 2^62, S=100' => [-4611686018427387905, 4611686018427387905, [9.332636185032189e-302, 0.0], true, 100.0, -4.611686018427388e+18],
            'small endpoints, S=100' => [0, 10, [30.0, 0.0], true, 100.0, 3.0],
            'small endpoints from hi, S=100' => [-3, 4, [30.0, 0.0, -0.0], false, 100.0, 1.9],
            'smallest weight between 0 and 1, S=100' => [0, 1, [5e-324, 0.0], true, 100.0, 0.0],
            'random 0' => [17631643319115663, 2230741617354521773, [0.3573079091221154, 0.0], true, 1.0, 8.083933407990057e+17],
            'random 1' => [-84791090891240, -3827248668358512, [0.46197198007052975, 0.0, -0.0], true, 100.0, -102080196265165.14],
            'random 2' => [-6866188627920765383, -962725009783576256, [0.09673502526065703, 0.0], false, 1.0, -1.533796712009447e+18],
            'random 3' => [4564958676592969833, 12651652488474361, [0.4835836393822912, 0.0, -0.0], false, 1.0, 2.214072850790494e+18],
            'random 4' => [2179522592193455, 110901394153224000, [27.391555510230603, 0.0, -0.0], true, 100.0, 3.1960134392594756e+16],
            'random 5' => [-56324880143753636, 8709896993330076, [27.4223577914657, 0.0, 3.552713678800501e-15], true, 100.0, -3.849081086834021e+16],
            'random 6' => [5824820882050232, -1140859537329449574, [6.382671876830457, 0.0], true, 100.0, -6.7364279165528984e+16],
            'random 7' => [-9529669798626926, 2477488675149193, [0.162604406251317, 0.0], true, 1.0, -7577252924233091.0],
            'random 8' => [-2966654451996212, -415914237953579926, [0.23925406764227386, 0.0, -0.0], true, 1.0, -1.0176604352779765e+17],
            'random 9' => [-465610333708920463, -781245170496908, [31.138766724527585, 1.7763568394002505e-15], true, 100.0, -3.2086828816119296e+17],
            'random 10' => [-3590399495835738317, -10537792974770131, [41.70808832518448, 0.0], true, 100.0, -2.0973076148870326e+18],
            'random 11' => [416307205543339231, 3616704284003936, [9.82786041421879, 0.0, -1.7763568394002505e-15], false, 100.0, 4.417535069051124e+16],
            'random 12' => [1657634893801580016, -6513285958097960148, [0.1497421581243863, 0.0, -0.0], false, 1.0, -5.289754635870974e+18],
            'random 13' => [217027086201451, -598115812640105, [47.81701746924013, 3.1086244689504383e-15], true, 100.0, -172749936136886.25],
            'random 14' => [51998642354217041, -1327061696177225, [13.366129515155478, 0.0, -0.0], false, 100.0, 5800520972066983.0],
            'random 15' => [2532342541024617801, 4152651196839245118, [0.0004717113127155059, 0.0, -0.0], true, 1.0, 2.533106858947656e+18],
            'random 16' => [-409804195888741580, 5124133853598967861, [0.2677967815985406, 0.0], false, 1.0, 3.642163054380454e+18],
            'random 17' => [7623694403431781, -411871356927172717, [23.208016332884483, 0.0, -0.0], false, 100.0, -3.145148768987239e+17],
            'random 18' => [9182560364013703, -8207953228603205, [0.1886991340134897, 0.0], false, 1.0, -4926378373626573.0],
            'random 19' => [-15878540501764500, -2291189986422544423, [42.94545395314971, 0.0, -0.0], false, 100.0, -1.314047157123891e+18],
            'random 20' => [-168934604010728426, 32158880314610677, [0.4133510209378982, 0.0], false, 1.0, -5.096331673522747e+16],
            'random 21' => [3659666947550699, -712122029034913020, [42.58178242948598, 0.0], true, 100.0, -3.011329375057868e+17],
            'random 22' => [233693913553356816, -9590393087716968, [10.103875345481597, 0.0], false, 100.0, 1.4990749990416334e+16],
            'random 23' => [1829351996257484670, 742068402152283529, [0.3350433115991772, 0.0], false, 1.0, 1.1063554981687457e+18],
            'random 24' => [-1102123665992542980, -770364129726590268, [48.4451756227587, 0.0, -0.0], false, 100.0, -9.310856197158808e+17],
            'random 25' => [2208823533778354, 14140225716432025, [0.47949251842187013, 0.0], true, 1.0, 7929841614643161.0],
            'random 26' => [-4127521658900714781, -5533545841365926, [0.07149301631398652, 0.0, -0.0], true, 1.0, -3.832828295487704e+18],
            'random 27' => [-2895623132991867769, -4080867438339837, [43.431033706397244, 0.0, -0.0], true, 100.0, -1.6397964370045926e+18],
            'random 28' => [12126512202436699, 502539761330587558, [0.2865819791996498, 0.0, -0.0], true, 1.0, 1.526701117633131e+17],
            'random 29' => [-1291807839598658, -7289484483474461, [30.19210632922637, 0.0], false, 100.0, -5478659573872303.0],
        ];
    }

    /**
     * Test data generated with:
     *   float(sum(map(Fraction, expansion)) / Fraction(scale))
     *
     *   Each expansion is the Grow-Expansion fold, from [], of exact TwoProducts with an integer-valued
     *   factor, as interpolateIntegers builds one; each is nonadjacent and in increasing magnitude, of
     *   magnitude below 2^70, and zero or above scale * 2^-1021.
     *
     * @return array<string, array{list<float>, float, float}>
     */
    public static function dataProviderForRoundExpansionDividedBy(): array
    {
        return [
            'zero, S=1' => [[0.0], 1.0, 0.0],
            'cancelling to zero, S=1' => [[0.0], 1.0, 0.0],
            'one, S=1' => [[1.0], 1.0, 1.0],
            'three, S=1' => [[3.0], 1.0, 3.0],
            'single component 0.1, S=1' => [[0.1], 1.0, 0.1],
            'product with an error, S=1' => [[2.2551405187698492e-17, 0.37], 1.0, 0.37],
            '0.1 + 0.2, S=1' => [[-2.7755575615628914e-17, 0.30000000000000004], 1.0, 0.30000000000000004],
            'negative, S=1' => [[2.7755575615628914e-17, -0.7000000000000001], 1.0, -0.7000000000000001],
            'three times the smallest normal, S=1' => [[6.675221575521604e-308], 1.0, 6.675221575521604e-308],
            'just above 2^-1021, S=1' => [[4.450147717014404e-308], 1.0, 4.450147717014404e-308],
            '1.01 * 2^-1021, S=1' => [[1.3350443151043208e-307], 1.0, 1.3350443151043208e-307],
            'negative near 2^-1021, S=1' => [[-6.675221575521604e-308], 1.0, -6.675221575521604e-308],
            'zero, S=100' => [[0.0], 100.0, 0.0],
            'cancelling to zero, S=100' => [[0.0], 100.0, 0.0],
            'one, S=100' => [[1.0], 100.0, 0.01],
            'three, S=100' => [[3.0], 100.0, 0.03],
            'single component 0.1, S=100' => [[0.1], 100.0, 0.001],
            'product with an error, S=100' => [[2.2551405187698492e-17, 0.37], 100.0, 0.0037],
            '0.1 + 0.2, S=100' => [[-2.7755575615628914e-17, 0.30000000000000004], 100.0, 0.003],
            'negative, S=100' => [[2.7755575615628914e-17, -0.7000000000000001], 100.0, -0.007],
            'three times the smallest normal, S=100' => [[6.675221575521604e-306], 100.0, 6.675221575521604e-308],
            'just above 2^-1021, S=100' => [[-2.77e-322, 4.450147717014404e-306], 100.0, 4.450147717014404e-308],
            '1.01 * 2^-1021, S=100' => [[4.494649194184547e-306], 100.0, 4.494649194184547e-308],
            'negative near 2^-1021, S=100' => [[-6.675221575521604e-306], 100.0, -6.675221575521604e-308],
            'tie 2^53 + 1, S=1' => [[1.0, 9007199254740992.0], 1.0, 9007199254740992.0],
            'tie 2^53 + 3, S=1' => [[-1.0, 9007199254740996.0], 1.0, 9007199254740996.0],
            'tie 1 + 2^-53, S=1' => [[1.1102230246251565e-16, 1.0], 1.0, 1.0],
            'tie 1 + 3 * 2^-53, S=1' => [[-1.1102230246251565e-16, 1.0000000000000004], 1.0, 1.0000000000000004],
            'negative tie, S=1' => [[-1.1102230246251565e-16, -1.0], 1.0, -1.0],
            'tie 2^53 + 1, S=100' => [[-28.0, 9.007199254740993e+17], 100.0, 9007199254740992.0],
            'tie 2^53 + 3, S=100' => [[44.0, 9.007199254740995e+17], 100.0, 9007199254740996.0],
            'tie 1 + 2^-53, S=100' => [[-3.1086244689504383e-15, 100.00000000000001], 100.0, 1.0],
            'negative tie, S=100' => [[3.1086244689504383e-15, -100.00000000000001], 100.0, -1.0],
            'near 100 * 2^63, S=100' => [[-100.0, 9.223372036854776e+20], 100.0, 9.223372036854776e+18],
            'tie below 2^63, S=100' => [[-51200.0, 9.223372036854776e+20], 100.0, 9.223372036854776e+18],
            'negative tie below 2^63, S=100' => [[51200.0, -9.223372036854776e+20], 100.0, -9.223372036854776e+18],
            'just past a midpoint below 2^63, S=100' => [[51201.0, -9.223372036854776e+20], 100.0, -9.223372036854775e+18],
            'near 2^63, S=1' => [[511.0, 9.223372036854775e+18], 1.0, 9.223372036854775e+18],
            'interpolation, lo == hi past 2^53, a tie to even, S=1' => [[1.0, 9007199254740992.0], 1.0, 9007199254740992.0],
            'interpolation, lo == hi == PHP_INT_MAX, S=1' => [[-1.0, 9.223372036854776e+18], 1.0, 9.223372036854776e+18],
            'interpolation, lo == hi == PHP_INT_MIN, S=1' => [[-9.223372036854776e+18], 1.0, -9.223372036854776e+18],
            'interpolation, PHP_INT_MIN to PHP_INT_MAX at S/2, S=1' => [[-0.5], 1.0, -0.5],
            'interpolation, PHP_INT_MIN to PHP_INT_MAX at S/2 from hi, S=1' => [[-0.5], 1.0, -0.5],
            'interpolation, PHP_INT_MIN to PHP_INT_MAX, small weight, S=1' => [[-1.3645334862033565e-14, -192.14, -6.640827866535438e+18], 1.0, -6.640827866535438e+18],
            'interpolation, PHP_INT_MIN to PHP_INT_MAX, small weight from hi, S=1' => [[1.3645334862033565e-14, 191.14, 6.640827866535438e+18], 1.0, 6.640827866535438e+18],
            'interpolation, PHP_INT_MAX-1 to PHP_INT_MAX at S/2, S=1' => [[-1.5, 9.223372036854776e+18], 1.0, 9.223372036854776e+18],
            'interpolation, PHP_INT_MAX-1 to PHP_INT_MAX, three components, S=1' => [[-8.326672684688674e-17, -1.3333, 9.223372036854776e+18], 1.0, 9.223372036854776e+18],
            'interpolation, 2^53 to 2^53+2 at S/2, a tie to even, S=1' => [[1.0, 9007199254740992.0], 1.0, 9007199254740992.0],
            'interpolation, 2^53+2 to 2^53+4 at S/2, a tie to even, S=1' => [[-1.0, 9007199254740996.0], 1.0, 9007199254740996.0],
            'interpolation, hi below lo, S=1' => [[-1.2498682644412895e-14, 159.33, 3.1359464925306235e+18], 1.0, 3.1359464925306235e+18],
            'interpolation, hi below lo, from hi, S=1' => [[-3.3306690738754696e-16, 4.2, -2.3058430092136934e+17], 1.0, -2.3058430092136934e+17],
            'interpolation, negative endpoints, S=1' => [[-5.329070518200751e-15, -108.199609375, -4.153896875523781e+18], 1.0, -4.153896875523781e+18],
            'interpolation, negative endpoints, three components, S=1' => [[-1.7763568394002505e-15, -21.800390625, -4.938179399225706e+17], 1.0, -4.938179399225706e+17],
            'interpolation, weight 0 from lo, S=1' => [[-1.0, 9007199254740996.0], 1.0, 9007199254740996.0],
            'interpolation, weight 0 from hi, S=1' => [[-1.0, 9007199254740996.0], 1.0, 9007199254740996.0],
            'interpolation, exact zero at S/2, S=1' => [[0.0], 1.0, 0.0],
            'interpolation, exact zero at weight 0, S=1' => [[0.0], 1.0, 0.0],
            'interpolation, exactly representable quarter, S=1' => [[4.611686018427389e+18], 1.0, 4.611686018427389e+18],
            'interpolation, exactly representable from hi, S=1' => [[-4.6116860184264704e+18], 1.0, -4.6116860184264704e+18],
            'interpolation, smallest weight past 2^62, S=1' => [[-1e-323, 2.2784756311113742e-305, 1.0, 4.611686018427388e+18], 1.0, 4.611686018427388e+18],
            'interpolation, smallest weight past 2^62 from hi, S=1' => [[1e-323, -2.2784756311113742e-305, -1.0, 9.223372036854776e+18], 1.0, 9.223372036854776e+18],
            'interpolation, tiny weight past 2^62, S=1' => [[1.8665272370064378e-301, 8.607837561916492e-283, -1.0, -4.611686018427388e+18], 1.0, -4.611686018427388e+18],
            'interpolation, small endpoints, S=1' => [[-1.1102230246251565e-16, 3.0], 1.0, 3.0],
            'interpolation, small endpoints from hi, S=1' => [[-5.551115123125783e-17, 1.9000000000000001], 1.0, 1.9000000000000001],
            'interpolation, lo == hi past 2^53, a tie to even, S=100' => [[-28.0, 9.007199254740993e+17], 100.0, 9007199254740992.0],
            'interpolation, lo == hi == PHP_INT_MAX, S=100' => [[-100.0, 9.223372036854776e+20], 100.0, 9.223372036854776e+18],
            'interpolation, lo == hi == PHP_INT_MIN, S=100' => [[-9.223372036854776e+20], 100.0, -9.223372036854776e+18],
            'interpolation, PHP_INT_MIN to PHP_INT_MAX at S/2, S=100' => [[-50.0], 100.0, -0.5],
            'interpolation, PHP_INT_MIN to PHP_INT_MAX at S/2 from hi, S=100' => [[-50.0], 100.0, -0.5],
            'interpolation, PHP_INT_MIN to PHP_INT_MAX, small weight, S=100' => [[-14.0, -6.640827866535439e+20], 100.0, -6.640827866535438e+18],
            'interpolation, PHP_INT_MIN to PHP_INT_MAX, small weight from hi, S=100' => [[-86.0, 6.640827866535439e+20], 100.0, 6.640827866535438e+18],
            'interpolation, PHP_INT_MAX-1 to PHP_INT_MAX at S/2, S=100' => [[-150.0, 9.223372036854776e+20], 100.0, 9.223372036854776e+18],
            'interpolation, PHP_INT_MAX-1 to PHP_INT_MAX, three components, S=100' => [[8.881784197001252e-15, -133.33, 9.223372036854776e+20], 100.0, 9.223372036854776e+18],
            'interpolation, 2^53 to 2^53+2 at S/2, a tie to even, S=100' => [[-28.0, 9.007199254740993e+17], 100.0, 9007199254740992.0],
            'interpolation, 2^53+2 to 2^53+4 at S/2, a tie to even, S=100' => [[44.0, 9.007199254740995e+17], 100.0, 9007199254740996.0],
            'interpolation, hi below lo, S=100' => [[1.8318679906315083e-15, 31677.0, 3.135946492530623e+20], 100.0, 3.1359464925306235e+18],
            'interpolation, hi below lo, from hi, S=100' => [[420.0, -2.305843009213694e+19], 100.0, -2.3058430092136938e+17],
            'interpolation, negative endpoints, S=100' => [[-420.0, -4.1541202962865455e+20], 100.0, -4.1541202962865454e+18],
            'interpolation, negative endpoints, three components, S=100' => [[220.0, -4.935945191598064e+19], 100.0, -4.9359451915980634e+17],
            'interpolation, weight 0 from lo, S=100' => [[44.0, 9.007199254740995e+17], 100.0, 9007199254740996.0],
            'interpolation, weight 0 from hi, S=100' => [[44.0, 9.007199254740995e+17], 100.0, 9007199254740996.0],
            'interpolation, exact zero at S/2, S=100' => [[0.0], 100.0, 0.0],
            'interpolation, exact zero at weight 0, S=100' => [[0.0], 100.0, 0.0],
            'interpolation, exactly representable quarter, S=100' => [[-28672.0, 4.611686018427389e+20], 100.0, 4.611686018427389e+18],
            'interpolation, exactly representable from hi, S=100' => [[-4.6116860184264704e+20], 100.0, -4.6116860184264704e+18],
            'interpolation, smallest weight past 2^62, S=100' => [[-1e-323, 2.2784756311113742e-305, 100.0, 4.611686018427388e+20], 100.0, 4.611686018427388e+18],
            'interpolation, smallest weight past 2^62 from hi, S=100' => [[1e-323, -2.2784756311113742e-305, -100.0, 9.223372036854776e+20], 100.0, 9.223372036854776e+18],
            'interpolation, tiny weight past 2^62, S=100' => [[1.8665272370064378e-301, 8.607837561916492e-283, -100.0, -4.611686018427388e+20], 100.0, -4.611686018427388e+18],
            'interpolation, small endpoints, S=100' => [[300.0], 100.0, 3.0],
            'interpolation, small endpoints from hi, S=100' => [[190.0], 100.0, 1.9],
            'interpolation, random 0' => [[2.6645352591003757e-15, 43.54935040414876, 8.083933407990057e+17], 1.0, 8.083933407990057e+17],
            'interpolation, random 1' => [[0.3604357990097924, -1.0208019626516514e+16], 100.0, -102080196265165.14],
            'interpolation, random 2' => [[3.219646771412954e-15, -57.44929574389226, -1.533796712009447e+18], 1.0, -1.533796712009447e+18],
            'interpolation, random 3' => [[-1.7573236017192464, 2.214072850790494e+18], 1.0, 2.214072850790494e+18],
            'interpolation, random 4' => [[157.3833271648612, 3.1960134392594755e+18], 100.0, 3.1960134392594756e+16],
            'interpolation, random 5' => [[-118.99070191350052, -3.8490810868340204e+18], 100.0, -3.849081086834021e+16],
            'interpolation, random 6' => [[-148.80554020102008, -6.736427916552899e+18], 100.0, -6.7364279165528984e+16],
            'interpolation, random 7' => [[0.4756437398624076, -7577252924233091.0], 1.0, -7577252924233091.0],
            'interpolation, random 8' => [[-6.914494056296055, -1.0176604352779765e+17], 1.0, -1.0176604352779765e+17],
            'interpolation, random 9' => [[3.375077994860476e-14, 562.6361202976658, -3.2086828816119296e+19], 100.0, -3.2086828816119296e+17],
            'interpolation, random 10' => [[3.694822225952521e-13, -5598.139852371315, -2.0973076148870326e+20], 100.0, -2.0973076148870326e+18],
            'interpolation, random 11' => [[-1.2434497875801753e-14, -227.29733080478968, 4.417535069051124e+18], 100.0, 4.417535069051124e+16],
            'interpolation, random 12' => [[-96.33713718960563, -5.289754635870974e+18], 1.0, -5.289754635870974e+18],
            'interpolation, random 13' => [[0.10393931833763226, -1.7274993613688624e+16], 100.0, -172749936136886.25],
            'interpolation, random 14' => [[-37.22532803972956, 5.800520972066984e+17], 100.0, 5800520972066983.0],
            'interpolation, random 15' => [[-8.881784197001252e-16, 223.72568989846866, 2.533106858947656e+18], 1.0, 2.533106858947656e+18],
            'interpolation, random 16' => [[7.66053886991358e-15, 142.0256238597678, 3.642163054380454e+18], 1.0, 3.642163054380454e+18],
            'interpolation, random 17' => [[280.32392764972303, -3.145148768987239e+19], 100.0, -3.145148768987239e+17],
            'interpolation, random 18' => [[0.1436265553901479, -4926378373626573.0], 1.0, -4926378373626573.0],
            'interpolation, random 19' => [[-1.4210854715202004e-14, -130.53221336652047, -1.314047157123891e+20], 100.0, -1.314047157123891e+18],
            'interpolation, random 20' => [[1.1102230246251565e-16, -1.4655866163085216, -5.096331673522747e+16], 1.0, -5.096331673522747e+16],
            'interpolation, random 21' => [[-1.4210854715202004e-14, -1684.0718911940837, -3.011329375057868e+19], 100.0, -3.011329375057868e+17],
            'interpolation, random 22' => [[-53.61333494148141, 1.4990749990416335e+18], 100.0, 1.4990749990416334e+16],
            'interpolation, random 23' => [[-5.551115123125783e-16, 32.87183268344901, 1.1063554981687457e+18], 1.0, 1.1063554981687457e+18],
            'interpolation, random 24' => [[1.1368683772161603e-13, -1166.204083798495, -9.310856197158809e+19], 100.0, -9.310856197158808e+17],
            'interpolation, random 25' => [[-0.19685523212528078, 7929841614643161.0], 1.0, 7929841614643161.0],
            'interpolation, random 26' => [[1.0103029524088925e-14, -161.6329784939255, -3.832828295487704e+18], 1.0, -3.832828295487704e+18],
            'interpolation, random 27' => [[-5.684341886080801e-13, -13158.103763737388, -1.6397964370045924e+20], 100.0, -1.6397964370045926e+18],
            'interpolation, random 28' => [[-4.440892098500626e-16, 7.745907024178885, 1.526701117633131e+17], 1.0, 1.526701117633131e+17],
            'interpolation, random 29' => [[-25.431912328619582, -5.478659573872303e+17], 100.0, -5478659573872303.0],
        ];
    }
}
