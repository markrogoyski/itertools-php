<?php

declare(strict_types=1);

namespace IterTools\Tests\Util\Precision;

use IterTools\Util\Precision\Decimal;

class DecimalTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         shortestDecimal finds the shortest decimal of a power of two whose nearest decimal at that length
     *               doesn't read back
     * @dataProvider dataProviderForShortestDecimalAtPowerOfTwo
     * @param        float                    $x
     * @param        array{string, int, int} $expected
     */
    public function testShortestDecimalAtPowerOfTwo(float $x, array $expected): void
    {
        // When
        $result = Decimal::shortestDecimal($x);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's shortest round-tripping repr, as its significand d.ddd, its exponent and
     * the number of digits after the point:
     *   t = Decimal(repr(2.0 ** k)).normalize().as_tuple(); digits = ''.join(map(str, t.digits))
     *   [digits[0] + ('.' + digits[1:] if len(digits) > 1 else ''), t.exponent + len(digits) - 1, len(digits) - 1]
     */
    public static function dataProviderForShortestDecimalAtPowerOfTwo(): array
    {
        return [
            '2^-77'   => [2.0 ** -77, ['6.617444900424222', -24, 15]],
            '2^-24'   => [2.0 ** -24, ['5.960464477539063', -8, 15]],
            '2^-44'   => [2.0 ** -44, ['5.684341886080802', -14, 15]],
            '2^-97'   => [2.0 ** -97, ['6.310887241768095', -30, 15]],
            '2^-1017' => [2.0 ** -1017, ['7.120236347223045', -307, 15]],
            '2^89'    => [2.0 ** 89, ['6.189700196426902', 26, 15]],
            '2^976'   => [2.0 ** 976, ['6.386688990511104', 293, 15]],
        ];
    }

    /**
     * @test         shortestDecimal of floats whose nearest decimal at the shortest length reads back
     * @dataProvider dataProviderForShortestDecimalOfOtherFloats
     * @param        float                    $x
     * @param        array{string, int, int} $expected
     */
    public function testShortestDecimalOfOtherFloats(float $x, array $expected): void
    {
        // When
        $result = Decimal::shortestDecimal($x);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's shortest round-tripping repr, as its significand d.ddd, its exponent and
     * the number of digits after the point:
     *   t = Decimal(repr(x)).normalize().as_tuple(); digits = ''.join(map(str, t.digits))
     *   [digits[0] + ('.' + digits[1:] if len(digits) > 1 else ''), t.exponent + len(digits) - 1, len(digits) - 1]
     */
    public static function dataProviderForShortestDecimalOfOtherFloats(): array
    {
        return [
            '0.1'                 => [0.1, ['1', -1, 0]],
            '0.3'                 => [0.3, ['3', -1, 0]],
            'smallest subnormal'  => [5e-324, ['5', -324, 0]],
            'largest float'       => [1.7976931348623157e308, ['1.7976931348623157', 308, 16]],
            '2^-76'               => [2.0 ** -76, ['1.3234889800848443', -23, 16]],
            '30.664096287827093'  => [30.664096287827093, ['3.0664096287827093', 1, 16]],
            '1e23'                => [1e23, ['1', 23, 0]],
            '2^54'                => [2.0 ** 54, ['1.8014398509481984', 16, 16]],
        ];
    }

    /**
     * @test         isShortestDecimal tells whether an integral float past 2^53 is the shortest decimal that reads back
     *               as it, also for decimals of more digits than a 32-bit int holds
     * @dataProvider dataProviderForIsShortestDecimal
     * @param        float $x
     * @param        bool  $expected
     */
    public function testIsShortestDecimal(float $x, bool $expected): void
    {
        // When
        $result = Decimal::isShortestDecimal($x);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, comparing the shortest round-tripping decimal with the float exactly:
     *   Fraction(repr(x)) == Fraction(x)
     */
    public static function dataProviderForIsShortestDecimal(): array
    {
        return [
            '2^53 + 2'                => [9007199254740994.0, true],
            '2^54'                    => [2.0 ** 54, true],
            '27021597764222988.0'     => [27021597764222988.0, true],
            '1e16'                    => [1e16, true],
            '1e22'                    => [1e22, true],
            '1.2345e20'               => [1.2345e20, true],
            '1.2345e21'               => [1.2345e21, false],
            '1e23'                    => [1e23, false],
            '2^60'                    => [2.0 ** 60, false],
            '2^63'                    => [2.0 ** 63, false],
            'largest float'           => [1.7976931348623157e308, false],
        ];
    }
}
