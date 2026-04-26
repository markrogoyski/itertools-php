<?php

declare(strict_types=1);

namespace IterTools\Tests\Infinite;

use IterTools\Infinite;
use IterTools\Single;

class IterateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         iterate produces expected sequence
     * @dataProvider dataProviderForIterate
     * @param        mixed    $initial
     * @param        callable $function
     * @param        int      $take
     * @param        array    $expected
     */
    public function testIterate(mixed $initial, callable $function, int $take, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::limit(Infinite::iterate($initial, $function), $take) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForIterate(): array
    {
        return [
            // Powers of 2
            [
                1,
                fn ($x) => $x * 2,
                5,
                [1, 2, 4, 8, 16],
            ],
            // Increment from zero
            [
                0,
                fn ($x) => $x + 1,
                6,
                [0, 1, 2, 3, 4, 5],
            ],
            // String concatenation
            [
                'a',
                fn (string $s) => $s . 'a',
                3,
                ['a', 'aa', 'aaa'],
            ],
            // Array (tuple-like) state
            [
                [0, 0],
                fn (array $p) => [$p[0] + 1, $p[1] - 1],
                3,
                [[0, 0], [1, -1], [2, -2]],
            ],
            // Float
            [
                1.0,
                fn (float $x) => $x / 2,
                4,
                [1.0, 0.5, 0.25, 0.125],
            ],
            // Take only the initial value
            [
                42,
                fn ($x) => $x + 1,
                1,
                [42],
            ],
            // Negative numbers
            [
                -1,
                fn ($x) => $x - 1,
                4,
                [-1, -2, -3, -4],
            ],
        ];
    }

    /**
     * @test iterate yields the initial value first
     */
    public function testIterateInitialFirst(): void
    {
        // Given
        $initial  = 100;
        $function = fn ($x) => $x * 10;

        // When
        $first = null;
        foreach (Single::limit(Infinite::iterate($initial, $function), 1) as $item) {
            $first = $item;
        }

        // Then
        $this->assertSame($initial, $first);
    }

    /**
     * @test iterate function is applied repeatedly to its previous output
     */
    public function testIterateFunctionAppliedToPreviousOutput(): void
    {
        // Given
        $function = fn (int $x) => $x + 3;

        // When
        $result = [];
        foreach (Single::limit(Infinite::iterate(0, $function), 5) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertSame([0, 3, 6, 9, 12], $result);
    }

    /**
     * @test iterate does not terminate when function returns null
     */
    public function testIterateContinuesPastNull(): void
    {
        // Given
        $function = function ($x) {
            if ($x === null) {
                return 'after-null';
            }
            return null;
        };

        // When
        $result = [];
        foreach (Single::limit(Infinite::iterate('start', $function), 4) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertSame(['start', null, 'after-null', null], $result);
    }

    /**
     * @test iterate Collatz-like sequence
     */
    public function testIterateCollatz(): void
    {
        // Given
        $collatz = fn (int $n) => $n % 2 === 0 ? \intdiv($n, 2) : 3 * $n + 1;

        // When
        $result = [];
        foreach (Single::limit(Infinite::iterate(6, $collatz), 9) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertSame([6, 3, 10, 5, 16, 8, 4, 2, 1], $result);
    }

    /**
     * @test iterate is lazy: pulling 1000 items does not exhaust memory
     */
    public function testIterateLazyOverLargeCount(): void
    {
        // Given
        $function = fn (int $x) => $x + 1;
        $count    = 0;
        $last     = null;

        // When
        foreach (Single::limit(Infinite::iterate(0, $function), 1000) as $item) {
            $count++;
            $last = $item;
        }

        // Then
        $this->assertSame(1000, $count);
        $this->assertSame(999, $last);
    }

    /**
     * @test iterate with manual break is infinite (would never terminate without break)
     */
    public function testIterateInfiniteRequiresExternalLimit(): void
    {
        // Given
        $function = fn (int $x) => $x + 1;
        $result   = [];

        // When
        $i = 0;
        foreach (Infinite::iterate(10, $function) as $item) {
            $result[] = $item;
            if (++$i >= 4) {
                break;
            }
        }

        // Then
        $this->assertSame([10, 11, 12, 13], $result);
    }

    /**
     * @test iterate function receiving complex state
     */
    public function testIterateFibonacci(): void
    {
        // Given: Fibonacci as a pair (a, b) -> (b, a+b)
        $function = fn (array $pair) => [$pair[1], $pair[0] + $pair[1]];

        // When
        $result = [];
        foreach (Single::limit(Infinite::iterate([0, 1], $function), 7) as $pair) {
            $result[] = $pair[0];
        }

        // Then
        $this->assertSame([0, 1, 1, 2, 3, 5, 8], $result);
    }
}
