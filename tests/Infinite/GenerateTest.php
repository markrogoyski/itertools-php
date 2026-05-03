<?php

declare(strict_types=1);

namespace IterTools\Tests\Infinite;

use IterTools\Infinite;

class GenerateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test generate yields values from a counter closure
     */
    public function testCounterClosure(): void
    {
        // Given
        $i = 0;
        $supplier = function () use (&$i): int {
            return ++$i;
        };

        // When
        $result = [];
        foreach (Infinite::generate($supplier) as $value) {
            $result[] = $value;
            if (\count($result) === 5) {
                break;
            }
        }

        // Then
        $this->assertSame([1, 2, 3, 4, 5], $result);
    }

    /**
     * @test generate yields a constant when supplier is constant
     */
    public function testConstantSupplier(): void
    {
        // Given
        $supplier = fn (): string => 'x';

        // When
        $result = [];
        foreach (Infinite::generate($supplier) as $value) {
            $result[] = $value;
            if (\count($result) === 4) {
                break;
            }
        }

        // Then
        $this->assertSame(['x', 'x', 'x', 'x'], $result);
    }

    /**
     * @test generate preserves captured state between calls
     */
    public function testStatefulClosureState(): void
    {
        // Given a Fibonacci generator via two captured variables
        $a = 0;
        $b = 1;
        $supplier = function () use (&$a, &$b): int {
            $next = $a;
            [$a, $b] = [$b, $a + $b];
            return $next;
        };

        // When
        $result = [];
        foreach (Infinite::generate($supplier) as $value) {
            $result[] = $value;
            if (\count($result) === 7) {
                break;
            }
        }

        // Then
        $this->assertSame([0, 1, 1, 2, 3, 5, 8], $result);
    }

    /**
     * @test generate output keys are sequential 0-indexed
     */
    public function testKeysSequential(): void
    {
        // Given
        $supplier = fn (): int => 0;

        // When
        $keys = [];
        foreach (Infinite::generate($supplier) as $key => $value) {
            $keys[] = $key;
            if (\count($keys) === 4) {
                break;
            }
        }

        // Then
        $this->assertSame([0, 1, 2, 3], $keys);
    }
}
