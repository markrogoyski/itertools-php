<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class AccumulateTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test accumulate example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $numbers = [1, 2, 3, 4, 5];

        // When
        $result = [];
        foreach (Single::accumulate($numbers, fn ($a, $b) => $a + $b) as $running) {
            $result[] = $running;
        }

        // Then
        $this->assertSame([1, 3, 6, 10, 15], $result);
    }

    /**
     * @test         accumulate without initial (array)
     * @dataProvider dataProviderForAccumulate
     * @param        array<mixed> $data
     * @param        callable     $op
     * @param        array<mixed> $expected
     */
    public function testAccumulateArray(array $data, callable $op, array $expected): void
    {
        // When
        $result = [];
        foreach (Single::accumulate($data, $op) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         accumulate without initial (Generator)
     * @dataProvider dataProviderForAccumulate
     * @param        array<mixed> $data
     * @param        callable     $op
     * @param        array<mixed> $expected
     */
    public function testAccumulateGenerator(array $data, callable $op, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = [];
        foreach (Single::accumulate($iterable, $op) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         accumulate without initial (Iterator)
     * @dataProvider dataProviderForAccumulate
     * @param        array<mixed> $data
     * @param        callable     $op
     * @param        array<mixed> $expected
     */
    public function testAccumulateIterator(array $data, callable $op, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = [];
        foreach (Single::accumulate($iterable, $op) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         accumulate without initial (IteratorAggregate)
     * @dataProvider dataProviderForAccumulate
     * @param        array<mixed> $data
     * @param        callable     $op
     * @param        array<mixed> $expected
     */
    public function testAccumulateIteratorAggregate(array $data, callable $op, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = [];
        foreach (Single::accumulate($iterable, $op) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForAccumulate(): array
    {
        $sum  = fn ($a, $b) => $a + $b;
        $prod = fn ($a, $b) => $a * $b;
        $max  = fn ($a, $b) => \max($a, $b);
        $cat  = fn ($a, $b) => $a . $b;

        return [
            // single element, no initial — yields that element unchanged
            [[7], $sum, [7]],
            // running sum of integers
            [[1, 2, 3, 4, 5], $sum, [1, 3, 6, 10, 15]],
            // running product of integers
            [[1, 2, 3, 4, 5], $prod, [1, 2, 6, 24, 120]],
            // running max
            [[3, 1, 4, 1, 5, 9, 2, 6], $max, [3, 3, 4, 4, 5, 9, 9, 9]],
            // string concatenation
            [['a', 'b', 'c'], $cat, ['a', 'ab', 'abc']],
            // single string element
            [['only'], $cat, ['only']],
        ];
    }

    /**
     * @test         accumulate with initial (array)
     * @dataProvider dataProviderForAccumulateWithInitial
     * @param        array<mixed> $data
     * @param        callable     $op
     * @param        mixed        $initial
     * @param        array<mixed> $expected
     */
    public function testAccumulateWithInitialArray(array $data, callable $op, mixed $initial, array $expected): void
    {
        // When
        $result = [];
        foreach (Single::accumulate($data, $op, $initial) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         accumulate with initial (Generator)
     * @dataProvider dataProviderForAccumulateWithInitial
     * @param        array<mixed> $data
     * @param        callable     $op
     * @param        mixed        $initial
     * @param        array<mixed> $expected
     */
    public function testAccumulateWithInitialGenerator(array $data, callable $op, mixed $initial, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = [];
        foreach (Single::accumulate($iterable, $op, $initial) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         accumulate with initial (Iterator)
     * @dataProvider dataProviderForAccumulateWithInitial
     * @param        array<mixed> $data
     * @param        callable     $op
     * @param        mixed        $initial
     * @param        array<mixed> $expected
     */
    public function testAccumulateWithInitialIterator(array $data, callable $op, mixed $initial, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = [];
        foreach (Single::accumulate($iterable, $op, $initial) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         accumulate with initial (IteratorAggregate)
     * @dataProvider dataProviderForAccumulateWithInitial
     * @param        array<mixed> $data
     * @param        callable     $op
     * @param        mixed        $initial
     * @param        array<mixed> $expected
     */
    public function testAccumulateWithInitialIteratorAggregate(
        array $data,
        callable $op,
        mixed $initial,
        array $expected
    ): void {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = [];
        foreach (Single::accumulate($iterable, $op, $initial) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForAccumulateWithInitial(): array
    {
        $sum  = fn ($a, $b) => $a + $b;
        $prod = fn ($a, $b) => $a * $b;
        $max  = fn ($a, $b) => \max($a, $b);
        $cat  = fn ($a, $b) => $a . $b;

        return [
            // single element with initial — yields initial, then op(initial, element)
            [[7], $sum, 100, [100, 107]],
            // running sum with initial
            [[1, 2, 3, 4, 5], $sum, 100, [100, 101, 103, 106, 110, 115]],
            // running product with initial
            [[2, 3, 4], $prod, 10, [10, 20, 60, 240]],
            // running max starting lower than every element
            [[3, 1, 4, 1, 5], $max, 0, [0, 3, 3, 4, 4, 5]],
            // string concatenation with prefix
            [['a', 'b', 'c'], $cat, '>', ['>', '>a', '>ab', '>abc']],
        ];
    }

    /**
     * @test         accumulate empty iterable without initial yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableNoInitial(iterable $data): void
    {
        // When
        $result = [];
        foreach (Single::accumulate($data, fn ($a, $b) => $a + $b) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test         accumulate empty iterable with initial yields only the initial
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableWithInitial(iterable $data): void
    {
        // When
        $result = [];
        foreach (Single::accumulate($data, fn ($a, $b) => $a + $b, 42) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame([42], $result);
    }

    /**
     * @test accumulate treats explicit null as a legitimate initial value
     */
    public function testExplicitNullInitial(): void
    {
        // Given
        $data = [1, 2, 3];
        $op   = fn (mixed $a, int $b): int => ($a ?? 0) + $b;

        // When
        $result = [];
        foreach (Single::accumulate($data, $op, null) as $value) {
            $result[] = $value;
        }

        // Then
        // First element is the explicit null initial; subsequent values are op(acc, element).
        $this->assertSame([null, 1, 3, 6], $result);
    }

    /**
     * @test accumulate with explicit null initial on empty iterable yields single null
     */
    public function testExplicitNullInitialOnEmpty(): void
    {
        // Given
        $data = [];

        // When
        $result = [];
        foreach (Single::accumulate($data, fn ($a, $b) => $a, null) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame([null], $result);
    }

    /**
     * @test accumulate throws InvalidArgumentException when more than one initial is given
     */
    public function testTwoInitialsThrows(): void
    {
        // Given
        $data = [1, 2, 3];

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        // @phpstan-ignore-next-line argument.unresolvableType
        $generator = Single::accumulate($data, fn ($a, $b) => $a + $b, 0, 1);
        // Force evaluation in case the generator is lazy.
        foreach ($generator as $_) {
            // no-op
        }
    }

    /**
     * @test accumulate is lazy — does not consume more of the iterator than requested
     */
    public function testLazyEvaluation(): void
    {
        // Given
        $yielded = [];
        $source = (function () use (&$yielded): \Generator {
            foreach ([1, 2, 3, 4, 5] as $n) {
                $yielded[] = $n;
                yield $n;
            }
        })();

        // When
        $taken = [];
        foreach (Single::accumulate($source, fn ($a, $b) => $a + $b) as $value) {
            $taken[] = $value;
            if (\count($taken) === 3) {
                break;
            }
        }

        // Then
        $this->assertSame([1, 3, 6], $taken);
        $this->assertSame([1, 2, 3], $yielded);
    }
}
