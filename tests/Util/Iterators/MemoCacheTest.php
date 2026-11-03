<?php

declare(strict_types=1);

namespace IterTools\Tests\Util\Iterators;

use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\CountingIteratorAggregateFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;
use IterTools\Util\Iterators\MemoCache;

class MemoCacheTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test replay yields every key/value pair from the start, on every replay
     * @dataProvider sourceProvider
     */
    public function testEveryReplayStartsFromTheBeginning(iterable $source): void
    {
        // Given
        $cache = new MemoCache($source);

        // When
        $first = self::pairs($cache->replay());
        $second = self::pairs($cache->replay());

        // Then
        $this->assertSame([[0, 'a'], [1, null], [2, 'c']], $first);
        $this->assertSame($first, $second);
    }

    public static function sourceProvider(): array
    {
        return [
            'array' => [['a', null, 'c']],
            'generator' => [(static function (): \Generator {
                yield from ['a', null, 'c'];
            })()],
            'iterator' => [new ArrayIteratorFixture(['a', null, 'c'])],
            'iterator aggregate' => [new IteratorAggregateFixture(['a', null, 'c'])],
        ];
    }

    /**
     * @test duplicate and null keys are cached positionally
     */
    public function testDuplicateAndNullKeysAreCachedPositionally(): void
    {
        // Given
        $source = (static function (): \Generator {
            yield 'k' => 1;
            yield 'k' => 2;
            yield null => 3;
        })();
        $cache = new MemoCache($source);

        // When
        $first = self::pairs($cache->replay());
        $second = self::pairs($cache->replay());

        // Then
        $this->assertSame([['k', 1], ['k', 2], [null, 3]], $first);
        $this->assertSame($first, $second);
    }

    /**
     * @test each key is read after its value, as foreach does, so a source whose current() moves its key caches
     *       the key foreach sees
     */
    public function testKeyIsReadAfterValue(): void
    {
        // Given
        $source = self::skippingIterator(['a', '', 'b']);
        $expected = [];
        foreach (self::skippingIterator(['a', '', 'b']) as $key => $value) {
            $expected[] = [$key, $value];
        }
        $cache = new MemoCache($source);

        // When
        $first = self::pairs($cache->replay());
        $second = self::pairs($cache->replay());

        // Then
        $this->assertSame([[0, 'a'], [2, 'b']], $expected);
        $this->assertSame($expected, $first);
        $this->assertSame($expected, $second);
    }

    /**
     * @test neither construction nor an unstarted replay touches the source
     */
    public function testConstructionAndUnstartedReplayDoNotTouchSource(): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $cache = new MemoCache($aggregate);
        $replay = $cache->replay();

        // Then
        $this->assertSame(0, $aggregate->getIteratorCallCount());

        // When
        $replay->current();

        // Then
        $this->assertSame(1, $aggregate->getIteratorCallCount());
    }

    /**
     * @test interleaved replays advance one shared frontier, pulling each source element once
     */
    public function testInterleavedReplaysShareOneFrontier(): void
    {
        // Given
        $pulls = 0;
        $source = (static function () use (&$pulls): \Generator {
            foreach (['a', 'b', 'c'] as $value) {
                ++$pulls;
                yield $value;
            }
        })();
        $cache = new MemoCache($source);
        $first = $cache->replay();
        $second = $cache->replay();

        // When / Then
        $this->assertSame('a', $first->current());
        $first->next();
        $this->assertSame('b', $first->current());
        $this->assertSame(2, $pulls);
        $this->assertSame('a', $second->current());
        $second->next();
        $this->assertSame('b', $second->current());
        $this->assertSame(2, $pulls);
        $second->next();
        $this->assertSame('c', $second->current());
        $first->next();
        $this->assertSame('c', $first->current());
        $this->assertSame(3, $pulls);
    }

    /**
     * @test a cached null value is replayed from the cache, not pulled again
     */
    public function testCachedNullValueIsNotAGap(): void
    {
        // Given
        $pulls = 0;
        $source = (static function () use (&$pulls): \Generator {
            foreach ([null, null, 'c'] as $value) {
                ++$pulls;
                yield $value;
            }
        })();
        $cache = new MemoCache($source);
        $leader = $cache->replay();
        $leader->current();
        $leader->next();

        // When
        $follower = $cache->replay();
        $first = $follower->current();
        $follower->next();
        $second = $follower->current();

        // Then
        $this->assertNull($first);
        $this->assertNull($second);
        $this->assertTrue($follower->valid());
        $this->assertSame(2, $pulls);
    }

    /**
     * @test an empty source is initialized once and every replay yields nothing
     */
    public function testEmptySourceIsInitializedOnce(): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture([]);
        $cache = new MemoCache($aggregate);

        // When
        $first = self::pairs($cache->replay());
        $second = self::pairs($cache->replay());

        // Then
        $this->assertSame([], $first);
        $this->assertSame([], $second);
        $this->assertSame(1, $aggregate->getIteratorCallCount());
    }

    /**
     * @test once exhausted, replays do not advance the source again
     */
    public function testExhaustedSourceIsNotAdvancedAgain(): void
    {
        // Given
        $source = self::countingIterator(['a', 'b']);
        $cache = new MemoCache($source);
        self::pairs($cache->replay());
        $nextCalls = $source->nextCalls;

        // When
        $replayed = self::pairs($cache->replay());

        // Then
        $this->assertSame([[0, 'a'], [1, 'b']], $replayed);
        $this->assertSame(2, $nextCalls);
        $this->assertSame($nextCalls, $source->nextCalls);
    }

    /**
     * @test the original iterable is released once the source iterator is obtained
     */
    public function testReleasesIterableAfterInitialization(): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture([1, 2]);
        $reference = \WeakReference::create($aggregate);
        $cache = new MemoCache($aggregate);
        unset($aggregate);

        // When
        $cache->replay()->current();

        // Then
        $this->assertNull($reference->get());
        $this->assertSame([[0, 1], [1, 2]], self::pairs($cache->replay()));
    }

    /**
     * @test a source failure is replayed after the successful prefix, and a replay of the prefix alone does not throw
     */
    public function testFailureIsReplayedAfterTheSuccessfulPrefix(): void
    {
        // Given
        $failure = new \RuntimeException('source failed');
        $source = (static function () use ($failure): \Generator {
            yield 'a';
            yield 'b';
            throw $failure;
        })();
        $cache = new MemoCache($source);
        [$values, $thrown] = self::captureValuesAndFailure($cache->replay());

        // When
        $prefix = $cache->replay();
        $prefixValues = [$prefix->current()];
        $prefix->next();
        $prefixValues[] = $prefix->current();
        [$replayedValues, $replayedFailure] = self::captureValuesAndFailure($cache->replay());

        // Then
        $this->assertSame(['a', 'b'], $values);
        $this->assertSame($failure, $thrown);
        $this->assertSame(['a', 'b'], $prefixValues);
        $this->assertSame(['a', 'b'], $replayedValues);
        $this->assertSame($failure, $replayedFailure);
    }

    /**
     * @test a source that replays its own cache trips the re-entrancy guard, and the failure is cached
     */
    public function testReentrantReplayThrowsTheCachedGuardFailure(): void
    {
        // Given
        $cache = null;
        $source = (static function () use (&$cache): \Generator {
            yield 'a';
            foreach ($cache->replay() as $_value) {
            }
            yield 'b';
        })();
        $cache = new MemoCache($source);

        // When
        [$firstValues, $firstFailure] = self::captureValuesAndFailure($cache->replay());
        [$secondValues, $secondFailure] = self::captureValuesAndFailure($cache->replay());

        // Then
        $this->assertSame(['a'], $firstValues);
        $this->assertSame(['a'], $secondValues);
        $this->assertInstanceOf(\LogicException::class, $firstFailure);
        $this->assertSame('Memoized iterable cannot advance its source re-entrantly', $firstFailure->getMessage());
        $this->assertSame($firstFailure, $secondFailure);
    }

    /** @return list<array{0: mixed, 1: mixed}> */
    private static function pairs(iterable $replay): array
    {
        $pairs = [];
        foreach ($replay as $key => $value) {
            $pairs[] = [$key, $value];
        }

        return $pairs;
    }

    /** @return array{0: list<mixed>, 1: \Throwable} */
    private static function captureValuesAndFailure(iterable $replay): array
    {
        $values = [];
        try {
            foreach ($replay as $value) {
                $values[] = $value;
            }
        } catch (\Throwable $failure) {
            return [$values, $failure];
        }

        throw new \LogicException('Expected memoized source to fail');
    }

    /**
     * An iterator whose current() skips empty rows and so moves its key, as SplFileObject does with SKIP_EMPTY and
     * without READ_AHEAD.
     */
    private static function skippingIterator(array $rows): \Iterator
    {
        return new class ($rows) implements \Iterator {
            private int $position = 0;

            public function __construct(private array $rows)
            {
            }

            public function rewind(): void
            {
                $this->position = 0;
            }

            public function valid(): bool
            {
                return $this->position < \count($this->rows);
            }

            public function key(): int
            {
                return $this->position;
            }

            public function current(): mixed
            {
                while ($this->rows[$this->position] === '' && $this->position + 1 < \count($this->rows)) {
                    ++$this->position;
                }
                return $this->rows[$this->position];
            }

            public function next(): void
            {
                ++$this->position;
            }
        };
    }

    private static function countingIterator(array $values): \Iterator
    {
        return new class ($values) implements \Iterator {
            public int $nextCalls = 0;
            private int $position = 0;

            public function __construct(private array $values)
            {
            }

            public function rewind(): void
            {
                $this->position = 0;
            }

            public function valid(): bool
            {
                return $this->position < \count($this->values);
            }

            public function key(): int
            {
                return $this->position;
            }

            public function current(): mixed
            {
                return $this->values[$this->position];
            }

            public function next(): void
            {
                ++$this->nextCalls;
                ++$this->position;
            }
        };
    }
}
