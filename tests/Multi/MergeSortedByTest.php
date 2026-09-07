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
}
