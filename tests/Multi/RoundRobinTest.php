<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi;

use IterTools\Multi;
use IterTools\Tests\Fixture;

class RoundRobinTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         roundRobin (array)
     * @dataProvider dataProviderForRoundRobin
     * @param        array<array<mixed>> $iterables
     * @param        array<mixed>        $expected
     */
    public function testRoundRobinArray(array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::roundRobin(...$iterables) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         roundRobin (Generator)
     * @dataProvider dataProviderForRoundRobin
     * @param        array<array<mixed>> $iterables
     * @param        array<mixed>        $expected
     */
    public function testRoundRobinGenerator(array $iterables, array $expected): void
    {
        // Given
        $generators = [];
        foreach ($iterables as $iterable) {
            $generators[] = Fixture\GeneratorFixture::getGenerator($iterable);
        }
        $result = [];

        // When
        foreach (Multi::roundRobin(...$generators) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         roundRobin (Iterator)
     * @dataProvider dataProviderForRoundRobin
     * @param        array<array<mixed>> $iterables
     * @param        array<mixed>        $expected
     */
    public function testRoundRobinIterator(array $iterables, array $expected): void
    {
        // Given
        $iterators = [];
        foreach ($iterables as $iterable) {
            $iterators[] = new Fixture\ArrayIteratorFixture($iterable);
        }
        $result = [];

        // When
        foreach (Multi::roundRobin(...$iterators) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         roundRobin (IteratorAggregate)
     * @dataProvider dataProviderForRoundRobin
     * @param        array<array<mixed>> $iterables
     * @param        array<mixed>        $expected
     */
    public function testRoundRobinIteratorAggregate(array $iterables, array $expected): void
    {
        // Given
        $traversables = [];
        foreach ($iterables as $iterable) {
            $traversables[] = new Fixture\IteratorAggregateFixture($iterable);
        }
        $result = [];

        // When
        foreach (Multi::roundRobin(...$traversables) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array<array{0: array<array<mixed>>, 1: array<mixed>}>
     */
    public static function dataProviderForRoundRobin(): array
    {
        return [
            // Zero iterables
            [
                [],
                [],
            ],
            // Single iterable, empty
            [
                [[]],
                [],
            ],
            // Single iterable, non-empty
            [
                [[1, 2, 3]],
                [1, 2, 3],
            ],
            // All empty
            [
                [[], [], []],
                [],
            ],
            // Two equal-length
            [
                [[1, 2, 3], [4, 5, 6]],
                [1, 4, 2, 5, 3, 6],
            ],
            // Two unequal: first longer
            [
                [[1, 2, 3, 4], [10, 20]],
                [1, 10, 2, 20, 3, 4],
            ],
            // Two unequal: second longer
            [
                [[1, 2], [10, 20, 30, 40]],
                [1, 10, 2, 20, 30, 40],
            ],
            // Three iterables, one shorter
            [
                [[1, 2, 3], ['a', 'b'], ['x', 'y', 'z']],
                [1, 'a', 'x', 2, 'b', 'y', 3, 'z'],
            ],
            // Empty among non-empties
            [
                [[], [1, 2]],
                [1, 2],
            ],
            [
                [[1, 2, 3], [], [4, 5]],
                [1, 4, 2, 5, 3],
            ],
            // Three queues from doc example
            [
                [['A', 'B', 'C'], ['D', 'E'], ['F', 'G', 'H']],
                ['A', 'D', 'F', 'B', 'E', 'G', 'C', 'H'],
            ],
            // Strings and mixed scalars
            [
                [['a', 'b'], [1, 2], [true, false]],
                ['a', 1, true, 'b', 2, false],
            ],
        ];
    }

    /**
     * @test roundRobin with mixed iterable types in one call
     */
    public function testMixedIterableTypes(): void
    {
        // Given
        $a = [1, 2, 3];
        $b = Fixture\GeneratorFixture::getGenerator(['a', 'b']);
        $c = new Fixture\ArrayIteratorFixture([10, 20, 30, 40]);
        $d = new Fixture\IteratorAggregateFixture(['x']);

        // When
        $result = [];
        foreach (Multi::roundRobin($a, $b, $c, $d) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertSame([1, 'a', 10, 'x', 2, 'b', 20, 3, 30, 40], $result);
    }

    /**
     * @test roundRobin passes object/array values through unchanged
     */
    public function testObjectAndArrayValuesPassThrough(): void
    {
        // Given
        $obj1 = new \stdClass();
        $obj1->name = 'first';
        $obj2 = new \stdClass();
        $obj2->name = 'second';
        $arr1 = ['nested', 'list'];
        $arr2 = ['another', 'one'];

        // When
        $result = [];
        foreach (Multi::roundRobin([$obj1, $arr1], [$obj2, $arr2]) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertCount(4, $result);
        $this->assertSame($obj1, $result[0]);
        $this->assertSame($obj2, $result[1]);
        $this->assertSame($arr1, $result[2]);
        $this->assertSame($arr2, $result[3]);
    }

    /**
     * @test roundRobin discards keys (sequentially re-indexed list output)
     */
    public function testKeysDiscarded(): void
    {
        // Given
        $a = ['x' => 1, 'y' => 2];
        $b = ['p' => 10, 'q' => 20];

        // When
        $generator = Multi::roundRobin($a, $b);
        $resultKeys = \iterator_to_array($generator, true);

        // Then
        // Without preserve_keys=false, integer keys override; we want to assert no string keys leak through
        $this->assertSame([1, 10, 2, 20], \array_values($resultKeys));
        foreach (\array_keys($resultKeys) as $key) {
            $this->assertIsInt($key);
        }
    }

    /**
     * @test roundRobin with no iterables yields empty
     */
    public function testNoIterables(): void
    {
        // When
        $result = [];
        foreach (Multi::roundRobin() as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test         roundRobin - taking the first outputs pulls exactly those values from generator sources
     * @dataProvider dataProviderForNoOverRead
     * @param        int            $take
     * @param        array<string>  $expected
     * @param        array<string>  $expectedPulled
     */
    public function testDoesNotPullPastTheLastYieldedValueGenerator(int $take, array $expected, array $expectedPulled): void
    {
        // Given sources that record every value pulled from them
        $pulled = [];
        $a = Fixture\GeneratorFixture::getRecordingGenerator(['a1', 'a2', 'a3'], $pulled);
        $b = Fixture\GeneratorFixture::getRecordingGenerator(['b1'], $pulled);
        $c = Fixture\GeneratorFixture::getRecordingGenerator(['c1', 'c2'], $pulled);

        // When
        $result = [];
        foreach (Multi::roundRobin($a, $b, $c) as $item) {
            $result[] = $item;
            if (\count($result) === $take) {
                break;
            }
        }

        // Then
        $this->assertSame($expected, $result);

        // And no source was started before its turn or advanced past the value it last yielded
        $this->assertSame($expectedPulled, $pulled);
    }

    /**
     * @test         roundRobin - taking the first outputs pulls exactly those values from iterator sources
     * @dataProvider dataProviderForNoOverRead
     * @param        int            $take
     * @param        array<string>  $expected
     * @param        array<string>  $expectedPulled
     */
    public function testDoesNotPullPastTheLastYieldedValueIterator(int $take, array $expected, array $expectedPulled): void
    {
        // Given sources that record every value pulled from them
        $pulled = [];
        $a = new Fixture\RecordingIteratorFixture(['a1', 'a2', 'a3'], $pulled);
        $b = new Fixture\RecordingIteratorFixture(['b1'], $pulled);
        $c = new Fixture\RecordingIteratorFixture(['c1', 'c2'], $pulled);

        // When
        $result = [];
        foreach (Multi::roundRobin($a, $b, $c) as $item) {
            $result[] = $item;
            if (\count($result) === $take) {
                break;
            }
        }

        // Then
        $this->assertSame($expected, $result);

        // And no source was started before its turn or advanced past the value it last yielded
        $this->assertSame($expectedPulled, $pulled);
    }

    /**
     * @test         roundRobin - taking the first outputs pulls exactly those values from IteratorAggregate sources
     * @dataProvider dataProviderForNoOverRead
     * @param        int            $take
     * @param        array<string>  $expected
     * @param        array<string>  $expectedPulled
     */
    public function testDoesNotPullPastTheLastYieldedValueIteratorAggregate(int $take, array $expected, array $expectedPulled): void
    {
        // Given sources that record every value pulled from them
        $pulled = [];
        $a = new Fixture\RecordingIteratorAggregateFixture(['a1', 'a2', 'a3'], $pulled);
        $b = new Fixture\RecordingIteratorAggregateFixture(['b1'], $pulled);
        $c = new Fixture\RecordingIteratorAggregateFixture(['c1', 'c2'], $pulled);

        // When
        $result = [];
        foreach (Multi::roundRobin($a, $b, $c) as $item) {
            $result[] = $item;
            if (\count($result) === $take) {
                break;
            }
        }

        // Then
        $this->assertSame($expected, $result);

        // And no source was started before its turn or advanced past the value it last yielded
        $this->assertSame($expectedPulled, $pulled);
    }

    public static function dataProviderForNoOverRead(): array
    {
        return [
            [1, ['a1'], ['a1']],
            [2, ['a1', 'b1'], ['a1', 'b1']],
            [3, ['a1', 'b1', 'c1'], ['a1', 'b1', 'c1']],
            [4, ['a1', 'b1', 'c1', 'a2'], ['a1', 'b1', 'c1', 'a2']],
            [5, ['a1', 'b1', 'c1', 'a2', 'c2'], ['a1', 'b1', 'c1', 'a2', 'c2']],
            [6, ['a1', 'b1', 'c1', 'a2', 'c2', 'a3'], ['a1', 'b1', 'c1', 'a2', 'c2', 'a3']],
            [7, ['a1', 'b1', 'c1', 'a2', 'c2', 'a3'], ['a1', 'b1', 'c1', 'a2', 'c2', 'a3']],
        ];
    }

    /**
     * @test roundRobin - a generator source is not rewound before its first turn
     */
    public function testDoesNotRewindASourceBeforeItsTurnGenerator(): void
    {
        // Given a second source that can no longer be rewound
        $first = Fixture\GeneratorFixture::getGenerator([1, 2]);
        $spent = Fixture\GeneratorFixture::getGenerator([10, 20, 30]);
        $spent->next();

        // When
        $result = [];
        foreach (Multi::roundRobin($first, $spent) as $item) {
            $result[] = $item;
            break;
        }

        // Then
        $this->assertSame([1], $result);
    }

    /**
     * @test roundRobin - an iterator source keeps its position until its first turn
     */
    public function testDoesNotRewindASourceBeforeItsTurnIterator(): void
    {
        // Given a second source already advanced past its first value
        $iterator = new Fixture\ArrayIteratorFixture([10, 20]);
        $iterator->next();

        // When
        $result = [];
        foreach (Multi::roundRobin([1, 2], $iterator) as $item) {
            $result[] = $item;
            break;
        }

        // Then
        $this->assertSame([1], $result);
        $this->assertSame(1, $iterator->key());
        $this->assertSame(20, $iterator->current());
    }

    /**
     * @test roundRobin - an IteratorAggregate source's getIterator is not called until its first turn
     */
    public function testDoesNotRewindASourceBeforeItsTurnIteratorAggregate(): void
    {
        // Given a second source that counts calls to getIterator
        $aggregate = new Fixture\CountingIteratorAggregateFixture([10, 20]);

        // When
        $result = [];
        foreach (Multi::roundRobin([1, 2], $aggregate) as $item) {
            $result[] = $item;
            break;
        }

        // Then
        $this->assertSame([1], $result);
        $this->assertSame(0, $aggregate->getIteratorCallCount());
    }

    /**
     * @test roundRobin - a started generator source throws at its first turn, after earlier sources have yielded
     */
    public function testRewindsASourceAtItsFirstTurnGenerator(): void
    {
        // Given a second source that can no longer be rewound
        $first = Fixture\GeneratorFixture::getGenerator([1, 2]);
        $spent = Fixture\GeneratorFixture::getGenerator([10, 20, 30]);
        $spent->next();

        // When
        $result = [];
        $exception = null;
        try {
            foreach (Multi::roundRobin($first, $spent) as $item) {
                $result[] = $item;
            }
        } catch (\Exception $e) {
            $exception = $e;
        }

        // Then
        $this->assertSame([1], $result);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertSame('Cannot rewind a generator that was already run', $exception->getMessage());
    }

    /**
     * @test roundRobin - an advanced iterator source is rewound at its first turn
     */
    public function testRewindsASourceAtItsFirstTurnIterator(): void
    {
        // Given a second source already advanced past its first value
        $iterator = new Fixture\ArrayIteratorFixture([10, 20]);
        $iterator->next();

        // When
        $result = \iterator_to_array(Multi::roundRobin([1, 2], $iterator), false);

        // Then
        $this->assertSame([1, 10, 2, 20], $result);
    }

    /**
     * @test roundRobin - leaves iterator sources positioned on the values just yielded
     */
    public function testLeavesSourcesOnTheValuesJustYieldedIterator(): void
    {
        // Given
        $a = new Fixture\ArrayIteratorFixture([1, 2, 3]);
        $b = new Fixture\ArrayIteratorFixture([4, 5, 6]);

        // When
        $result = [];
        foreach (Multi::roundRobin($a, $b) as $item) {
            $result[] = $item;
            if (\count($result) === 2) {
                break;
            }
        }

        // Then
        $this->assertSame([1, 4], $result);
        $this->assertSame(0, $a->key());
        $this->assertSame(0, $b->key());
    }
}
