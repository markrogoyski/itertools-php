<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class IntersperseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         intersperse array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        mixed $separator
     * @param        array $expected
     */
    public function testArray(array $data, mixed $separator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::intersperse($data, $separator) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [
                [],
                0,
                [],
            ],
            [
                [1],
                0,
                [1],
            ],
            [
                [1, 2],
                0,
                [1, 0, 2],
            ],
            [
                [1, 2, 3],
                0,
                [1, 0, 2, 0, 3],
            ],
            [
                [1, 2, 3, 4, 5],
                0,
                [1, 0, 2, 0, 3, 0, 4, 0, 5],
            ],
            [
                ['a', 'b', 'c'],
                '-',
                ['a', '-', 'b', '-', 'c'],
            ],
            [
                ['path', 'to', 'file'],
                '/',
                ['path', '/', 'to', '/', 'file'],
            ],
            [
                [1, 2, 3],
                null,
                [1, null, 2, null, 3],
            ],
            [
                [1, 2, 3],
                [0, 0],
                [1, [0, 0], 2, [0, 0], 3],
            ],
            [
                [1, 2, 3],
                (object) ['sep' => true],
                [1, (object) ['sep' => true], 2, (object) ['sep' => true], 3],
            ],
            [
                [1, 2.2, '3', true, null],
                'X',
                [1, 'X', 2.2, 'X', '3', 'X', true, 'X', null],
            ],
        ];
    }

    /**
     * @test         intersperse generator
     * @dataProvider dataProviderForGenerator
     * @param        \Generator $data
     * @param        mixed      $separator
     * @param        array      $expected
     */
    public function testGenerator(\Generator $data, mixed $separator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::intersperse($data, $separator) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForGenerator(): array
    {
        $gen = static fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                0,
                [],
            ],
            [
                $gen([1]),
                0,
                [1],
            ],
            [
                $gen([1, 2]),
                0,
                [1, 0, 2],
            ],
            [
                $gen([1, 2, 3]),
                0,
                [1, 0, 2, 0, 3],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                0,
                [1, 0, 2, 0, 3, 0, 4, 0, 5],
            ],
            [
                $gen(['a', 'b', 'c']),
                '-',
                ['a', '-', 'b', '-', 'c'],
            ],
            [
                $gen([1, 2, 3]),
                null,
                [1, null, 2, null, 3],
            ],
            [
                $gen([1, 2, 3]),
                [0, 0],
                [1, [0, 0], 2, [0, 0], 3],
            ],
        ];
    }

    /**
     * @test         intersperse iterator
     * @dataProvider dataProviderForIterator
     * @param        \Iterator $data
     * @param        mixed     $separator
     * @param        array     $expected
     */
    public function testIterator(\Iterator $data, mixed $separator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::intersperse($data, $separator) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForIterator(): array
    {
        $iter = static fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                0,
                [],
            ],
            [
                $iter([1]),
                0,
                [1],
            ],
            [
                $iter([1, 2]),
                0,
                [1, 0, 2],
            ],
            [
                $iter([1, 2, 3]),
                0,
                [1, 0, 2, 0, 3],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                0,
                [1, 0, 2, 0, 3, 0, 4, 0, 5],
            ],
            [
                $iter(['a', 'b', 'c']),
                '-',
                ['a', '-', 'b', '-', 'c'],
            ],
            [
                $iter([1, 2, 3]),
                null,
                [1, null, 2, null, 3],
            ],
            [
                $iter([1, 2, 3]),
                [0, 0],
                [1, [0, 0], 2, [0, 0], 3],
            ],
        ];
    }

    /**
     * @test         intersperse IteratorAggregate
     * @dataProvider dataProviderForIteratorAggregate
     * @param        \Traversable $data
     * @param        mixed        $separator
     * @param        array        $expected
     */
    public function testIteratorAggregate(\Traversable $data, mixed $separator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::intersperse($data, $separator) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForIteratorAggregate(): array
    {
        $agg = static fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $agg([]),
                0,
                [],
            ],
            [
                $agg([1]),
                0,
                [1],
            ],
            [
                $agg([1, 2]),
                0,
                [1, 0, 2],
            ],
            [
                $agg([1, 2, 3]),
                0,
                [1, 0, 2, 0, 3],
            ],
            [
                $agg([1, 2, 3, 4, 5]),
                0,
                [1, 0, 2, 0, 3, 0, 4, 0, 5],
            ],
            [
                $agg(['a', 'b', 'c']),
                '-',
                ['a', '-', 'b', '-', 'c'],
            ],
            [
                $agg([1, 2, 3]),
                null,
                [1, null, 2, null, 3],
            ],
            [
                $agg([1, 2, 3]),
                [0, 0],
                [1, [0, 0], 2, [0, 0], 3],
            ],
        ];
    }

    /**
     * @test intersperse discards associative keys (native array)
     */
    public function testAssociativeArrayKeysDiscarded(): void
    {
        // Given
        $data = ['first' => 1, 'second' => 2, 'third' => 3];

        // When
        $result = \iterator_to_array(Single::intersperse($data, 0), false);

        // Then
        $this->assertEquals([1, 0, 2, 0, 3], $result);
    }

    /**
     * @test intersperse discards associative keys (key-value Generator)
     */
    public function testAssociativeKeyValueGeneratorKeysDiscarded(): void
    {
        // Given
        $data = GeneratorFixture::getKeyValueGenerator(['first' => 1, 'second' => 2, 'third' => 3]);

        // When
        $result = \iterator_to_array(Single::intersperse($data, 0), false);

        // Then
        $this->assertEquals([1, 0, 2, 0, 3], $result);
    }

    /**
     * @test intersperse discards associative keys (IteratorAggregate)
     */
    public function testAssociativeIteratorAggregateKeysDiscarded(): void
    {
        // Given
        $data = new IteratorAggregateFixture(['first' => 1, 'second' => 2, 'third' => 3]);

        // When
        $result = \iterator_to_array(Single::intersperse($data, 0), false);

        // Then
        $this->assertEquals([1, 0, 2, 0, 3], $result);
    }

    /**
     * @test intersperse produces sequential integer keys in output
     */
    public function testOutputKeysAreSequentialInts(): void
    {
        // Given
        $data = [10, 20, 30];

        // When
        $result = \iterator_to_array(Single::intersperse($data, 0));

        // Then
        $this->assertEquals([10, 0, 20, 0, 30], $result);
        $this->assertEquals([0, 1, 2, 3, 4], \array_keys($result));
    }

    /**
     * @test intersperse array separator is yielded by value (not expanded)
     */
    public function testArraySeparatorNotExpanded(): void
    {
        // Given
        $data      = [1, 2, 3];
        $separator = ['a', 'b'];

        // When
        $result = \iterator_to_array(Single::intersperse($data, $separator), false);

        // Then
        $this->assertCount(5, $result);
        $this->assertSame(1, $result[0]);
        $this->assertSame(['a', 'b'], $result[1]);
        $this->assertSame(2, $result[2]);
        $this->assertSame(['a', 'b'], $result[3]);
        $this->assertSame(3, $result[4]);
    }

    /**
     * @test intersperse object separator is yielded by reference (same instance)
     */
    public function testObjectSeparatorSameInstance(): void
    {
        // Given
        $data      = [1, 2, 3];
        $separator = (object) ['x' => 1];

        // When
        $result = \iterator_to_array(Single::intersperse($data, $separator), false);

        // Then
        $this->assertSame($separator, $result[1]);
        $this->assertSame($separator, $result[3]);
    }
}
