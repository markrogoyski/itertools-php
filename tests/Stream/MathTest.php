<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class MathTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param array $input
     * @param callable $streamFactoryFunc
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForArray
     * @dataProvider dataProviderForArrayFrequencies
     * @dataProvider dataProviderForArrayTypeSensitive
     */
    public function testArray(array $input, callable $streamFactoryFunc, array $expected): void
    {
        // When
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, applying each stage of the chain in turn:
     *   runningTotal       list(itertools.accumulate(seq))               [initial=i]
     *   runningProduct     list(itertools.accumulate(seq, operator.mul)) [initial=i]
     *   runningDifference  list(itertools.accumulate(seq, operator.sub, initial=0))[1:]
     *   runningAverage     [t / n for n, t in enumerate(itertools.accumulate([initial] + seq), 1)]
     *   runningMax / Min   list(itertools.accumulate(seq, max)) / (seq, min)
     * The int-vs-float result type is the library's contract, not the oracle's: an average is an
     * int when the int running total divides evenly by the count, otherwise a float.
     */
    public static function dataProviderForArray(): array
    {
        return [
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->toArray(),
                [],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage(1)
                    ->toArray(),
                [1],
            ],
            [
                [1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                [3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage(1)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                [1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->runningTotal()
                    ->toArray(),
                [1, 3, 6],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMax()
                    ->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMax()
                    ->runningTotal()
                    ->toArray(),
                [1, 2, 4, 6, 9, 12],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMin()
                    ->toArray(),
                [1, -1, -1, -2, -2, -3],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->toArray(),
                [-1, -3, -6, -10, -15],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->toArray(),
                [-1, -4, -10, -20, -35],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->runningMin()
                    ->toArray(),
                [-1, -4, -10, -20, -35],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->runningMax()
                    ->toArray(),
                [-1, -1, -1, -1, -1],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMin()
                    ->runningTotal()
                    ->toArray(),
                [1, 0, -1, -3, -5, -8],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->toArray(),
                [1, 2, 6, 24, 120],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->toArray(),
                [1, 3, 9, 33, 153],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMax()
                    ->toArray(),
                [1, 3, 9, 33, 153],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMin()
                    ->toArray(),
                [1, 1, 1, 1, 1],
            ],
            [
                [0.5, 1.5, 2.5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningAverage()
                    ->toArray(),
                [0.5, 0.625, 1.0416666666666667],
            ],
            [
                [0.1, 0.2, 0.3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningTotal(0.5)
                    ->runningMax()
                    ->toArray(),
                [0.5, 0.6, 0.8, 1.1],
            ],
            [
                [0.1, 0.2, 0.3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->toArray(),
                [-0.1, -0.4, -1.0],
            ],
        ];
    }

    /**
     * Test data generated with Python (first-seen order; strings only, which Python never equates
     * across types):
     *   c = collections.Counter(seq)                 [collections.Counter(map(len, seq)) for By]
     *   frequencies: dict(c)   relative: {k: float(Fraction(n, len(seq))) for k, n in c.items()}
     */
    public static function dataProviderForArrayFrequencies(): array
    {
        return [
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequencies()
                    ->toAssociativeArray(),
                [],
            ],
            [
                ['a', 'c', 'b', 'c'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequencies()
                    ->toAssociativeArray(),
                ['a' => 1, 'c' => 2, 'b' => 1],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequencies()
                    ->toAssociativeArray(),
                [],
            ],
            [
                ['a', 'c', 'b', 'c'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequencies()
                    ->toAssociativeArray(),
                ['a' => 0.25, 'c' => 0.5, 'b' => 0.25],
            ],
            [
                ['apple', 'pear', 'banana', 'kiwi', 'plum'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequenciesBy(fn ($word) => \strlen($word))
                    ->toAssociativeArray(),
                [5 => 1, 4 => 3, 6 => 1],
            ],
            [
                ['apple', 'pear', 'banana', 'kiwi', 'plum'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequenciesBy(fn ($word) => \strlen($word))
                    ->toAssociativeArray(),
                [5 => 0.2, 4 => 0.6, 6 => 0.2],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it): strict
     * keeps int 1 and string '1' as two values, coercive makes them one. toAssociativeArray
     * merges the two strict keys anyway, because PHP array keys canonicalize '1' to int 1.
     */
    public static function dataProviderForArrayTypeSensitive(): array
    {
        return [
            [
                [1, '1'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequencies()
                    ->toArray(),
                [1, 1],
            ],
            [
                [1, '1'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequencies(false)
                    ->toAssociativeArray(),
                ['1' => 2],
            ],
            [
                [1, '1'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequencies()
                    ->toArray(),
                [0.5, 0.5],
            ],
            [
                [1, '1'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequencies(false)
                    ->toAssociativeArray(),
                ['1' => 1.0],
            ],
        ];
    }

    /**
     * @param \Generator $input
     * @param callable $streamFactoryFunc
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForGenerator
     * @dataProvider dataProviderForGeneratorFrequencies
     * @dataProvider dataProviderForGeneratorTypeSensitive
     */
    public function testGenerator(\Generator $input, callable $streamFactoryFunc, array $expected): void
    {
        // When
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, applying each stage of the chain in turn:
     *   runningTotal       list(itertools.accumulate(seq))               [initial=i]
     *   runningProduct     list(itertools.accumulate(seq, operator.mul)) [initial=i]
     *   runningDifference  list(itertools.accumulate(seq, operator.sub, initial=0))[1:]
     *   runningAverage     [t / n for n, t in enumerate(itertools.accumulate([initial] + seq), 1)]
     *   runningMax / Min   list(itertools.accumulate(seq, max)) / (seq, min)
     * The int-vs-float result type is the library's contract, not the oracle's: an average is an
     * int when the int running total divides evenly by the count, otherwise a float.
     */
    public static function dataProviderForGenerator(): array
    {
        return [
            [
                GeneratorFixture::getGenerator([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->toArray(),
                [],
            ],
            [
                GeneratorFixture::getGenerator([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage(1)
                    ->toArray(),
                [1],
            ],
            [
                GeneratorFixture::getGenerator([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                GeneratorFixture::getGenerator([3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage(1)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                GeneratorFixture::getGenerator([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->runningTotal()
                    ->toArray(),
                [1, 3, 6],
            ],
            [
                GeneratorFixture::getGenerator([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMax()
                    ->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            [
                GeneratorFixture::getGenerator([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMax()
                    ->runningTotal()
                    ->toArray(),
                [1, 2, 4, 6, 9, 12],
            ],
            [
                GeneratorFixture::getGenerator([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMin()
                    ->toArray(),
                [1, -1, -1, -2, -2, -3],
            ],
            [
                GeneratorFixture::getGenerator([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->toArray(),
                [-1, -3, -6, -10, -15],
            ],
            [
                GeneratorFixture::getGenerator([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->toArray(),
                [-1, -4, -10, -20, -35],
            ],
            [
                GeneratorFixture::getGenerator([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->runningMin()
                    ->toArray(),
                [-1, -4, -10, -20, -35],
            ],
            [
                GeneratorFixture::getGenerator([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->runningMax()
                    ->toArray(),
                [-1, -1, -1, -1, -1],
            ],
            [
                GeneratorFixture::getGenerator([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMin()
                    ->runningTotal()
                    ->toArray(),
                [1, 0, -1, -3, -5, -8],
            ],
            [
                GeneratorFixture::getGenerator([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->toArray(),
                [1, 2, 6, 24, 120],
            ],
            [
                GeneratorFixture::getGenerator([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->toArray(),
                [1, 3, 9, 33, 153],
            ],
            [
                GeneratorFixture::getGenerator([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMax()
                    ->toArray(),
                [1, 3, 9, 33, 153],
            ],
            [
                GeneratorFixture::getGenerator([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMin()
                    ->toArray(),
                [1, 1, 1, 1, 1],
            ],
            [
                GeneratorFixture::getGenerator([0.5, 1.5, 2.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningAverage()
                    ->toArray(),
                [0.5, 0.625, 1.0416666666666667],
            ],
            [
                GeneratorFixture::getGenerator([0.1, 0.2, 0.3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningTotal(0.5)
                    ->runningMax()
                    ->toArray(),
                [0.5, 0.6, 0.8, 1.1],
            ],
            [
                GeneratorFixture::getGenerator([0.1, 0.2, 0.3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->toArray(),
                [-0.1, -0.4, -1.0],
            ],
        ];
    }

    /**
     * Test data generated with Python (first-seen order; strings only, which Python never equates
     * across types):
     *   c = collections.Counter(seq)                 [collections.Counter(map(len, seq)) for By]
     *   frequencies: dict(c)   relative: {k: float(Fraction(n, len(seq))) for k, n in c.items()}
     */
    public static function dataProviderForGeneratorFrequencies(): array
    {
        return [
            [
                GeneratorFixture::getGenerator([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequencies()
                    ->toAssociativeArray(),
                [],
            ],
            [
                GeneratorFixture::getGenerator(['a', 'c', 'b', 'c']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequencies()
                    ->toAssociativeArray(),
                ['a' => 1, 'c' => 2, 'b' => 1],
            ],
            [
                GeneratorFixture::getGenerator([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequencies()
                    ->toAssociativeArray(),
                [],
            ],
            [
                GeneratorFixture::getGenerator(['a', 'c', 'b', 'c']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequencies()
                    ->toAssociativeArray(),
                ['a' => 0.25, 'c' => 0.5, 'b' => 0.25],
            ],
            [
                GeneratorFixture::getGenerator(['apple', 'pear', 'banana', 'kiwi', 'plum']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequenciesBy(fn ($word) => \strlen($word))
                    ->toAssociativeArray(),
                [5 => 1, 4 => 3, 6 => 1],
            ],
            [
                GeneratorFixture::getGenerator(['apple', 'pear', 'banana', 'kiwi', 'plum']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequenciesBy(fn ($word) => \strlen($word))
                    ->toAssociativeArray(),
                [5 => 0.2, 4 => 0.6, 6 => 0.2],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it): strict
     * keeps int 1 and string '1' as two values, coercive makes them one. toAssociativeArray
     * merges the two strict keys anyway, because PHP array keys canonicalize '1' to int 1.
     */
    public static function dataProviderForGeneratorTypeSensitive(): array
    {
        return [
            [
                GeneratorFixture::getGenerator([1, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequencies()
                    ->toArray(),
                [1, 1],
            ],
            [
                GeneratorFixture::getGenerator([1, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequencies(false)
                    ->toAssociativeArray(),
                ['1' => 2],
            ],
            [
                GeneratorFixture::getGenerator([1, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequencies()
                    ->toArray(),
                [0.5, 0.5],
            ],
            [
                GeneratorFixture::getGenerator([1, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequencies(false)
                    ->toAssociativeArray(),
                ['1' => 1.0],
            ],
        ];
    }

    /**
     * @param \Iterator $input
     * @param callable $streamFactoryFunc
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForIterator
     * @dataProvider dataProviderForIteratorFrequencies
     * @dataProvider dataProviderForIteratorTypeSensitive
     */
    public function testIterator(\Iterator $input, callable $streamFactoryFunc, array $expected): void
    {
        // When
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, applying each stage of the chain in turn:
     *   runningTotal       list(itertools.accumulate(seq))               [initial=i]
     *   runningProduct     list(itertools.accumulate(seq, operator.mul)) [initial=i]
     *   runningDifference  list(itertools.accumulate(seq, operator.sub, initial=0))[1:]
     *   runningAverage     [t / n for n, t in enumerate(itertools.accumulate([initial] + seq), 1)]
     *   runningMax / Min   list(itertools.accumulate(seq, max)) / (seq, min)
     * The int-vs-float result type is the library's contract, not the oracle's: an average is an
     * int when the int running total divides evenly by the count, otherwise a float.
     */
    public static function dataProviderForIterator(): array
    {
        return [
            [
                new ArrayIteratorFixture([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->toArray(),
                [],
            ],
            [
                new ArrayIteratorFixture([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage(1)
                    ->toArray(),
                [1],
            ],
            [
                new ArrayIteratorFixture([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                new ArrayIteratorFixture([3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage(1)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                new ArrayIteratorFixture([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->runningTotal()
                    ->toArray(),
                [1, 3, 6],
            ],
            [
                new ArrayIteratorFixture([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMax()
                    ->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            [
                new ArrayIteratorFixture([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMax()
                    ->runningTotal()
                    ->toArray(),
                [1, 2, 4, 6, 9, 12],
            ],
            [
                new ArrayIteratorFixture([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMin()
                    ->toArray(),
                [1, -1, -1, -2, -2, -3],
            ],
            [
                new ArrayIteratorFixture([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->toArray(),
                [-1, -3, -6, -10, -15],
            ],
            [
                new ArrayIteratorFixture([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->toArray(),
                [-1, -4, -10, -20, -35],
            ],
            [
                new ArrayIteratorFixture([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->runningMin()
                    ->toArray(),
                [-1, -4, -10, -20, -35],
            ],
            [
                new ArrayIteratorFixture([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->runningMax()
                    ->toArray(),
                [-1, -1, -1, -1, -1],
            ],
            [
                new ArrayIteratorFixture([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMin()
                    ->runningTotal()
                    ->toArray(),
                [1, 0, -1, -3, -5, -8],
            ],
            [
                new ArrayIteratorFixture([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->toArray(),
                [1, 2, 6, 24, 120],
            ],
            [
                new ArrayIteratorFixture([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->toArray(),
                [1, 3, 9, 33, 153],
            ],
            [
                new ArrayIteratorFixture([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMax()
                    ->toArray(),
                [1, 3, 9, 33, 153],
            ],
            [
                new ArrayIteratorFixture([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMin()
                    ->toArray(),
                [1, 1, 1, 1, 1],
            ],
            [
                new ArrayIteratorFixture([0.5, 1.5, 2.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningAverage()
                    ->toArray(),
                [0.5, 0.625, 1.0416666666666667],
            ],
            [
                new ArrayIteratorFixture([0.1, 0.2, 0.3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningTotal(0.5)
                    ->runningMax()
                    ->toArray(),
                [0.5, 0.6, 0.8, 1.1],
            ],
            [
                new ArrayIteratorFixture([0.1, 0.2, 0.3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->toArray(),
                [-0.1, -0.4, -1.0],
            ],
        ];
    }

    /**
     * Test data generated with Python (first-seen order; strings only, which Python never equates
     * across types):
     *   c = collections.Counter(seq)                 [collections.Counter(map(len, seq)) for By]
     *   frequencies: dict(c)   relative: {k: float(Fraction(n, len(seq))) for k, n in c.items()}
     */
    public static function dataProviderForIteratorFrequencies(): array
    {
        return [
            [
                new ArrayIteratorFixture([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequencies()
                    ->toAssociativeArray(),
                [],
            ],
            [
                new ArrayIteratorFixture(['a', 'c', 'b', 'c']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequencies()
                    ->toAssociativeArray(),
                ['a' => 1, 'c' => 2, 'b' => 1],
            ],
            [
                new ArrayIteratorFixture([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequencies()
                    ->toAssociativeArray(),
                [],
            ],
            [
                new ArrayIteratorFixture(['a', 'c', 'b', 'c']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequencies()
                    ->toAssociativeArray(),
                ['a' => 0.25, 'c' => 0.5, 'b' => 0.25],
            ],
            [
                new ArrayIteratorFixture(['apple', 'pear', 'banana', 'kiwi', 'plum']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequenciesBy(fn ($word) => \strlen($word))
                    ->toAssociativeArray(),
                [5 => 1, 4 => 3, 6 => 1],
            ],
            [
                new ArrayIteratorFixture(['apple', 'pear', 'banana', 'kiwi', 'plum']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequenciesBy(fn ($word) => \strlen($word))
                    ->toAssociativeArray(),
                [5 => 0.2, 4 => 0.6, 6 => 0.2],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it): strict
     * keeps int 1 and string '1' as two values, coercive makes them one. toAssociativeArray
     * merges the two strict keys anyway, because PHP array keys canonicalize '1' to int 1.
     */
    public static function dataProviderForIteratorTypeSensitive(): array
    {
        return [
            [
                new ArrayIteratorFixture([1, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequencies()
                    ->toArray(),
                [1, 1],
            ],
            [
                new ArrayIteratorFixture([1, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequencies(false)
                    ->toAssociativeArray(),
                ['1' => 2],
            ],
            [
                new ArrayIteratorFixture([1, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequencies()
                    ->toArray(),
                [0.5, 0.5],
            ],
            [
                new ArrayIteratorFixture([1, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequencies(false)
                    ->toAssociativeArray(),
                ['1' => 1.0],
            ],
        ];
    }

    /**
     * @param \Traversable $input
     * @param callable $streamFactoryFunc
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForTraversable
     * @dataProvider dataProviderForTraversableFrequencies
     * @dataProvider dataProviderForTraversableTypeSensitive
     */
    public function testTraversable(\Traversable $input, callable $streamFactoryFunc, array $expected): void
    {
        // When
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, applying each stage of the chain in turn:
     *   runningTotal       list(itertools.accumulate(seq))               [initial=i]
     *   runningProduct     list(itertools.accumulate(seq, operator.mul)) [initial=i]
     *   runningDifference  list(itertools.accumulate(seq, operator.sub, initial=0))[1:]
     *   runningAverage     [t / n for n, t in enumerate(itertools.accumulate([initial] + seq), 1)]
     *   runningMax / Min   list(itertools.accumulate(seq, max)) / (seq, min)
     * The int-vs-float result type is the library's contract, not the oracle's: an average is an
     * int when the int running total divides evenly by the count, otherwise a float.
     */
    public static function dataProviderForTraversable(): array
    {
        return [
            [
                new IteratorAggregateFixture([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->toArray(),
                [],
            ],
            [
                new IteratorAggregateFixture([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage(1)
                    ->toArray(),
                [1],
            ],
            [
                new IteratorAggregateFixture([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                new IteratorAggregateFixture([3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage(1)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                new IteratorAggregateFixture([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->runningTotal()
                    ->toArray(),
                [1, 3, 6],
            ],
            [
                new IteratorAggregateFixture([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMax()
                    ->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            [
                new IteratorAggregateFixture([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMax()
                    ->runningTotal()
                    ->toArray(),
                [1, 2, 4, 6, 9, 12],
            ],
            [
                new IteratorAggregateFixture([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMin()
                    ->toArray(),
                [1, -1, -1, -2, -2, -3],
            ],
            [
                new IteratorAggregateFixture([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->toArray(),
                [-1, -3, -6, -10, -15],
            ],
            [
                new IteratorAggregateFixture([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->toArray(),
                [-1, -4, -10, -20, -35],
            ],
            [
                new IteratorAggregateFixture([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->runningMin()
                    ->toArray(),
                [-1, -4, -10, -20, -35],
            ],
            [
                new IteratorAggregateFixture([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->runningMax()
                    ->toArray(),
                [-1, -1, -1, -1, -1],
            ],
            [
                new IteratorAggregateFixture([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMin()
                    ->runningTotal()
                    ->toArray(),
                [1, 0, -1, -3, -5, -8],
            ],
            [
                new IteratorAggregateFixture([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->toArray(),
                [1, 2, 6, 24, 120],
            ],
            [
                new IteratorAggregateFixture([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->toArray(),
                [1, 3, 9, 33, 153],
            ],
            [
                new IteratorAggregateFixture([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMax()
                    ->toArray(),
                [1, 3, 9, 33, 153],
            ],
            [
                new IteratorAggregateFixture([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMin()
                    ->toArray(),
                [1, 1, 1, 1, 1],
            ],
            [
                new IteratorAggregateFixture([0.5, 1.5, 2.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningAverage()
                    ->toArray(),
                [0.5, 0.625, 1.0416666666666667],
            ],
            [
                new IteratorAggregateFixture([0.1, 0.2, 0.3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningTotal(0.5)
                    ->runningMax()
                    ->toArray(),
                [0.5, 0.6, 0.8, 1.1],
            ],
            [
                new IteratorAggregateFixture([0.1, 0.2, 0.3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->toArray(),
                [-0.1, -0.4, -1.0],
            ],
        ];
    }

    /**
     * Test data generated with Python (first-seen order; strings only, which Python never equates
     * across types):
     *   c = collections.Counter(seq)                 [collections.Counter(map(len, seq)) for By]
     *   frequencies: dict(c)   relative: {k: float(Fraction(n, len(seq))) for k, n in c.items()}
     */
    public static function dataProviderForTraversableFrequencies(): array
    {
        return [
            [
                new IteratorAggregateFixture([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequencies()
                    ->toAssociativeArray(),
                [],
            ],
            [
                new IteratorAggregateFixture(['a', 'c', 'b', 'c']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequencies()
                    ->toAssociativeArray(),
                ['a' => 1, 'c' => 2, 'b' => 1],
            ],
            [
                new IteratorAggregateFixture([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequencies()
                    ->toAssociativeArray(),
                [],
            ],
            [
                new IteratorAggregateFixture(['a', 'c', 'b', 'c']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequencies()
                    ->toAssociativeArray(),
                ['a' => 0.25, 'c' => 0.5, 'b' => 0.25],
            ],
            [
                new IteratorAggregateFixture(['apple', 'pear', 'banana', 'kiwi', 'plum']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequenciesBy(fn ($word) => \strlen($word))
                    ->toAssociativeArray(),
                [5 => 1, 4 => 3, 6 => 1],
            ],
            [
                new IteratorAggregateFixture(['apple', 'pear', 'banana', 'kiwi', 'plum']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequenciesBy(fn ($word) => \strlen($word))
                    ->toAssociativeArray(),
                [5 => 0.2, 4 => 0.6, 6 => 0.2],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it): strict
     * keeps int 1 and string '1' as two values, coercive makes them one. toAssociativeArray
     * merges the two strict keys anyway, because PHP array keys canonicalize '1' to int 1.
     */
    public static function dataProviderForTraversableTypeSensitive(): array
    {
        return [
            [
                new IteratorAggregateFixture([1, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequencies()
                    ->toArray(),
                [1, 1],
            ],
            [
                new IteratorAggregateFixture([1, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->frequencies(false)
                    ->toAssociativeArray(),
                ['1' => 2],
            ],
            [
                new IteratorAggregateFixture([1, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequencies()
                    ->toArray(),
                [0.5, 0.5],
            ],
            [
                new IteratorAggregateFixture([1, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->relativeFrequencies(false)
                    ->toAssociativeArray(),
                ['1' => 1.0],
            ],
        ];
    }
}
