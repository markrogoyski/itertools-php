<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture;

class ToCountByTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test toCountBy example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $words = ['apple', 'pear', 'banana', 'kiwi', 'plum'];

        // When
        $counts = Reduce::toCountBy($words, fn ($word) => \strlen($word));

        // Then (apple=5, pear=4, banana=6, kiwi=4, plum=4)
        $this->assertEquals([5 => 1, 4 => 3, 6 => 1], $counts);
    }

    /**
     * @test         toCountBy array
     * @dataProvider dataProviderForCounts
     * @param        array    $data
     * @param        callable $keyFunc
     * @param        array    $expected
     */
    public function testArray(array $data, callable $keyFunc, array $expected): void
    {
        // When
        $result = Reduce::toCountBy($data, $keyFunc);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         toCountBy generator
     * @dataProvider dataProviderForCounts
     * @param        array    $data
     * @param        callable $keyFunc
     * @param        array    $expected
     */
    public function testGenerator(array $data, callable $keyFunc, array $expected): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toCountBy($generator, $keyFunc);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         toCountBy iterator
     * @dataProvider dataProviderForCounts
     * @param        array    $data
     * @param        callable $keyFunc
     * @param        array    $expected
     */
    public function testIterator(array $data, callable $keyFunc, array $expected): void
    {
        // Given
        $iterator = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Reduce::toCountBy($iterator, $keyFunc);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         toCountBy traversable
     * @dataProvider dataProviderForCounts
     * @param        array    $data
     * @param        callable $keyFunc
     * @param        array    $expected
     */
    public function testTraversable(array $data, callable $keyFunc, array $expected): void
    {
        // Given
        $traversable = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Reduce::toCountBy($traversable, $keyFunc);

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForCounts(): array
    {
        return [
            // Empty
            [[], fn ($x) => $x, []],
            // Single element
            [[5], fn ($x) => $x, [5 => 1]],
            [['a'], fn ($x) => $x, ['a' => 1]],
            // Even/odd grouping
            [
                [1, 2, 3, 4, 5, 6],
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd' => 3, 'even' => 3],
            ],
            // Group by length (apple=5, pear=4, banana=6, kiwi=4, plum=4)
            [
                ['apple', 'pear', 'banana', 'kiwi', 'plum'],
                fn ($x) => \strlen($x),
                [5 => 1, 4 => 3, 6 => 1],
            ],
            // Mixed int/string projections (both valid key types)
            [
                [1, 'a', 1, 'b', 'a'],
                fn ($x) => $x,
                [1 => 2, 'a' => 2, 'b' => 1],
            ],
            // Single bucket
            [
                [10, 20, 30],
                fn ($x) => 'all',
                ['all' => 3],
            ],
        ];
    }

    /**
     * @test toCountBy with objects and a property-extracting closure
     */
    public function testObjectsWithPropertyExtractor(): void
    {
        // Given
        $people = [
            (object)['name' => 'Alice', 'city' => 'NYC'],
            (object)['name' => 'Bob',   'city' => 'LA'],
            (object)['name' => 'Carol', 'city' => 'NYC'],
        ];

        // When
        $counts = Reduce::toCountBy($people, fn ($p) => $p->city);

        // Then
        $this->assertEquals(['NYC' => 2, 'LA' => 1], $counts);
    }

    /**
     * @test toCountBy collapses numeric-string keys into int keys (intrinsic PHP array behavior)
     */
    public function testNumericStringKeyCollapse(): void
    {
        // Given (keyFunc returns string "1" for even values and int 1 for odd values)
        $data = [1, 2, 3, 4];

        // When
        $counts = Reduce::toCountBy($data, fn ($x) => $x % 2 === 0 ? '1' : 1);

        // Then PHP coerces string "1" to int 1, collapsing into a single key
        $this->assertCount(1, $counts);
        $this->assertEquals([1 => 4], $counts);
    }

    /**
     * @test         toCountBy throws TypeError when keyFunc returns a non-int|string
     * @dataProvider dataProviderForInvalidKeyTypes
     * @param        callable $keyFunc
     * @param        string   $expectedType
     */
    public function testInvalidKeyTypeThrows(callable $keyFunc, string $expectedType): void
    {
        // Given
        $data = [1, 2, 3];

        // Then
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage($expectedType);

        // When
        Reduce::toCountBy($data, $keyFunc);
    }

    public static function dataProviderForInvalidKeyTypes(): array
    {
        return [
            'bool'   => [fn ($x) => true, 'boolean'],
            'null'   => [fn ($x) => null, 'NULL'],
            'float'  => [fn ($x) => 1.5, 'double'],
            'array'  => [fn ($x) => [$x], 'array'],
            'object' => [fn ($x) => (object)['v' => $x], 'object'],
        ];
    }

    /**
     * @test toCountBy happy path emits no PHP deprecation notices
     */
    public function testNoDeprecationNotices(): void
    {
        // Given
        $data     = [1, 2, 3, 4];
        $raised   = [];
        $previous = \set_error_handler(static function (int $errno, string $errstr) use (&$raised): bool {
            $raised[] = $errstr;
            return true;
        });

        try {
            // When
            $counts = Reduce::toCountBy($data, fn ($x) => $x % 2 === 0 ? 'even' : 'odd');
        } finally {
            \set_error_handler($previous);
        }

        // Then
        $this->assertSame([], $raised);
        $this->assertEquals(['odd' => 2, 'even' => 2], $counts);
    }
}
