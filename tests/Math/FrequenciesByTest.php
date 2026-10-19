<?php

declare(strict_types=1);

namespace IterTools\Tests\Math;

use IterTools\Math;
use IterTools\Tests\Fixture;

class FrequenciesByTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Example data generated with Python:
     *   collections.Counter(map(len, ['apple', 'pear', 'banana', 'kiwi']))
     *
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
        $this->assertSame([5 => 1, 4 => 2, 6 => 1], $frequencies);
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
        $this->assertSame($expectedKeys, $keys);
        $this->assertSame($expectedFrequencies, $frequencies);
    }

    /**
     * Test data generated with Python (first-seen order), key function as in each row
     * (x, 'even' if x % 2 == 0 else 'odd', len(x), 'all', x % 3):
     *   c = collections.Counter(map(key, data))
     *   keys = list(c); frequencies = list(c.values())
     */
    public static function dataProviderForArray(): array
    {
        return [
            [
                [],
                fn ($x) => $x,
                [],
                [],
            ],
            [
                [5],
                fn ($x) => $x,
                [5],
                [1],
            ],
            [
                ['a'],
                fn ($x) => $x,
                ['a'],
                [1],
            ],
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
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                fn ($x) => $x % 3,
                [1, 2, 0],
                [4, 3, 3],
            ],
            [
                [3, 1, 2, 1, 3, 3],
                fn ($x) => $x,
                [3, 1, 2],
                [3, 2, 1],
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
        $this->assertSame($expectedKeys, $keys);
        $this->assertSame($expectedFrequencies, $frequencies);
    }

    /**
     * Test data generated with Python (first-seen order), key function as in each row
     * (x, 'even' if x % 2 == 0 else 'odd', len(x), 'all', x % 3):
     *   c = collections.Counter(map(key, data))
     *   keys = list(c); frequencies = list(c.values())
     */
    public static function dataProviderForGenerator(): array
    {
        return [
            [
                Fixture\GeneratorFixture::getGenerator([]),
                fn ($x) => $x,
                [],
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([5]),
                fn ($x) => $x,
                [5],
                [1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator(['a']),
                fn ($x) => $x,
                ['a'],
                [1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5, 6]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd', 'even'],
                [3, 3],
            ],
            [
                Fixture\GeneratorFixture::getGenerator(['apple', 'pear', 'banana', 'kiwi']),
                fn ($x) => \strlen($x),
                [5, 4, 6],
                [1, 2, 1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([10, 20, 30, 40]),
                fn ($x) => 'all',
                ['all'],
                [4],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                fn ($x) => $x % 3,
                [1, 2, 0],
                [4, 3, 3],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([3, 1, 2, 1, 3, 3]),
                fn ($x) => $x,
                [3, 1, 2],
                [3, 2, 1],
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
        $this->assertSame($expectedKeys, $keys);
        $this->assertSame($expectedFrequencies, $frequencies);
    }

    /**
     * Test data generated with Python (first-seen order), key function as in each row
     * (x, 'even' if x % 2 == 0 else 'odd', len(x), 'all', x % 3):
     *   c = collections.Counter(map(key, data))
     *   keys = list(c); frequencies = list(c.values())
     */
    public static function dataProviderForIterator(): array
    {
        return [
            [
                new Fixture\ArrayIteratorFixture([]),
                fn ($x) => $x,
                [],
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([5]),
                fn ($x) => $x,
                [5],
                [1],
            ],
            [
                new Fixture\ArrayIteratorFixture(['a']),
                fn ($x) => $x,
                ['a'],
                [1],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5, 6]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd', 'even'],
                [3, 3],
            ],
            [
                new Fixture\ArrayIteratorFixture(['apple', 'pear', 'banana', 'kiwi']),
                fn ($x) => \strlen($x),
                [5, 4, 6],
                [1, 2, 1],
            ],
            [
                new Fixture\ArrayIteratorFixture([10, 20, 30, 40]),
                fn ($x) => 'all',
                ['all'],
                [4],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                fn ($x) => $x % 3,
                [1, 2, 0],
                [4, 3, 3],
            ],
            [
                new Fixture\ArrayIteratorFixture([3, 1, 2, 1, 3, 3]),
                fn ($x) => $x,
                [3, 1, 2],
                [3, 2, 1],
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
        $this->assertSame($expectedKeys, $keys);
        $this->assertSame($expectedFrequencies, $frequencies);
    }

    /**
     * Test data generated with Python (first-seen order), key function as in each row
     * (x, 'even' if x % 2 == 0 else 'odd', len(x), 'all', x % 3):
     *   c = collections.Counter(map(key, data))
     *   keys = list(c); frequencies = list(c.values())
     */
    public static function dataProviderForTraversable(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([]),
                fn ($x) => $x,
                [],
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([5]),
                fn ($x) => $x,
                [5],
                [1],
            ],
            [
                new Fixture\IteratorAggregateFixture(['a']),
                fn ($x) => $x,
                ['a'],
                [1],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5, 6]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd', 'even'],
                [3, 3],
            ],
            [
                new Fixture\IteratorAggregateFixture(['apple', 'pear', 'banana', 'kiwi']),
                fn ($x) => \strlen($x),
                [5, 4, 6],
                [1, 2, 1],
            ],
            [
                new Fixture\IteratorAggregateFixture([10, 20, 30, 40]),
                fn ($x) => 'all',
                ['all'],
                [4],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                fn ($x) => $x % 3,
                [1, 2, 0],
                [4, 3, 3],
            ],
            [
                new Fixture\IteratorAggregateFixture([3, 1, 2, 1, 3, 3]),
                fn ($x) => $x,
                [3, 1, 2],
                [3, 2, 1],
            ],
        ];
    }

    /**
     * Expected values generated with Python:
     *   collections.Counter(['NYC', 'LA', 'NYC', 'LA', 'NYC'])
     *
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
        $this->assertSame(['NYC' => 3, 'LA' => 2], $frequencies);
    }

    /**
     * Expected values generated with Python:
     *   c = collections.Counter([1, 'a', 2, 'a', 1, 'b']); list(c), list(c.values())
     *
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
        $this->assertSame([1, 'a', 2, 'b'], $keys);
        $this->assertSame([2, 2, 1, 1], $frequencies);
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python equates only equal types here:
     * it cannot express int 1 and '1' being one key in coercive mode and two in strict mode).
     *
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
        $this->assertSame([[1, 2], ['1', 2]], $strict);

        // When non-strict
        $coercive = [];
        foreach (Math::frequenciesBy($data, fn ($x) => $x, false) as $key => $frequency) {
            $coercive[] = [$key, $frequency];
        }

        // Then non-strict collapses int 1 and string "1" together
        $this->assertCount(1, $coercive);
        $this->assertSame(4, $coercive[0][1]);
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
     * Expected values generated with Python:
     *   collections.Counter(['odd', 'even', 'odd', 'even'])
     *
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
        $this->assertSame(['odd' => 2, 'even' => 2], $result);
    }
}
