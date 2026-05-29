<?php

declare(strict_types=1);

namespace IterTools\Tests\Math;

use IterTools\Math;
use IterTools\Tests\Fixture;

class FrequenciesByTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test frequenciesBy example usage
     */
    public function testFrequenciesByExampleUsage(): void
    {
        // Given
        $words = ['apple', 'pear', 'banana', 'kiwi'];

        // And
        $frequencies = [];

        // When
        foreach (Math::frequenciesBy($words, fn ($word) => \strlen($word)) as $length => $frequency) {
            $frequencies[$length] = $frequency;
        }

        // Then
        $this->assertEquals([5 => 1, 4 => 2, 6 => 1], $frequencies);
    }

    /**
     * @test         frequenciesBy array
     * @dataProvider dataProviderForArray
     * @param        array    $data
     * @param        callable $keyFunc
     * @param        array    $expectedKeys
     * @param        array    $expectedFrequencies
     */
    public function testArray(array $data, callable $keyFunc, array $expectedKeys, array $expectedFrequencies): void
    {
        // Given
        $keys        = [];
        $frequencies = [];

        // When
        foreach (Math::frequenciesBy($data, $keyFunc) as $key => $frequency) {
            $keys[]        = $key;
            $frequencies[] = $frequency;
        }

        // Then
        $this->assertEquals($expectedKeys, $keys);
        $this->assertEquals($expectedFrequencies, $frequencies);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [[], fn ($x) => $x, [], []],
            [[5], fn ($x) => $x, [5], [1]],
            [['a'], fn ($x) => $x, ['a'], [1]],
            [
                [1, 2, 3, 4, 5, 6],
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd', 'even'],
                [3, 3],
            ],
            [
                ['apple', 'pear', 'banana', 'kiwi'],
                fn ($x) => \strlen($x),
                [5, 4, 6],
                [1, 2, 1],
            ],
            [
                [10, 20, 30, 40],
                fn ($x) => 'all',
                ['all'],
                [4],
            ],
        ];
    }

    /**
     * @test         frequenciesBy generator
     * @dataProvider dataProviderForGenerator
     * @param        \Generator $data
     * @param        callable   $keyFunc
     * @param        array      $expectedKeys
     * @param        array      $expectedFrequencies
     */
    public function testGenerator(\Generator $data, callable $keyFunc, array $expectedKeys, array $expectedFrequencies): void
    {
        // Given
        $keys        = [];
        $frequencies = [];

        // When
        foreach (Math::frequenciesBy($data, $keyFunc) as $key => $frequency) {
            $keys[]        = $key;
            $frequencies[] = $frequency;
        }

        // Then
        $this->assertEquals($expectedKeys, $keys);
        $this->assertEquals($expectedFrequencies, $frequencies);
    }

    public static function dataProviderForGenerator(): array
    {
        $gen = fn (array $data) => Fixture\GeneratorFixture::getGenerator($data);

        return [
            [$gen([]), fn ($x) => $x, [], []],
            [$gen([5]), fn ($x) => $x, [5], [1]],
            [$gen(['a']), fn ($x) => $x, ['a'], [1]],
            [
                $gen([1, 2, 3, 4, 5, 6]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd', 'even'],
                [3, 3],
            ],
            [
                $gen(['apple', 'pear', 'banana', 'kiwi']),
                fn ($x) => \strlen($x),
                [5, 4, 6],
                [1, 2, 1],
            ],
            [
                $gen([10, 20, 30, 40]),
                fn ($x) => 'all',
                ['all'],
                [4],
            ],
        ];
    }

    /**
     * @test         frequenciesBy iterator
     * @dataProvider dataProviderForIterator
     * @param        \Iterator $data
     * @param        callable  $keyFunc
     * @param        array     $expectedKeys
     * @param        array     $expectedFrequencies
     */
    public function testIterator(\Iterator $data, callable $keyFunc, array $expectedKeys, array $expectedFrequencies): void
    {
        // Given
        $keys        = [];
        $frequencies = [];

        // When
        foreach (Math::frequenciesBy($data, $keyFunc) as $key => $frequency) {
            $keys[]        = $key;
            $frequencies[] = $frequency;
        }

        // Then
        $this->assertEquals($expectedKeys, $keys);
        $this->assertEquals($expectedFrequencies, $frequencies);
    }

    public static function dataProviderForIterator(): array
    {
        $iter = fn (array $data) => new Fixture\ArrayIteratorFixture($data);

        return [
            [$iter([]), fn ($x) => $x, [], []],
            [$iter([5]), fn ($x) => $x, [5], [1]],
            [$iter(['a']), fn ($x) => $x, ['a'], [1]],
            [
                $iter([1, 2, 3, 4, 5, 6]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd', 'even'],
                [3, 3],
            ],
            [
                $iter(['apple', 'pear', 'banana', 'kiwi']),
                fn ($x) => \strlen($x),
                [5, 4, 6],
                [1, 2, 1],
            ],
            [
                $iter([10, 20, 30, 40]),
                fn ($x) => 'all',
                ['all'],
                [4],
            ],
        ];
    }

    /**
     * @test         frequenciesBy traversable
     * @dataProvider dataProviderForTraversable
     * @param        \Traversable $data
     * @param        callable     $keyFunc
     * @param        array        $expectedKeys
     * @param        array        $expectedFrequencies
     */
    public function testTraversable(\Traversable $data, callable $keyFunc, array $expectedKeys, array $expectedFrequencies): void
    {
        // Given
        $keys        = [];
        $frequencies = [];

        // When
        foreach (Math::frequenciesBy($data, $keyFunc) as $key => $frequency) {
            $keys[]        = $key;
            $frequencies[] = $frequency;
        }

        // Then
        $this->assertEquals($expectedKeys, $keys);
        $this->assertEquals($expectedFrequencies, $frequencies);
    }

    public static function dataProviderForTraversable(): array
    {
        $trav = fn (array $data) => new Fixture\IteratorAggregateFixture($data);

        return [
            [$trav([]), fn ($x) => $x, [], []],
            [$trav([5]), fn ($x) => $x, [5], [1]],
            [$trav(['a']), fn ($x) => $x, ['a'], [1]],
            [
                $trav([1, 2, 3, 4, 5, 6]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd', 'even'],
                [3, 3],
            ],
            [
                $trav(['apple', 'pear', 'banana', 'kiwi']),
                fn ($x) => \strlen($x),
                [5, 4, 6],
                [1, 2, 1],
            ],
            [
                $trav([10, 20, 30, 40]),
                fn ($x) => 'all',
                ['all'],
                [4],
            ],
        ];
    }

    /**
     * @test frequenciesBy with objects and a property-extracting closure
     */
    public function testObjectsWithPropertyExtractor(): void
    {
        // Given
        $people = [
            (object)['name' => 'Alice', 'city' => 'NYC'],
            (object)['name' => 'Bob',   'city' => 'LA'],
            (object)['name' => 'Carol', 'city' => 'NYC'],
            (object)['name' => 'Dave',  'city' => 'LA'],
            (object)['name' => 'Eve',   'city' => 'NYC'],
        ];

        // And
        $frequencies = [];

        // When
        foreach (Math::frequenciesBy($people, fn ($p) => $p->city) as $city => $frequency) {
            $frequencies[$city] = $frequency;
        }

        // Then
        $this->assertEquals(['NYC' => 3, 'LA' => 2], $frequencies);
    }

    /**
     * @test frequenciesBy with mixed int/string projections preserves both key types
     */
    public function testMixedIntStringProjectionsStrict(): void
    {
        // Given
        $data = [1, 'a', 2, 'a', 1, 'b'];

        // And
        $keys        = [];
        $frequencies = [];

        // When (strict: numeric-string-vs-int distinction preserved)
        foreach (Math::frequenciesBy($data, fn ($x) => $x, true) as $key => $frequency) {
            $keys[]        = $key;
            $frequencies[] = $frequency;
        }

        // Then
        $this->assertEquals([1, 'a', 2, 'b'], $keys);
        $this->assertEquals([2, 2, 1, 1], $frequencies);
    }

    /**
     * @test frequenciesBy strict vs non-strict value comparison
     */
    public function testStrictVsNonStrict(): void
    {
        // Given (projection returns int 1 and string "1" for different inputs)
        $data = [1, '1', 1, '1'];

        // When strict
        $strict = [];
        foreach (Math::frequenciesBy($data, fn ($x) => $x, true) as $key => $frequency) {
            $strict[] = [$key, $frequency];
        }

        // Then strict keeps int 1 and string "1" distinct
        $this->assertEquals([[1, 2], ['1', 2]], $strict);

        // When non-strict
        $coercive = [];
        foreach (Math::frequenciesBy($data, fn ($x) => $x, false) as $key => $frequency) {
            $coercive[] = [$key, $frequency];
        }

        // Then non-strict collapses int 1 and string "1" together
        $this->assertCount(1, $coercive);
        $this->assertEquals(4, $coercive[0][1]);
    }

    /**
     * @test         frequenciesBy throws TypeError when keyFunc returns a non-int|string
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
        foreach (Math::frequenciesBy($data, $keyFunc) as $_) {
            // drain
        }
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
     * @test frequenciesBy happy path emits no PHP deprecation notices
     */
    public function testNoDeprecationNotices(): void
    {
        // Given
        $data    = [1, 2, 3, 4];
        $raised  = [];
        $previous = \set_error_handler(static function (int $errno, string $errstr) use (&$raised): bool {
            $raised[] = $errstr;
            return true;
        });

        try {
            // When
            $result = [];
            foreach (Math::frequenciesBy($data, fn ($x) => $x % 2 === 0 ? 'even' : 'odd') as $key => $count) {
                $result[$key] = $count;
            }
        } finally {
            \set_error_handler($previous);
        }

        // Then
        $this->assertSame([], $raised);
        $this->assertEquals(['odd' => 2, 'even' => 2], $result);
    }
}
