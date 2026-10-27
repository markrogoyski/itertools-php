<?php

declare(strict_types=1);

namespace IterTools\Tests\Transform;

use IterTools\Single;
use IterTools\Transform;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\CountingIteratorAggregateFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;
use IterTools\Tests\Fixture\RecordingIteratorFixture;

class MemoizeTest extends \PHPUnit\Framework\TestCase
{
    /** @dataProvider replayableSourceProvider */
    public function testReplaysEverySupportedSource(iterable $source): void
    {
        // Given
        $memoized = Transform::memoize($source);

        // When
        $first = \iterator_to_array($memoized, false);
        $second = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame([1, 2, 3], $first);
        $this->assertSame($first, $second);
    }

    public static function replayableSourceProvider(): array
    {
        return [
            'array' => [[1, 2, 3]],
            'generator' => [(static function (): \Generator {
                yield from [1, 2, 3];
            })()],
            'iterator' => [new ArrayIteratorFixture([1, 2, 3])],
            'iterator aggregate' => [new IteratorAggregateFixture([1, 2, 3])],
        ];
    }

    public function testReplaysOneShotSourceAndPreservesDuplicateKeys(): void
    {
        // Given
        $pulls = 0;
        $source = (static function () use (&$pulls): \Generator {
            ++$pulls;
            yield 'a' => 1;
            ++$pulls;
            yield 'a' => 2;
        })();
        $memoized = Transform::memoize($source);

        // When
        $first = \iterator_to_array($memoized, false);
        $second = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame([1, 2], $first);
        $this->assertSame($first, $second);
        $this->assertSame(2, $pulls);
    }

    public function testIsIncrementallyLazyAndSupportsInfiniteSources(): void
    {
        // Given
        $pulls = 0;
        $source = (static function () use (&$pulls): \Generator {
            for ($value = 0;; ++$value) {
                ++$pulls;
                yield $value;
            }
        })();
        $memoized = Transform::memoize($source);
        $this->assertSame(0, $pulls);

        // When
        $first = \iterator_to_array(Single::limit($memoized, 3));
        $second = \iterator_to_array(Single::limit($memoized, 5));

        // Then
        $this->assertSame([0, 1, 2], $first);
        $this->assertSame([0, 1, 2, 3, 4], $second);
        $this->assertSame(5, $pulls);
    }

    public function testPreservesValuesKeysAndObjectIdentityPositionally(): void
    {
        // Given
        $object = new \stdClass();
        $source = (static function () use ($object): \Generator {
            yield 'same' => null;
            yield 'same' => ['array'];
            yield 9 => $object;
        })();
        $memoized = Transform::memoize($source);

        // When
        $tuples = [];
        foreach ($memoized as $key => $value) {
            $tuples[] = [$key, $value];
        }

        // Then
        $this->assertSame('same', $tuples[0][0]);
        $this->assertNull($tuples[0][1]);
        $this->assertSame(['same', ['array']], $tuples[1]);
        $this->assertSame($object, $tuples[2][1]);
    }

    public function testReplaysEmptySourceAndDoesNotReinitialize(): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture([]);
        $memoized = Transform::memoize($aggregate);

        // When
        $first = \iterator_to_array($memoized, false);
        $second = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame([], $first);
        $this->assertSame([], $second);
        $this->assertSame(1, $aggregate->getIteratorCallCount());
    }

    public function testConstructionDoesNotTouchIteratorAggregateSource(): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $memoized = Transform::memoize($aggregate);

        // Then
        $this->assertSame(0, $aggregate->getIteratorCallCount());

        // When
        $first = \iterator_to_array($memoized, false);
        $second = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame([1, 2, 3], $first);
        $this->assertSame([1, 2, 3], $second);
        $this->assertSame(1, $aggregate->getIteratorCallCount());
    }

    public function testCacheIsCompact(): void
    {
        // Given
        $n = 200_000;
        $source = (static function () use ($n): \Generator {
            for ($i = 0; $i < $n; ++$i) {
                yield $i;
            }
        })();
        $before = \memory_get_usage();
        $memoized = Transform::memoize($source);

        // When
        foreach ($memoized as $_) {
        }
        $retained = \memory_get_usage() - $before;

        // Then
        $this->assertLessThan(64 * $n, $retained);
    }

    public function testNullKeysAndValuesAreReplayed(): void
    {
        // Given
        $source = (static function (): \Generator {
            yield null => null;
            yield 1 => null;
            yield null => 2;
        })();
        $memoized = Transform::memoize($source);
        $expected = [[null, null], [1, null], [null, 2]];

        // When
        $first = [];
        foreach ($memoized as $key => $value) {
            $first[] = [$key, $value];
        }
        $second = [];
        foreach ($memoized as $key => $value) {
            $second[] = [$key, $value];
        }

        // Then
        $this->assertSame($expected, $first);
        $this->assertSame($expected, $second);
    }

    /** @dataProvider cloneSourceProvider */
    public function testCloneAfterPartialConsumptionSharesTheCache(iterable $source): void
    {
        // Given
        $memoized = Transform::memoize($source);
        $memoized->getIterator()->current();
        $clone = clone $memoized;

        // When
        $cloneHead = \iterator_to_array(Single::limit($clone, 2), false);
        $original = \iterator_to_array($memoized, false);
        $cloneFull = \iterator_to_array($clone, false);

        // Then
        $this->assertSame(['a', 'b'], $cloneHead);
        $this->assertSame(['a', 'b', 'c', 'd'], $original);
        $this->assertSame(['a', 'b', 'c', 'd'], $cloneFull);
    }

    /** @dataProvider cloneSourceProvider */
    public function testCloneBeforeFirstPullSharesTheCache(iterable $source): void
    {
        // Given
        $memoized = Transform::memoize($source);
        $clone = clone $memoized;

        // When
        $cloneHead = \iterator_to_array(Single::limit($clone, 2), false);
        $original = \iterator_to_array($memoized, false);
        $cloneFull = \iterator_to_array($clone, false);

        // Then
        $this->assertSame(['a', 'b'], $cloneHead);
        $this->assertSame(['a', 'b', 'c', 'd'], $original);
        $this->assertSame(['a', 'b', 'c', 'd'], $cloneFull);
    }

    /** @dataProvider cloneSourceProvider */
    public function testCloneAfterExhaustionReplaysTheCache(iterable $source): void
    {
        // Given
        $memoized = Transform::memoize($source);
        \iterator_to_array($memoized, false);
        $clone = clone $memoized;

        // When
        $original = \iterator_to_array($memoized, false);
        $cloned = \iterator_to_array($clone, false);

        // Then
        $this->assertSame(['a', 'b', 'c', 'd'], $original);
        $this->assertSame(['a', 'b', 'c', 'd'], $cloned);
    }

    public static function cloneSourceProvider(): array
    {
        return [
            'array' => [['a', 'b', 'c', 'd']],
            'generator' => [(static function (): \Generator {
                yield from ['a', 'b', 'c', 'd'];
            })()],
            'iterator' => [new ArrayIteratorFixture(['a', 'b', 'c', 'd'])],
            'iterator aggregate' => [new IteratorAggregateFixture(['a', 'b', 'c', 'd'])],
        ];
    }

    public function testInterleavedCloneAndOriginalPullEachSourceElementOnce(): void
    {
        // Given
        $pulls = 0;
        $source = (static function () use (&$pulls): \Generator {
            foreach (['a', 'b', 'c'] as $value) {
                ++$pulls;
                yield $value;
            }
        })();
        $memoized = Transform::memoize($source);
        $clone = clone $memoized;
        $original = $memoized->getIterator();
        $cloned = $clone->getIterator();

        // When / Then
        $this->assertSame('a', $cloned->current());
        $this->assertSame('a', $original->current());
        $original->next();
        $this->assertSame('b', $original->current());
        $cloned->next();
        $this->assertSame('b', $cloned->current());
        $cloned->next();
        $this->assertSame('c', $cloned->current());
        $original->next();
        $this->assertSame('c', $original->current());
        $this->assertSame(3, $pulls);
    }

    public function testCloneIsLazyAcrossHandles(): void
    {
        // Given
        $pulls = 0;
        $source = (static function () use (&$pulls): \Generator {
            for ($value = 0;; ++$value) {
                ++$pulls;
                yield $value;
            }
        })();
        $memoized = Transform::memoize($source);
        $clone = clone $memoized;

        // When
        $cloned = \iterator_to_array(Single::limit($clone, 2));
        $original = \iterator_to_array(Single::limit($memoized, 3));

        // Then
        $this->assertSame([0, 1], $cloned);
        $this->assertSame([0, 1, 2], $original);
        $this->assertSame(3, $pulls);
    }

    public function testCloneBeforeFirstPullRewindsIteratorSourceOnce(): void
    {
        // Given
        $pulled = [];
        $memoized = Transform::memoize(new RecordingIteratorFixture(['a', 'b', 'c'], $pulled));
        $clone = clone $memoized;

        // When
        $cloned = \iterator_to_array($clone, false);
        $original = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame(['a', 'b', 'c'], $cloned);
        $this->assertSame(['a', 'b', 'c'], $original);
        $this->assertSame(['a', 'b', 'c'], $pulled);
    }

    public function testCloneBeforeFirstPullReadsIteratorAggregateSourceOnce(): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture(['a', 'b', 'c']);
        $memoized = Transform::memoize($aggregate);
        $clone = clone $memoized;

        // When
        $cloned = \iterator_to_array($clone, false);
        $original = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame(['a', 'b', 'c'], $cloned);
        $this->assertSame(['a', 'b', 'c'], $original);
        $this->assertSame(1, $aggregate->getIteratorCallCount());
    }

    public function testCloneReplaysDuplicateKeysAndNullsPositionally(): void
    {
        // Given
        $source = (static function (): \Generator {
            yield 'k' => null;
            yield 'k' => 1;
            yield null => 2;
        })();
        $memoized = Transform::memoize($source);
        $memoized->getIterator()->current();
        $clone = clone $memoized;
        $expected = [['k', null], ['k', 1], [null, 2]];

        // When
        $cloned = [];
        foreach ($clone as $key => $value) {
            $cloned[] = [$key, $value];
        }
        $original = [];
        foreach ($memoized as $key => $value) {
            $original[] = [$key, $value];
        }

        // Then
        $this->assertSame($expected, $cloned);
        $this->assertSame($expected, $original);
    }

    public function testCloneBeforeSourceFailureReplaysTheSamePrefixAndException(): void
    {
        // Given
        $failure = new \RuntimeException('source failed');
        $source = (static function () use ($failure): \Generator {
            yield 'a';
            yield 'b';
            throw $failure;
        })();
        $memoized = Transform::memoize($source);
        $clone = clone $memoized;

        // When
        [$originalValues, $originalFailure] = self::captureValuesAndFailure($memoized);
        [$clonedValues, $clonedFailure] = self::captureValuesAndFailure($clone);

        // Then
        $this->assertSame(['a', 'b'], $originalValues);
        $this->assertSame(['a', 'b'], $clonedValues);
        $this->assertSame($failure, $originalFailure);
        $this->assertSame($failure, $clonedFailure);
    }

    public function testCloneReentrantAdvanceThrowsTheSharedGuardFailure(): void
    {
        // Given
        $clone = null;
        $source = (static function () use (&$clone): \Generator {
            foreach ($clone as $_value) {
            }
            yield 1;
        })();
        $memoized = Transform::memoize($source);
        $clone = clone $memoized;

        // When
        [$originalValues, $originalFailure] = self::captureValuesAndFailure($memoized);
        [$clonedValues, $clonedFailure] = self::captureValuesAndFailure($clone);

        // Then
        $this->assertSame([], $originalValues);
        $this->assertSame([], $clonedValues);
        $this->assertInstanceOf(\LogicException::class, $originalFailure);
        $this->assertSame('Memoized iterable cannot advance its source re-entrantly', $originalFailure->getMessage());
        $this->assertSame($originalFailure, $clonedFailure);
    }

    /** @return array{0: list<mixed>, 1: \Throwable} */
    private static function captureValuesAndFailure(iterable $memoized): array
    {
        $values = [];
        try {
            foreach ($memoized as $value) {
                $values[] = $value;
            }
        } catch (\Throwable $failure) {
            return [$values, $failure];
        }

        throw new \LogicException('Expected memoized source to fail');
    }
}
