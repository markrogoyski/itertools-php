<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class EnumerateTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test enumerate example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $words = ['apple', 'banana', 'cherry'];

        // When
        $result = [];
        foreach (Single::enumerate($words) as $pair) {
            $result[] = $pair;
        }

        // Then
        $this->assertSame([[0, 'apple'], [1, 'banana'], [2, 'cherry']], $result);
    }

    /**
     * @test         enumerate (array)
     * @dataProvider dataProviderForEnumerate
     * @param        array<mixed> $data
     * @param        int          $start
     * @param        array<array{int, mixed}> $expected
     */
    public function testEnumerateArray(array $data, int $start, array $expected): void
    {
        // When
        $result = [];
        foreach (Single::enumerate($data, $start) as $pair) {
            $result[] = $pair;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         enumerate (Generator)
     * @dataProvider dataProviderForEnumerate
     * @param        array<mixed> $data
     * @param        int          $start
     * @param        array<array{int, mixed}> $expected
     */
    public function testEnumerateGenerator(array $data, int $start, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = [];
        foreach (Single::enumerate($iterable, $start) as $pair) {
            $result[] = $pair;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         enumerate (Iterator)
     * @dataProvider dataProviderForEnumerate
     * @param        array<mixed> $data
     * @param        int          $start
     * @param        array<array{int, mixed}> $expected
     */
    public function testEnumerateIterator(array $data, int $start, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = [];
        foreach (Single::enumerate($iterable, $start) as $pair) {
            $result[] = $pair;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         enumerate (IteratorAggregate)
     * @dataProvider dataProviderForEnumerate
     * @param        array<mixed> $data
     * @param        int          $start
     * @param        array<array{int, mixed}> $expected
     */
    public function testEnumerateIteratorAggregate(array $data, int $start, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = [];
        foreach (Single::enumerate($iterable, $start) as $pair) {
            $result[] = $pair;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForEnumerate(): array
    {
        return [
            // single element, default start
            [['only'], 0, [[0, 'only']]],
            // multiple elements, default start
            [['a', 'b', 'c'], 0, [[0, 'a'], [1, 'b'], [2, 'c']]],
            // mixed types
            [[1, 'two', 3.0, true, null], 0, [[0, 1], [1, 'two'], [2, 3.0], [3, true], [4, null]]],
            // explicit start = 1
            [['x', 'y', 'z'], 1, [[1, 'x'], [2, 'y'], [3, 'z']]],
            // negative start
            [['a', 'b', 'c'], -2, [[-2, 'a'], [-1, 'b'], [0, 'c']]],
            // start = 0 explicit (matches default)
            [['a', 'b'], 0, [[0, 'a'], [1, 'b']]],
            // larger positive start
            [['a', 'b', 'c'], 100, [[100, 'a'], [101, 'b'], [102, 'c']]],
        ];
    }

    /**
     * @test         enumerate empty iterable yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterable(iterable $data): void
    {
        // When
        $result = [];
        foreach (Single::enumerate($data) as $pair) {
            $result[] = $pair;
        }

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test         enumerate empty iterable with non-zero start yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableWithStart(iterable $data): void
    {
        // When
        $result = [];
        foreach (Single::enumerate($data, 5) as $pair) {
            $result[] = $pair;
        }

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test enumerate index is independent of source string keys
     */
    public function testIndexIndependentOfStringKeys(): void
    {
        // Given
        $data = ['x' => 'apple', 'y' => 'banana', 'z' => 'cherry'];

        // When
        $result = [];
        foreach (Single::enumerate($data) as $pair) {
            $result[] = $pair;
        }

        // Then
        $this->assertSame([[0, 'apple'], [1, 'banana'], [2, 'cherry']], $result);
    }

    /**
     * @test enumerate index is independent of source numeric keys
     */
    public function testIndexIndependentOfNumericKeys(): void
    {
        // Given
        $data = [10 => 'a', 20 => 'b', 30 => 'c'];

        // When
        $result = [];
        foreach (Single::enumerate($data) as $pair) {
            $result[] = $pair;
        }

        // Then
        $this->assertSame([[0, 'a'], [1, 'b'], [2, 'c']], $result);
    }

    /**
     * @test enumerate index is independent of source keys with custom start
     */
    public function testIndexIndependentOfSourceKeysWithCustomStart(): void
    {
        // Given
        $data = ['x' => 'apple', 'y' => 'banana', 'z' => 'cherry'];

        // When
        $result = [];
        foreach (Single::enumerate($data, 10) as $pair) {
            $result[] = $pair;
        }

        // Then
        $this->assertSame([[10, 'apple'], [11, 'banana'], [12, 'cherry']], $result);
    }

    /**
     * @test enumerate yields list arrays (numerically indexed [0 => index, 1 => value])
     */
    public function testYieldsListArrays(): void
    {
        // Given
        $data = ['a', 'b'];

        // When
        $first = null;
        foreach (Single::enumerate($data) as $pair) {
            $first = $pair;
            break;
        }

        // Then
        $this->assertIsArray($first);
        $this->assertSame([0, 1], \array_keys($first));
    }

    /**
     * @test enumerate supports destructuring assignment
     */
    public function testDestructuring(): void
    {
        // Given
        $data = ['apple', 'banana', 'cherry'];

        // When
        $result = [];
        foreach (Single::enumerate($data, 1) as [$index, $value]) {
            $result[$index] = $value;
        }

        // Then
        $this->assertSame([1 => 'apple', 2 => 'banana', 3 => 'cherry'], $result);
    }

    /**
     * @test         enumerate (array) throws OverflowException for an element whose index would pass PHP_INT_MAX
     * @dataProvider dataProviderForIndexPastIntegerRange
     * @param        array<mixed>             $data
     * @param        int                      $start
     * @param        array<array{int, mixed}> $expected the pairs yielded before the exception
     */
    public function testIndexPastIntegerRangeThrowsArray(array $data, int $start, array $expected): void
    {
        // When
        $result = [];
        try {
            foreach (Single::enumerate($data, $start) as $pair) {
                $result[] = $pair;
            }
            $this->fail('Expected \OverflowException');
        } catch (\OverflowException) {
            // Then
            $this->assertSame($expected, $result);
        }
    }

    /**
     * @test         enumerate (Generator) throws OverflowException for an element whose index would pass PHP_INT_MAX
     * @dataProvider dataProviderForIndexPastIntegerRange
     * @param        array<mixed>             $data
     * @param        int                      $start
     * @param        array<array{int, mixed}> $expected the pairs yielded before the exception
     */
    public function testIndexPastIntegerRangeThrowsGenerator(array $data, int $start, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = [];
        try {
            foreach (Single::enumerate($iterable, $start) as $pair) {
                $result[] = $pair;
            }
            $this->fail('Expected \OverflowException');
        } catch (\OverflowException) {
            // Then
            $this->assertSame($expected, $result);
        }
    }

    /**
     * @test         enumerate (Iterator) throws OverflowException for an element whose index would pass PHP_INT_MAX
     * @dataProvider dataProviderForIndexPastIntegerRange
     * @param        array<mixed>             $data
     * @param        int                      $start
     * @param        array<array{int, mixed}> $expected the pairs yielded before the exception
     */
    public function testIndexPastIntegerRangeThrowsIterator(array $data, int $start, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = [];
        try {
            foreach (Single::enumerate($iterable, $start) as $pair) {
                $result[] = $pair;
            }
            $this->fail('Expected \OverflowException');
        } catch (\OverflowException) {
            // Then
            $this->assertSame($expected, $result);
        }
    }

    /**
     * @test         enumerate (IteratorAggregate) throws OverflowException for an element whose index would pass PHP_INT_MAX
     * @dataProvider dataProviderForIndexPastIntegerRange
     * @param        array<mixed>             $data
     * @param        int                      $start
     * @param        array<array{int, mixed}> $expected the pairs yielded before the exception
     */
    public function testIndexPastIntegerRangeThrowsIteratorAggregate(array $data, int $start, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = [];
        try {
            foreach (Single::enumerate($iterable, $start) as $pair) {
                $result[] = $pair;
            }
            $this->fail('Expected \OverflowException');
        } catch (\OverflowException) {
            // Then
            $this->assertSame($expected, $result);
        }
    }

    public static function dataProviderForIndexPastIntegerRange(): array
    {
        return [
            'start at PHP_INT_MAX, two elements' => [
                ['a', 'b'],
                \PHP_INT_MAX,
                [[\PHP_INT_MAX, 'a']],
            ],
            'start just below PHP_INT_MAX, three elements' => [
                ['a', 'b', 'c'],
                \PHP_INT_MAX - 1,
                [[\PHP_INT_MAX - 1, 'a'], [\PHP_INT_MAX, 'b']],
            ],
        ];
    }

    /**
     * @test         enumerate ends normally when its last element takes the index PHP_INT_MAX
     * @dataProvider dataProviderForIndexReachingIntegerRangeEdge
     * @param        array<mixed>             $data
     * @param        int                      $start
     * @param        array<array{int, mixed}> $expected
     */
    public function testIndexReachingIntegerRangeEdge(array $data, int $start, array $expected): void
    {
        // When
        $result = [];
        foreach (Single::enumerate($data, $start) as $pair) {
            $result[] = $pair;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForIndexReachingIntegerRangeEdge(): array
    {
        return [
            'one element at PHP_INT_MAX' => [
                ['a'],
                \PHP_INT_MAX,
                [[\PHP_INT_MAX, 'a']],
            ],
            'last of two elements at PHP_INT_MAX' => [
                ['a', 'b'],
                \PHP_INT_MAX - 1,
                [[\PHP_INT_MAX - 1, 'a'], [\PHP_INT_MAX, 'b']],
            ],
            'start at PHP_INT_MIN' => [
                ['a', 'b'],
                \PHP_INT_MIN,
                [[\PHP_INT_MIN, 'a'], [\PHP_INT_MIN + 1, 'b']],
            ],
        ];
    }

    /**
     * @test enumerate starting at PHP_INT_MAX reads only the element that overflows, not the rest
     */
    public function testIndexPastIntegerRangeStopsReading(): void
    {
        // Given
        $pulls = 0;
        $source = (static function () use (&$pulls): \Generator {
            foreach (['a', 'b', 'c', 'd'] as $value) {
                ++$pulls;
                yield $value;
            }
        })();

        // When
        try {
            foreach (Single::enumerate($source, \PHP_INT_MAX) as $_pair) {
            }
            $this->fail('Expected \OverflowException');
        } catch (\OverflowException) {
            // Then
            $this->assertSame(2, $pulls);
        }
    }
}
