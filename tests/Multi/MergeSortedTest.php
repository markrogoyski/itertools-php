<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi;

use IterTools\Multi;
use IterTools\Single;
use IterTools\Infinite;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\CountingIteratorAggregateFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class MergeSortedTest extends \PHPUnit\Framework\TestCase
{
    public function testHandlesNoSourcesAndEmptySources(): void
    {
        // When
        $none = \iterator_to_array(Multi::mergeSorted());
        $empty = \iterator_to_array(Multi::mergeSorted([], GeneratorFixture::getGenerator([])));

        // Then
        $this->assertSame([], $none);
        $this->assertSame([], $empty);
    }

    public function testCombinesEverySupportedIterableType(): void
    {
        // Given
        $array = [-4, 4];
        $generator = GeneratorFixture::getGenerator([-3, 1.5]);
        $iterator = new ArrayIteratorFixture([-2, 2]);
        $aggregate = new IteratorAggregateFixture([-1, 3]);

        // When
        $result = \iterator_to_array(Multi::mergeSorted($array, $generator, $iterator, $aggregate));

        // Then
        $this->assertSame([-4, -3, -2, -1, 1.5, 2, 3, 4], $result);
    }

    public function testMergesStablyAndDiscardsKeys(): void
    {
        // Given
        $first = ['a' => 1, 'b' => 2, 'c' => 2];
        $second = ['d' => 2, 'e' => 3];

        // When
        $result = \iterator_to_array(Multi::mergeSorted($first, $second));

        // Then
        $this->assertSame([1, 2, 2, 2, 3], $result);
    }

    public function testIsLazyAndSupportsInfiniteInputs(): void
    {
        // Given
        $pulls = 0;
        $source = (static function () use (&$pulls): \Generator {
            ++$pulls;
            yield 0;
            ++$pulls;
            yield 2;
        })();
        $merged = Multi::mergeSorted($source, [1, 3]);

        // When
        $result = \iterator_to_array(Single::limit($merged, 1));

        // Then
        $this->assertSame([0], $result);
        $this->assertSame(1, $pulls);
    }

    public function testYieldsExactPrefixBeforeRejectingLaterNan(): void
    {
        // Given
        $merged = Multi::mergeSorted([1, \NAN], [2]);
        $yielded = [];

        // When
        try {
            foreach ($merged as $value) {
                $yielded[] = $value;
            }
            $this->fail('Expected NAN to be rejected');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('Multi::mergeSorted cannot order NAN', $e->getMessage());
        }

        // Then
        $this->assertSame([1], $yielded);
    }

    public function testYieldsNothingWhenSeededValueIsNan(): void
    {
        // Given
        $merged = Multi::mergeSorted([1], [\NAN]);
        $yielded = [];

        // When
        try {
            foreach ($merged as $value) {
                $yielded[] = $value;
            }
            $this->fail('Expected NAN to be rejected');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('Multi::mergeSorted cannot order NAN', $e->getMessage());
        }

        // Then
        $this->assertSame([], $yielded);
    }

    public function testPreservesSourceOrderWithNamedArguments(): void
    {
        // Given
        $alpha = [new \stdClass(), new \stdClass()];
        $beta = [new \stdClass(), new \stdClass()];
        $gamma = [new \stdClass()];

        // When
        $result = \iterator_to_array(Multi::mergeSorted(alpha: $alpha, beta: $beta, gamma: $gamma));

        // Then
        $this->assertSame([$alpha[0], $alpha[1], $beta[0], $beta[1], $gamma[0]], $result);
    }

    public function testDoesNotTouchIteratorAggregateUntilAdvanced(): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture([1, 2]);

        // When
        $merged = Multi::mergeSorted($aggregate, [3]);

        // Then
        $this->assertSame(0, $aggregate->getIteratorCallCount());

        // When
        $result = \iterator_to_array($merged);

        // Then
        $this->assertSame([1, 2, 3], $result);
        $this->assertSame(1, $aggregate->getIteratorCallCount());
    }

    public function testPropagatesSourceExceptionWithoutWrapping(): void
    {
        // Given
        $exception = new \RuntimeException('source failed');
        $failing = (static function () use ($exception): \Generator {
            yield 1;
            throw $exception;
        })();
        $merged = Multi::mergeSorted($failing, [2]);
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

    public function testMergesInfiniteSources(): void
    {
        // When
        $result = \iterator_to_array(Single::limit(
            Multi::mergeSorted(Infinite::count(0, 2), Infinite::count(1, 2)),
            10,
        ));

        // Then
        $this->assertSame(\range(0, 9), $result);
    }
}
