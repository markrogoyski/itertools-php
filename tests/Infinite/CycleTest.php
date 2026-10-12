<?php

declare(strict_types=1);

namespace IterTools\Tests\Infinite;

use IterTools\Infinite;
use IterTools\Stream;
use IterTools\Tests\Fixture;
use IterTools\Transform;

class CycleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test cycle array
     */
    public function testCycleArray(): void
    {
        // Given
        $array    = [1, 2, 3, 4, 5];
        $result   = [];
        $expected = [1, 2, 3, 4, 5, 1, 2, 3, 4, 5];

        // And
        $count = 0;

        // When
        foreach (Infinite::cycle($array) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 10) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle empty array
     */
    public function testCycleEmptyArray(): void
    {
        // Given
        $array    = [];
        $result   = [];
        $expected = [];

        // When
        foreach (Infinite::cycle($array) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle generator
     */
    public function testCycleGenerator(): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5]);
        $result    = [];
        $expected  = [1, 2, 3, 4, 5, 1, 2, 3, 4, 5];

        // And
        $count = 0;

        // When
        foreach (Infinite::cycle($generator) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 10) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle generator
     */
    public function testCycleEmptyGenerator(): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([]);
        $result    = [];
        $expected  = [];

        // When
        foreach (Infinite::cycle($generator) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle iterator
     */
    public function testCycleIterator(): void
    {
        // Given
        $iterator  = new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5]);
        $result    = [];
        $expected  = [1, 2, 3, 4, 5, 1, 2, 3, 4, 5];

        // And
        $count = 0;

        // When
        foreach (Infinite::cycle($iterator) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 10) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle empty iterator
     */
    public function testCycleEmptyIterator(): void
    {
        // Given
        $iterator  = new Fixture\ArrayIteratorFixture([]);
        $result    = [];
        $expected  = [];

        // When
        foreach (Infinite::cycle($iterator) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle traversable
     */
    public function testCycleTraversable(): void
    {
        // Given
        $traversable = new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5]);
        $result      = [];
        $expected    = [1, 2, 3, 4, 5, 1, 2, 3, 4, 5];

        // And
        $count = 0;

        // When
        foreach (Infinite::cycle($traversable) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 10) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle empty traversable
     */
    public function testCycleEmptyTraversable(): void
    {
        // Given
        $traversable = new Fixture\IteratorAggregateFixture([]);
        $result      = [];
        $expected    = [];

        // When
        foreach (Infinite::cycle($traversable) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle IteratorAggregate returning Generator
     */
    public function testCycleIteratorAggregateReturningGenerator(): void
    {
        // Given
        $agg = new class implements \IteratorAggregate {
            public function getIterator(): \Traversable
            {
                yield 1;
                yield 2;
                yield 3;
            }
        };

        // And
        $result    = [];
        $expected  = [1, 2, 3, 1, 2, 3];
        $count = 0;

        // When
        foreach (Infinite::cycle($agg) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 6) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle IteratorAggregate with duplicate keys
     */
    public function testCycleIteratorAggregateWithDuplicateKeys(): void
    {
        // Given
        $agg = new class implements \IteratorAggregate {
            public function getIterator(): \Traversable
            {
                yield 'a' => 1;
                yield 'a' => 2;
                yield 'b' => 3;
            }
        };

        // And
        $result    = [];
        $expected  = [1, 2, 3, 1, 2, 3];
        $count = 0;

        // When
        foreach (Infinite::cycle($agg) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 6) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle Generator with duplicate keys
     */
    public function testCycleGeneratorWithDuplicateKeys(): void
    {
        // Given
        $gen = function () {
            yield 'x' => 10;
            yield 'x' => 20;
        };

        // And
        $result    = [];
        $expected  = [10, 20, 10, 20];
        $count = 0;

        // When
        foreach (Infinite::cycle($gen()) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 4) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle repeats every pass of a source that can only be iterated once
     * @dataProvider dataProviderForOneShotSources
     * @param iterable $source
     * @param array    $expected
     */
    public function testCycleOneShotSource(iterable $source, array $expected): void
    {
        // When
        $result = $this->takeValues(Infinite::cycle($source), \count($expected));

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForOneShotSources(): array
    {
        return [
            'Generator' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                [1, 2, 3, 1, 2, 3, 1, 2, 3],
            ],
            'IteratorIterator around Generator' => [
                new \IteratorIterator(Fixture\GeneratorFixture::getGenerator([1, 2, 3])),
                [1, 2, 3, 1, 2, 3, 1, 2, 3],
            ],
            'IteratorIterator around IteratorIterator around Generator' => [
                new \IteratorIterator(new \IteratorIterator(Fixture\GeneratorFixture::getGenerator([1, 2, 3]))),
                [1, 2, 3, 1, 2, 3, 1, 2, 3],
            ],
            'Stream of Generator' => [
                Stream::of(Fixture\GeneratorFixture::getGenerator([1, 2, 3])),
                [1, 2, 3, 1, 2, 3, 1, 2, 3],
            ],
            'memoized Stream of Generator' => [
                Stream::of(Fixture\GeneratorFixture::getGenerator([1, 2, 3]))->memoize(),
                [1, 2, 3, 1, 2, 3, 1, 2, 3],
            ],
            'memoized Generator' => [
                Transform::memoize(Fixture\GeneratorFixture::getGenerator([1, 2, 3])),
                [1, 2, 3, 1, 2, 3, 1, 2, 3],
            ],
            'Stream with Generator-backed operation' => [
                Stream::of([1, 2, 3])->map(fn ($x) => $x * 10),
                [10, 20, 30, 10, 20, 30, 10, 20, 30],
            ],
        ];
    }

    /**
     * @test cycle empty source of every kind terminates without yielding
     * @dataProvider dataProviderForEmptySources
     * @param iterable $source
     */
    public function testCycleEmptySourceTerminates(iterable $source): void
    {
        // Given
        $result = [];

        // When
        foreach (Infinite::cycle($source) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertSame([], $result);
    }

    public static function dataProviderForEmptySources(): array
    {
        return [
            'array' => [[]],
            'Generator' => [Fixture\GeneratorFixture::getGenerator([])],
            'Iterator' => [new Fixture\ArrayIteratorFixture([])],
            'IteratorAggregate' => [new Fixture\IteratorAggregateFixture([])],
            'IteratorIterator around Generator' => [new \IteratorIterator(Fixture\GeneratorFixture::getGenerator([]))],
            'memoized Stream of Generator' => [Stream::of(Fixture\GeneratorFixture::getGenerator([]))->memoize()],
            'tee copy' => [Transform::tee([], 2)[0]],
        ];
    }

    /**
     * @test cycle nested IteratorAggregates ending in a Generator
     */
    public function testCycleNestedIteratorAggregates(): void
    {
        // Given
        $inner = new class implements \IteratorAggregate {
            public function getIterator(): \Generator
            {
                yield 1;
                yield 2;
                yield 3;
            }
        };
        $outer = new class ($inner) implements \IteratorAggregate {
            public function __construct(private \IteratorAggregate $inner)
            {
            }

            public function getIterator(): \Traversable
            {
                return $this->inner;
            }
        };

        // When
        $result = $this->takeValues(Infinite::cycle($outer), 9);

        // Then
        $this->assertSame([1, 2, 3, 1, 2, 3, 1, 2, 3], $result);
    }

    /**
     * @test cycle tee copies: each copy repeats the full sequence once it has advanced past position 0
     */
    public function testCycleTeeCopies(): void
    {
        // Given
        [$first, $second] = Transform::tee(Fixture\GeneratorFixture::getGenerator([1, 2, 3]), 2);

        // When
        $firstResult  = $this->takeValues(Infinite::cycle($first), 8);
        $secondResult = $this->takeValues(Infinite::cycle($second), 8);

        // Then
        $this->assertSame([1, 2, 3, 1, 2, 3, 1, 2], $firstResult);
        $this->assertSame([1, 2, 3, 1, 2, 3, 1, 2], $secondResult);
    }

    /**
     * @test cycle a tee copy whose sibling was consumed first
     */
    public function testCycleTeeCopyAfterSiblingConsumed(): void
    {
        // Given
        [$first, $second] = Transform::tee(Fixture\GeneratorFixture::getGenerator([1, 2, 3]), 2);
        $consumed = \iterator_to_array($first, false);

        // When
        $result = $this->takeValues(Infinite::cycle($second), 7);

        // Then
        $this->assertSame([1, 2, 3], $consumed);
        $this->assertSame([1, 2, 3, 1, 2, 3, 1], $result);
    }

    /**
     * @test cycle an IteratorAggregate whose getIterator() returns a fresh forward-only cursor per call does not lose the first element
     */
    public function testCycleCursorAggregateKeepsFirstElement(): void
    {
        // Given
        $cursor = new class ([1, 2, 3]) implements \IteratorAggregate {
            public int $getIteratorCalls = 0;
            private int $offset = 0;

            public function __construct(private array $rows)
            {
            }

            public function fetch(): ?int
            {
                return $this->rows[$this->offset++] ?? null;
            }

            public function getIterator(): \Iterator
            {
                ++$this->getIteratorCalls;

                // Like PDOStatement: creating the iterator fetches the next row immediately.
                return new class ($this) implements \Iterator {
                    private ?int $row;
                    private int $key = 0;

                    public function __construct(private object $cursor)
                    {
                        $this->row = $this->cursor->fetch();
                    }

                    public function current(): mixed
                    {
                        return $this->row;
                    }

                    public function key(): mixed
                    {
                        return $this->key;
                    }

                    public function next(): void
                    {
                        $this->row = $this->cursor->fetch();
                        ++$this->key;
                    }

                    public function valid(): bool
                    {
                        return $this->row !== null;
                    }

                    public function rewind(): void
                    {
                        if ($this->key > 0) {
                            throw new \Error('Iterator does not support rewinding');
                        }
                    }
                };
            }
        };

        // When
        $result = $this->takeValues(Infinite::cycle($cursor), 7);

        // Then
        $this->assertSame([1, 2, 3, 1, 2, 3, 1], $result);
        $this->assertSame(1, $cursor->getIteratorCalls);
    }

    /**
     * @test cycle calls getIterator() on an IteratorAggregate only once, and not before iteration begins
     */
    public function testCycleCallsGetIteratorOnceLazily(): void
    {
        // Given
        $aggregate = new Fixture\CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $cycle = Infinite::cycle($aggregate);

        // Then
        $this->assertSame(0, $aggregate->getIteratorCallCount());

        // When
        $result = $this->takeValues($cycle, 7);

        // Then
        $this->assertSame([1, 2, 3, 1, 2, 3, 1], $result);
        $this->assertSame(1, $aggregate->getIteratorCallCount());
    }

    /**
     * @test cycle a PDOStatement keeps the first row and repeats all rows
     * @requires extension pdo_sqlite
     */
    public function testCyclePdoStatement(): void
    {
        // Given
        $pdo = new \PDO('sqlite::memory:');
        $pdo->exec('CREATE TABLE numbers (n INTEGER)');
        $pdo->exec('INSERT INTO numbers VALUES (1), (2), (3)');
        $statement = $pdo->query('SELECT n FROM numbers ORDER BY n', \PDO::FETCH_COLUMN, 0);

        // When
        $result = $this->takeValues(Infinite::cycle($statement), 7);

        // Then
        $this->assertSame([1, 2, 3, 1, 2, 3, 1], $result);
    }

    /**
     * @test cycle does not consume a Generator until iterated, then pulls one element at a time
     */
    public function testCycleIsLazy(): void
    {
        // Given
        $pulled    = 0;
        $generator = (function () use (&$pulled) {
            foreach ([1, 2, 3, 4, 5] as $value) {
                ++$pulled;
                yield $value;
            }
        })();

        // When
        $cycle = Infinite::cycle($generator);

        // Then
        $this->assertSame(0, $pulled);

        // When
        $result = $this->takeValues($cycle, 2);

        // Then
        $this->assertSame([1, 2], $result);
        $this->assertSame(2, $pulled);
    }

    /**
     * @test cycle reads a non-rewindable source only once and replays later passes from its cache
     */
    public function testCycleReadsSourceOnce(): void
    {
        // Given
        $pulled    = 0;
        $generator = (function () use (&$pulled) {
            foreach ([1, 2, 3] as $value) {
                ++$pulled;
                yield $value;
            }
        })();

        // When
        $result = $this->takeValues(Infinite::cycle($generator), 10);

        // Then
        $this->assertSame([1, 2, 3, 1, 2, 3, 1, 2, 3, 1], $result);
        $this->assertSame(3, $pulled);
    }

    /**
     * @test cycle restarts from the first element when iteration is restarted part-way through the first pass
     */
    public function testCycleRestartDuringFirstPass(): void
    {
        // Given
        $cycle = Infinite::cycle(Fixture\GeneratorFixture::getGenerator([1, 2, 3]));

        // When
        $firstAttempt  = $this->takeValues($cycle, 2);
        $secondAttempt = $this->takeValues($cycle, 7);

        // Then
        $this->assertSame([1, 2], $firstAttempt);
        $this->assertSame([1, 2, 3, 1, 2, 3, 1], $secondAttempt);
    }

    /**
     * @test cycle preserves source keys on every pass
     * @dataProvider dataProviderForKeyedSources
     * @param iterable $source
     * @param array    $expected list of [key, value] pairs
     */
    public function testCyclePreservesKeys(iterable $source, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Infinite::cycle($source) as $key => $value) {
            $result[] = [$key, $value];
            if (\count($result) === \count($expected)) {
                break;
            }
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForKeyedSources(): array
    {
        return [
            'array' => [
                ['a' => 1, 'b' => 2],
                [['a', 1], ['b', 2], ['a', 1], ['b', 2], ['a', 1]],
            ],
            'Generator' => [
                Fixture\GeneratorFixture::getKeyValueGenerator(['a' => 1, 'b' => 2]),
                [['a', 1], ['b', 2], ['a', 1], ['b', 2], ['a', 1]],
            ],
            'Iterator' => [
                new \ArrayIterator(['a' => 1, 'b' => 2]),
                [['a', 1], ['b', 2], ['a', 1], ['b', 2], ['a', 1]],
            ],
            'IteratorAggregate' => [
                new Fixture\IteratorAggregateFixture(['a' => 1, 'b' => 2]),
                [['a', 1], ['b', 2], ['a', 1], ['b', 2], ['a', 1]],
            ],
            'Generator with duplicate keys' => [
                Fixture\GeneratorFixture::getCombined(['x', 'x'], [10, 20]),
                [['x', 10], ['x', 20], ['x', 10], ['x', 20], ['x', 10]],
            ],
        ];
    }

    /**
     * @param \Iterator $iterator
     * @param int       $count
     * @return array
     */
    private function takeValues(\Iterator $iterator, int $count): array
    {
        $result = [];
        foreach ($iterator as $value) {
            $result[] = $value;
            if (\count($result) === $count) {
                break;
            }
        }
        return $result;
    }
}
