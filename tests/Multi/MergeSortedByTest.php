<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi;

use IterTools\Multi;
use IterTools\Tests\Fixture\CountingIteratorAggregateFixture;

class MergeSortedByTest extends \PHPUnit\Framework\TestCase
{
    public function testProjectsOnceAndPreservesStableSourceOrder(): void
    {
        // Given
        $calls = [];
        $first = [(object) ['rank' => 1, 'id' => 'a'], (object) ['rank' => 1, 'id' => 'b']];
        $second = [(object) ['rank' => 1, 'id' => 'c'], (object) ['rank' => 2, 'id' => 'd']];

        // When
        $result = \iterator_to_array(Multi::mergeSortedBy(
            static function (object $item) use (&$calls): int {
                $calls[] = $item->id;
                return $item->rank;
            },
            $first,
            $second,
        ));

        // Then
        $this->assertSame(['a', 'b', 'c', 'd'], \array_map(static fn (object $item): string => $item->id, $result));
        $this->assertCount(4, $calls);
        $this->assertSame([0, 1, 2, 3], \array_keys($result));
    }

    public function testMergesStringsByLength(): void
    {
        // When
        $result = \iterator_to_array(Multi::mergeSortedBy('\strlen', ['a', 'bbb'], ['cc', 'dddd']));

        // Then
        $this->assertSame(['a', 'cc', 'bbb', 'dddd'], $result);
    }

    public function testYieldsExactPrefixBeforeRejectingLaterProjectedNan(): void
    {
        // Given
        $merged = Multi::mergeSortedBy(static fn (float $value): float => $value, [1.0, \NAN], [2.0]);
        $yielded = [];

        // When
        try {
            foreach ($merged as $value) {
                $yielded[] = $value;
            }
            $this->fail('Expected projected NAN to be rejected');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('Multi::mergeSortedBy key function returned NAN', $e->getMessage());
        }

        // Then
        $this->assertSame([1.0], $yielded);
    }

    public function testYieldsNothingWhenSeededProjectedKeyIsNan(): void
    {
        // Given
        $merged = Multi::mergeSortedBy(static fn (float $value): float => $value, [1.0], [\NAN]);
        $yielded = [];

        // When
        try {
            foreach ($merged as $value) {
                $yielded[] = $value;
            }
            $this->fail('Expected projected NAN to be rejected');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('Multi::mergeSortedBy key function returned NAN', $e->getMessage());
        }

        // Then
        $this->assertSame([], $yielded);
    }

    /**
     * @test the NAN guard only checks a key that is itself NAN: a NAN nested in a composite key is not
     *       detected, so nothing throws (the merge order is unspecified; see README "NaN and Ordering")
     */
    public function testDoesNotRejectNanNestedInCompositeKey(): void
    {
        // Given
        $merged = Multi::mergeSortedBy(
            static fn (array $pair): array => $pair,
            [[1.0, 'a'], [\NAN, 'b']],
            [[2.0, 'c']],
        );

        // When
        $ids = \array_map(static fn (array $pair): string => $pair[1], \iterator_to_array($merged, false));

        // Then
        \sort($ids);
        $this->assertSame(['a', 'b', 'c'], $ids);
    }

    public function testPropagatesKeyFunctionExceptionWithoutWrapping(): void
    {
        // Given
        $exception = new \RuntimeException('key function failed');
        $keyFunc = static function (int $value) use ($exception): int {
            if ($value === 2) {
                throw $exception;
            }
            return $value;
        };
        $merged = Multi::mergeSortedBy($keyFunc, [1, 2], [3]);
        $yielded = [];

        // When
        try {
            foreach ($merged as $value) {
                $yielded[] = $value;
            }
            $this->fail('Expected key function exception to propagate');
        } catch (\RuntimeException $e) {
            // Then
            $this->assertSame($exception, $e);
            $this->assertNull($e->getPrevious());
        }

        // Then
        $this->assertSame([1], $yielded);
    }

    public function testPropagatesSourceExceptionWithoutWrapping(): void
    {
        // Given
        $exception = new \RuntimeException('source failed');
        $failing = (static function () use ($exception): \Generator {
            yield 1;
            throw $exception;
        })();
        $merged = Multi::mergeSortedBy(static fn (int $value): int => $value, $failing, [2]);
        $yielded = [];

        // When
        try {
            foreach ($merged as $value) {
                $yielded[] = $value;
            }
            $this->fail('Expected source exception to propagate');
        } catch (\RuntimeException $e) {
            // Then
            $this->assertSame($exception, $e);
            $this->assertNull($e->getPrevious());
        }

        // Then
        $this->assertSame([1], $yielded);
    }

    public function testPreservesSourceOrderWithNamedArguments(): void
    {
        // Given
        $alpha = [[1, 'a1'], [1, 'a2']];
        $beta = [[1, 'b1'], [1, 'b2']];
        $gamma = [[1, 'c1']];

        // When
        $result = \iterator_to_array(Multi::mergeSortedBy(
            static fn (array $item): int => $item[0],
            alpha: $alpha,
            beta: $beta,
            gamma: $gamma,
        ));

        // Then
        $this->assertSame(['a1', 'a2', 'b1', 'b2', 'c1'], \array_column($result, 1));
    }

    public function testDoesNotTouchIteratorAggregateUntilAdvanced(): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture([1, 2]);

        // When
        $merged = Multi::mergeSortedBy(static fn (int $value): int => $value, $aggregate, [3]);

        // Then
        $this->assertSame(0, $aggregate->getIteratorCallCount());

        // When
        $result = \iterator_to_array($merged);

        // Then
        $this->assertSame([1, 2, 3], $result);
        $this->assertSame(1, $aggregate->getIteratorCallCount());
    }

    /**
     * @test mergeSortedBy accepts named arguments that match its internal parameter names
     */
    public function testMergeSortedByAcceptsNamedArgumentsMatchingInternalParameterNames(): void
    {
        // When
        $result = \iterator_to_array(Multi::mergeSortedBy(
            static fn (int $value): int => $value,
            nanMessage: [1, 4],
            other: [2, 3],
        ), false);

        // Then
        $this->assertSame([1, 2, 3, 4], $result);
    }

    /**
     * @test         mergeSortedBy merges int keys beyond 2^53 and float keys by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array<array<mixed>> $sources
     * @param        array<mixed>        $expected
     */
    public function testIntAndFloatBeyondFloatPrecision(array $sources, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = \iterator_to_array(Multi::mergeSortedBy(static fn (array $item) => $item['v'], ...$sources), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which compares an int and a float by exact value
     * (tools/oracles/exact_order_heaps.py):
     *   list(heapq.merge(*sources, key=lambda d: d['v']))
     * Each int compares equal as a float to a float it is not equal to, so <=> orders them wrongly.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecision(): array
    {
        return [
            'int key 2^53 + 1 in the first source, above float key 2.0^53 in the second' => [
                [[['v' => 9007199254740993]], [['v' => 9007199254740992.0]]],
                [['v' => 9007199254740992.0], ['v' => 9007199254740993]],
            ],
            'negative float key -2.0^53 in the first source, above int key -(2^53 + 1) in the second' => [
                [[['v' => -9007199254740992.0]], [['v' => -9007199254740993]]],
                [['v' => -9007199254740993], ['v' => -9007199254740992.0]],
            ],
        ];
    }
}
