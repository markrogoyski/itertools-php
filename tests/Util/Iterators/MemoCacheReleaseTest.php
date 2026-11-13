<?php

declare(strict_types=1);

namespace IterTools\Tests\Util\Iterators;

use IterTools\Infinite;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\WrappingIteratorAggregateFixture;
use IterTools\Transform;
use IterTools\Util\Iterators\MemoCache;

/**
 * The sources are built in each test rather than in a data provider, which would keep them alive.
 */
class MemoCacheReleaseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test a fully read \ArrayIterator source is released, and the cache still replays it
     */
    public function testExhaustedSourceIsReleasedArrayIterator(): void
    {
        // Given
        $source = new \ArrayIterator(['a', 'b', 'c']);
        $reference = \WeakReference::create($source);
        $cache = new MemoCache($source);
        unset($source);

        // When
        $first = self::pairs($cache->replay());

        // Then
        $this->assertNull($reference->get());
        $this->assertSame([[0, 'a'], [1, 'b'], [2, 'c']], $first);
        $this->assertSame($first, self::pairs($cache->replay()));
    }

    /**
     * @test a fully read Iterator source is released, and the cache still replays it
     */
    public function testExhaustedSourceIsReleasedIterator(): void
    {
        // Given
        $source = new ArrayIteratorFixture(['a', 'b', 'c']);
        $reference = \WeakReference::create($source);
        $cache = new MemoCache($source);
        unset($source);

        // When
        $first = self::pairs($cache->replay());

        // Then
        $this->assertNull($reference->get());
        $this->assertSame([[0, 'a'], [1, 'b'], [2, 'c']], $first);
        $this->assertSame($first, self::pairs($cache->replay()));
    }

    /**
     * @test a fully read Generator source is released, and the cache still replays it
     */
    public function testExhaustedSourceIsReleasedGenerator(): void
    {
        // Given
        $source = GeneratorFixture::getGenerator(['a', 'b', 'c']);
        $reference = \WeakReference::create($source);
        $cache = new MemoCache($source);
        unset($source);

        // When
        $first = self::pairs($cache->replay());

        // Then
        $this->assertNull($reference->get());
        $this->assertSame([[0, 'a'], [1, 'b'], [2, 'c']], $first);
        $this->assertSame($first, self::pairs($cache->replay()));
    }

    /**
     * @test the fully read iterator of an IteratorAggregate source is released, and the cache still replays it
     */
    public function testExhaustedSourceIsReleasedIteratorAggregate(): void
    {
        // Given
        $iterator = new \ArrayIterator(['a', 'b', 'c']);
        $reference = \WeakReference::create($iterator);
        $cache = new MemoCache(new WrappingIteratorAggregateFixture($iterator));
        unset($iterator);

        // When
        $first = self::pairs($cache->replay());

        // Then
        $this->assertNull($reference->get());
        $this->assertSame([[0, 'a'], [1, 'b'], [2, 'c']], $first);
        $this->assertSame($first, self::pairs($cache->replay()));
    }

    /**
     * @test a source that throws is released, and a replay yields what was cached, then rethrows the failure
     */
    public function testFailedSourceIsReleased(): void
    {
        // Given
        $source = new class (['a', 'b', 'c']) extends \ArrayIterator {
            public function current(): mixed
            {
                if ($this->key() === 1) {
                    throw new \RuntimeException('boom');
                }

                return parent::current();
            }
        };
        $reference = \WeakReference::create($source);
        $cache = new MemoCache($source);
        unset($source);

        // When
        [$firstPairs, $firstFailure] = self::pairsAndFailure($cache->replay());

        // Then
        $this->assertNull($reference->get());
        $this->assertSame([[0, 'a']], $firstPairs);
        $this->assertInstanceOf(\RuntimeException::class, $firstFailure);
        $this->assertSame('boom', $firstFailure->getMessage());

        // And when
        [$secondPairs, $secondFailure] = self::pairsAndFailure($cache->replay());

        // Then
        $this->assertSame([[0, 'a']], $secondPairs);
        $this->assertSame($firstFailure, $secondFailure);
    }

    /**
     * @test memoize releases a fully read source, and still replays it
     */
    public function testMemoizeReleasesExhaustedSource(): void
    {
        // Given
        $source = new \ArrayIterator(['a', 'b', 'c']);
        $reference = \WeakReference::create($source);
        $memoized = Transform::memoize($source);
        unset($source);

        // When
        $first = self::pairs($memoized);

        // Then
        $this->assertNull($reference->get());
        $this->assertSame([[0, 'a'], [1, 'b'], [2, 'c']], $first);
        $this->assertSame($first, self::pairs($memoized));
    }

    /**
     * @test cycling a limited tee copy no longer makes its sibling buffer the rest of the source
     */
    public function testCycledLimitedTeeCopyReleasesSiblingBuffer(): void
    {
        // Given
        $references = [];
        $source = (static function () use (&$references): \Generator {
            for ($i = 0; $i < 10; ++$i) {
                $object = new \stdClass();
                $references[$i] = \WeakReference::create($object);
                yield $i => $object;
            }
        })();
        [$copy, $sibling] = Transform::tee($source, 2);
        unset($source);
        $cycle = Infinite::cycle(new \LimitIterator($copy, 0, 3));
        unset($copy);

        // When
        $keys = [];
        foreach ($cycle as $key => $ignored) {
            $keys[] = $key;
            if (\count($keys) === 4) {
                break;
            }
        }
        unset($ignored);
        \iterator_count($sibling);

        // Then
        $alive = [];
        foreach ($references as $i => $reference) {
            if ($reference->get() !== null) {
                $alive[] = $i;
            }
        }
        $this->assertSame([0, 1, 2, 0], $keys);
        $this->assertSame([0, 1, 2], $alive);
    }

    /**
     * @param iterable<mixed, mixed> $replay
     *
     * @return list<array{mixed, mixed}>
     */
    private static function pairs(iterable $replay): array
    {
        $pairs = [];
        foreach ($replay as $key => $value) {
            $pairs[] = [$key, $value];
        }

        return $pairs;
    }

    /**
     * @param iterable<mixed, mixed> $replay
     *
     * @return array{list<array{mixed, mixed}>, \Throwable|null}
     */
    private static function pairsAndFailure(iterable $replay): array
    {
        $pairs = [];
        try {
            foreach ($replay as $key => $value) {
                $pairs[] = [$key, $value];
            }
        } catch (\Throwable $failure) {
            return [$pairs, $failure];
        }

        return [$pairs, null];
    }
}
