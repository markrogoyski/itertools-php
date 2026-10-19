<?php

declare(strict_types=1);

namespace IterTools\Tests\Math;

use IterTools\Math;
use IterTools\Tests\Fixture;

class RelativeFrequenciesByTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Bound for the sum of the relative frequencies against 1. Each term is within 0.5 ulp
     * (about 1.1e-16) of its exact value, so the sum of the three terms below is within about
     * 4e-16 of 1.
     */
    private const SUM_DELTA = 1e-12;

    /**
     * Example data generated with Python:
     *   c = collections.Counter(map(len, ['apple', 'pear', 'kiwi', 'plum']))
     *   {k: float(Fraction(n, 4)) for k, n in c.items()}
     *
     * @test relativeFrequenciesBy example usage
     */
    public function testRelativeFrequenciesByExampleUsage(): void
    {
        // Given
        $words = ['apple', 'pear', 'kiwi', 'plum'];

        // And
        $frequencies = [];

        // When (group by length: apple=5, pear=4, kiwi=4, plum=4)
        foreach (Math::relativeFrequenciesBy($words, fn ($word) => \strlen($word)) as $length => $frequency) {
            $frequencies[$length] = $frequency;
        }

        // Then
        $this->assertSame([5 => 0.25, 4 => 0.75], $frequencies);
    }

    /**
     * @test         relativeFrequenciesBy array
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
        foreach (Math::relativeFrequenciesBy($data, $keyFunc) as $key => $frequency) {
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
     *   keys = list(c); frequencies = [float(Fraction(n, len(data))) for n in c.values()]
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
                [1.0],
            ],
            [
                [1, 2, 3, 4],
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd', 'even'],
                [0.5, 0.5],
            ],
            [
                ['apple', 'pear', 'kiwi', 'plum'],
                fn ($x) => \strlen($x),
                [5, 4],
                [0.25, 0.75],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                fn ($x) => $x % 3,
                [1, 2, 0],
                [0.4, 0.3, 0.3],
            ],
            [
                [1, 2, 3],
                fn ($x) => $x,
                [1, 2, 3],
                [0.3333333333333333, 0.3333333333333333, 0.3333333333333333],
            ],
        ];
    }

    /**
     * @test         relativeFrequenciesBy generator
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
        foreach (Math::relativeFrequenciesBy($data, $keyFunc) as $key => $frequency) {
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
     *   keys = list(c); frequencies = [float(Fraction(n, len(data))) for n in c.values()]
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
                [1.0],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd', 'even'],
                [0.5, 0.5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator(['apple', 'pear', 'kiwi', 'plum']),
                fn ($x) => \strlen($x),
                [5, 4],
                [0.25, 0.75],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                fn ($x) => $x % 3,
                [1, 2, 0],
                [0.4, 0.3, 0.3],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => $x,
                [1, 2, 3],
                [0.3333333333333333, 0.3333333333333333, 0.3333333333333333],
            ],
        ];
    }

    /**
     * @test         relativeFrequenciesBy iterator
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
        foreach (Math::relativeFrequenciesBy($data, $keyFunc) as $key => $frequency) {
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
     *   keys = list(c); frequencies = [float(Fraction(n, len(data))) for n in c.values()]
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
                [1.0],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, 4]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd', 'even'],
                [0.5, 0.5],
            ],
            [
                new Fixture\ArrayIteratorFixture(['apple', 'pear', 'kiwi', 'plum']),
                fn ($x) => \strlen($x),
                [5, 4],
                [0.25, 0.75],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                fn ($x) => $x % 3,
                [1, 2, 0],
                [0.4, 0.3, 0.3],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => $x,
                [1, 2, 3],
                [0.3333333333333333, 0.3333333333333333, 0.3333333333333333],
            ],
        ];
    }

    /**
     * @test         relativeFrequenciesBy traversable
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
        foreach (Math::relativeFrequenciesBy($data, $keyFunc) as $key => $frequency) {
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
     *   keys = list(c); frequencies = [float(Fraction(n, len(data))) for n in c.values()]
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
                [1.0],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, 4]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd', 'even'],
                [0.5, 0.5],
            ],
            [
                new Fixture\IteratorAggregateFixture(['apple', 'pear', 'kiwi', 'plum']),
                fn ($x) => \strlen($x),
                [5, 4],
                [0.25, 0.75],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                fn ($x) => $x % 3,
                [1, 2, 0],
                [0.4, 0.3, 0.3],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => $x,
                [1, 2, 3],
                [0.3333333333333333, 0.3333333333333333, 0.3333333333333333],
            ],
        ];
    }

    /**
     * @test relativeFrequenciesBy frequencies sum to 1
     */
    public function testFrequenciesSumToOne(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        // When
        $sum = 0.0;
        foreach (Math::relativeFrequenciesBy($data, fn ($x) => $x % 3) as $frequency) {
            $sum += $frequency;
        }

        // Then
        $this->assertEqualsWithDelta(1.0, $sum, self::SUM_DELTA);
    }

    /**
     * @test relativeFrequenciesBy throws TypeError when keyFunc returns a non-int|string
     */
    public function testInvalidKeyTypeThrows(): void
    {
        // Given
        $data = [1, 2, 3];

        // Then
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('double');

        // When
        foreach (Math::relativeFrequenciesBy($data, fn ($x) => 1.5) as $_) {
            // drain
        }
    }

    /**
     * @test         relativeFrequenciesBy yields a float when there is only one group
     * @dataProvider dataProviderForSingleGroup
     * @param        iterable $data
     * @param        bool     $strict
     * @param        array    $expected
     */
    public function testSingleGroupYieldsFloat(iterable $data, bool $strict, array $expected): void
    {
        // When
        $result = \iterator_to_array(Math::relativeFrequenciesBy($data, fn ($x) => $x % 2 === 0 ? 'even' : 1, $strict));

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForSingleGroup(): array
    {
        return [
            'array one element'      => [[2], true, ['even' => 1.0]],
            'array one group'        => [[2, 4, 6], true, ['even' => 1.0]],
            'array int key'          => [[1, 3, 5], true, [1 => 1.0]],
            'array coercive'         => [[1, 3, 5], false, [1 => 1.0]],
            'generator one element'  => [Fixture\GeneratorFixture::getGenerator([2]), true, ['even' => 1.0]],
            'generator one group'    => [Fixture\GeneratorFixture::getGenerator([2, 4, 6]), true, ['even' => 1.0]],
            'generator int key'      => [Fixture\GeneratorFixture::getGenerator([1, 3, 5]), true, [1 => 1.0]],
            'generator coercive'     => [Fixture\GeneratorFixture::getGenerator([1, 3, 5]), false, [1 => 1.0]],
            'iterator one element'   => [new Fixture\ArrayIteratorFixture([2]), true, ['even' => 1.0]],
            'iterator one group'     => [new Fixture\ArrayIteratorFixture([2, 4, 6]), true, ['even' => 1.0]],
            'iterator int key'       => [new Fixture\ArrayIteratorFixture([1, 3, 5]), true, [1 => 1.0]],
            'iterator coercive'      => [new Fixture\ArrayIteratorFixture([1, 3, 5]), false, [1 => 1.0]],
            'aggregate one element'  => [new Fixture\IteratorAggregateFixture([2]), true, ['even' => 1.0]],
            'aggregate one group'    => [new Fixture\IteratorAggregateFixture([2, 4, 6]), true, ['even' => 1.0]],
            'aggregate int key'      => [new Fixture\IteratorAggregateFixture([1, 3, 5]), true, [1 => 1.0]],
            'aggregate coercive'     => [new Fixture\IteratorAggregateFixture([1, 3, 5]), false, [1 => 1.0]],
        ];
    }
}
