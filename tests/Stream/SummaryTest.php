<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class SummaryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param array    $input
     * @param callable $summaryStreamFactoryFunc
     * @return void
     * @dataProvider dataProviderForArrayTrue
     */
    public function testArrayTrue(array $input, callable $summaryStreamFactoryFunc): void
    {
        // When
        $result = $summaryStreamFactoryFunc($input);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForArrayTrue(): array
    {
        return [
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->isSorted(),
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->isReversed(),
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->sameWith([]),
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->sameCountWith([]),
            ],
            [
                [1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->isSorted(),
            ],
            [
                [1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->sameWith([1, 2, 3]),
            ],
            [
                [1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->sameCountWith([11, 22, 33]),
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($item) => $item > 0)
                    ->runningMax()
                    ->isSorted(),
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($item) => $item < 0)
                    ->runningMin()
                    ->isReversed(),
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMin()
                    ->isSorted(),
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMin()
                    ->isReversed(),
            ],
            [
                [1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allMatch(fn ($x) => $x > 0),
            ],
            [
                [1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->anyMatch(fn ($x) => $x === 3),
            ],
            [
                [1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->noneMatch(fn ($x) => $x === 9),
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->exactlyN(5),
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->exactlyN(5, fn ($x) => $x < 6),
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->exactlyN(2, fn ($x) => $x <= 2),
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(),
            ],
            [
                [1, 1, 2],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 2, 1]),
            ],
            [
                [1, 1, 2],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 1, 2], [2, 1, 1], [1, 2, 1]),
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 2, 3], [2, 1, 3], [3, 1, 2], [1, 3, 2]),
            ],
            [
                ['i', 't', 'e', 'r'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(['r', 'i', 't', 'e'], ['r', 'e', 'i', 't'], ['t', 'i', 'e', 'r'], ['t', 'i', 'r', 'e'], ['t', 'r', 'i', 'e']),
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith(['1', '2', '3']),
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith(['1', '3', '2']),
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith(['3', '2', '1'], [2.0, 1.0, 3.0]),
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith(['3', 2.0, 1], ['2', 1.0, 3]),
            ],
            [
                [1, 2.0, '3'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith([2.0, '1', 3], [3, 2, 1]),
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(),
            ],
            [
                [0],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(),
            ],
            [
                [1],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(),
            ],
            [
                [1, 1, 0, 0],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(),
            ],
            [
                [2, 4, 6],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                [1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                [2, 4, 6, 1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                [1, -1, 3, -3, 2, -2, 5, -5, 4, -4],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->isPartitioned(fn ($item) => $item < 0),
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isEmpty(),
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(),
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(false),
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(),
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(false),
            ],
            [
                [1, '1', true, [1], (object)[1]],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(),
            ],
        ];
    }

    /**
     * @param array    $input
     * @param callable $summaryStreamFactoryFunc
     * @return void
     * @dataProvider dataProviderForArrayFalse
     */
    public function testArrayFalse(array $input, callable $summaryStreamFactoryFunc): void
    {
        // Given
        // When
        $result = $summaryStreamFactoryFunc($input);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForArrayFalse(): array
    {
        return [
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->sameWith([1]),
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->sameCountWith([1]),
            ],
            [
                [1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->isReversed(),
            ],
            [
                [1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->sameWith([11, 22, 33]),
            ],
            [
                [1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->sameCountWith([11, 22, 33, 44, 55]),
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($item) => $item > 0)
                    ->runningMax()
                    ->isReversed(),
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($item) => $item < 0)
                    ->runningMin()
                    ->isSorted(),
            ],
            [
                [1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allMatch(fn ($x) => $x > 3),
            ],
            [
                [1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->anyMatch(fn ($x) => $x === 9),
            ],
            [
                [1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->noneMatch(fn ($x) => $x === 5),
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->exactlyN(3),
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->exactlyN(5, fn ($x) => $x > 6),
            ],
            [
                [1, 1, 2],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 2, 2]),
            ],
            [
                [1, 1, 2],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 1, 2], [2, 1, 1], [1, 2, 2]),
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 2, 3], [2, 1, 3], [3, 2, 2], [1, 3, 2]),
            ],
            [
                [1, 1, 2],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith([1, 2, 2]),
            ],
            [
                [1, 1, 2],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith([1, 1, 2], [2, 1, 1], [1, 2, 2]),
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith([1, 2, 3], [2, 1, 3], [3, 2, 2], [1, 3, 2]),
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(['1', '2', '3']),
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(['1', '3', '2']),
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(['3', '2', '1'], [2.0, 1.0, 3.0]),
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(['3', 2.0, 1], ['2', 1.0, 3]),
            ],
            [
                [1, 2],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                [1, 1, 1, 2],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                [1, 2, 2, 2],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                [2, 4, 1, 6, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                [1, 3, 5, 2, 4, 6],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isEmpty(),
            ],
            [
                [1, 2, 3, 4, 5, 1],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(),
            ],
            [
                [1, 2, 3, 4, 5, '1'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(false),
            ],
            [
                [1, '1', true, [1], (object)[1]],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(false),
            ],
        ];
    }

    /**
     * @param \Generator $input
     * @param callable   $summaryStreamFactoryFunc
     * @return void
     * @dataProvider dataProviderForGeneratorTrue
     */
    public function testGeneratorTrue(\Generator $input, callable $summaryStreamFactoryFunc): void
    {
        // When
        $result = $summaryStreamFactoryFunc($input);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForGeneratorTrue(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->isSorted(),
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->isReversed(),
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->sameWith([]),
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->sameCountWith([]),
            ],
            [
                $gen([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->isSorted(),
            ],
            [
                $gen([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->sameWith([1, 2, 3]),
            ],
            [
                $gen([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->sameCountWith([11, 22, 33]),
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($item) => $item > 0)
                    ->runningMax()
                    ->isSorted(),
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($item) => $item < 0)
                    ->runningMin()
                    ->isReversed(),
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMin()
                    ->isSorted(),
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMin()
                    ->isReversed(),
            ],
            [
                $gen([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allMatch(fn ($x) => $x > 0),
            ],
            [
                $gen([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->anyMatch(fn ($x) => $x === 3),
            ],
            [
                $gen([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->noneMatch(fn ($x) => $x === 9),
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(),
            ],
            [
                $gen([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 2, 1]),
            ],
            [
                $gen([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 1, 2], [2, 1, 1], [1, 2, 1]),
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 2, 3], [2, 1, 3], [3, 1, 2], [1, 3, 2]),
            ],
            [
                $gen(['i', 't', 'e', 'r']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith($gen(['r', 'i', 't', 'e']), $gen(['r', 'e', 'i', 't']), $gen(['t', 'i', 'e', 'r']), $gen(['t', 'i', 'r', 'e']), $gen(['t', 'r', 'i', 'e'])),
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith(['1', '2', '3']),
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith(['1', '3', '2']),
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith(['3', '2', '1'], [2.0, 1.0, 3.0]),
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith(['3', 2.0, 1], ['2', 1.0, 3]),
            ],
            [
                $gen([1, 2.0, '3']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith($gen([2.0, '1', 3]), $gen([3, 2, 1])),
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(),
            ],
            [
                $gen([0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(),
            ],
            [
                $gen([1]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(),
            ],
            [
                $gen([1, 1, 0, 0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(),
            ],
            [
                $gen([2, 4, 6]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $gen([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $gen([2, 4, 6, 1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $gen([1, -1, 3, -3, 2, -2, 5, -5, 4, -4]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->isPartitioned(fn ($item) => $item < 0),
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isEmpty(),
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(),
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(false),
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(),
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(false),
            ],
            [
                $gen([1, '1', true, [1], (object)[1]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(),
            ],
        ];
    }

    /**
     * @param \Generator $input
     * @param callable   $summaryStreamFactoryFunc
     * @return void
     * @dataProvider dataProviderForGeneratorFalse
     */
    public function testGeneratorFalse(\Generator $input, callable $summaryStreamFactoryFunc): void
    {
        // When
        $result = $summaryStreamFactoryFunc($input);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForGeneratorFalse(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->sameWith([1]),
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->sameCountWith([1]),
            ],
            [
                $gen([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->isReversed(),
            ],
            [
                $gen([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->sameWith([11, 22, 33]),
            ],
            [
                $gen([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->sameCountWith([11, 22, 33, 44, 55]),
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($item) => $item > 0)
                    ->runningMax()
                    ->isReversed(),
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($item) => $item < 0)
                    ->runningMin()
                    ->isSorted(),
            ],
            [
                $gen([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allMatch(fn ($x) => $x > 3),
            ],
            [
                $gen([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->anyMatch(fn ($x) => $x === 9),
            ],
            [
                $gen([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->noneMatch(fn ($x) => $x === 5),
            ],
            [
                $gen([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 2, 2]),
            ],
            [
                $gen([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 1, 2], [2, 1, 1], [1, 2, 2]),
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 2, 3], [2, 1, 3], [3, 2, 2], [1, 3, 2]),
            ],
            [
                $gen([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith([1, 2, 2]),
            ],
            [
                $gen([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith([1, 1, 2], [2, 1, 1], [1, 2, 2]),
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith([1, 2, 3], [2, 1, 3], [3, 2, 2], [1, 3, 2]),
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(['1', '2', '3']),
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(['1', '3', '2']),
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(['3', '2', '1'], [2.0, 1.0, 3.0]),
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(['3', 2.0, 1], ['2', 1.0, 3]),
            ],
            [
                $gen([1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $gen([1, 1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $gen([1, 2, 2, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $gen([2, 4, 1, 6, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $gen([1, 3, 5, 2, 4, 6]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isEmpty(),
            ],
            [
                $gen([1, 2, 3, 4, 5, 1]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(),
            ],
            [
                $gen([1, 2, 3, 4, 5, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(false),
            ],
            [
                $gen([1, '1', true, [1], (object)[1]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(false),
            ],
        ];
    }

    /**
     * @param \Iterator $input
     * @param callable  $summaryStreamFactoryFunc
     * @return void
     * @dataProvider dataProviderForIteratorTrue
     */
    public function testIteratorTrue(\Iterator $input, callable $summaryStreamFactoryFunc): void
    {
        // When
        $result = $summaryStreamFactoryFunc($input);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForIteratorTrue(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->isSorted(),
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->isReversed(),
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->sameWith([]),
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->sameCountWith([]),
            ],
            [
                $iter([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->isSorted(),
            ],
            [
                $iter([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->sameWith([1, 2, 3]),
            ],
            [
                $iter([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->sameCountWith([11, 22, 33]),
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($item) => $item > 0)
                    ->runningMax()
                    ->isSorted(),
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($item) => $item < 0)
                    ->runningMin()
                    ->isReversed(),
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMin()
                    ->isSorted(),
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMin()
                    ->isReversed(),
            ],
            [
                $iter([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allMatch(fn ($x) => $x > 0),
            ],
            [
                $iter([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->anyMatch(fn ($x) => $x === 3),
            ],
            [
                $iter([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->noneMatch(fn ($x) => $x === 9),
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(),
            ],
            [
                $iter([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 2, 1]),
            ],
            [
                $iter([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 1, 2], [2, 1, 1], [1, 2, 1]),
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 2, 3], [2, 1, 3], [3, 1, 2], [1, 3, 2]),
            ],
            [
                $iter(['i', 't', 'e', 'r']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith($iter(['r', 'i', 't', 'e']), $iter(['r', 'e', 'i', 't']), $iter(['t', 'i', 'e', 'r']), $iter(['t', 'i', 'r', 'e']), $iter(['t', 'r', 'i', 'e'])),
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith(['1', '2', '3']),
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith(['1', '3', '2']),
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith(['3', '2', '1'], [2.0, 1.0, 3.0]),
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith(['3', 2.0, 1], ['2', 1.0, 3]),
            ],
            [
                $iter([1, 2.0, '3']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith($iter([2.0, '1', 3]), $iter([3, 2, 1])),
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(),
            ],
            [
                $iter([0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(),
            ],
            [
                $iter([1]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(),
            ],
            [
                $iter([1, 1, 0, 0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(),
            ],
            [
                $iter([2, 4, 6]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $iter([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $iter([2, 4, 6, 1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $iter([1, -1, 3, -3, 2, -2, 5, -5, 4, -4]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->isPartitioned(fn ($item) => $item < 0),
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isEmpty(),
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(),
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(false),
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(),
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(false),
            ],
            [
                $iter([1, '1', true, [1], (object)[1]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(),
            ],
        ];
    }

    /**
     * @param \Iterator $input
     * @param callable  $summaryStreamFactoryFunc
     * @return void
     * @dataProvider dataProviderForIteratorFalse
     */
    public function testIteratorFalse(\Iterator $input, callable $summaryStreamFactoryFunc): void
    {
        // When
        $result = $summaryStreamFactoryFunc($input);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForIteratorFalse(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->sameWith([1]),
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->sameCountWith([1]),
            ],
            [
                $iter([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->isReversed(),
            ],
            [
                $iter([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->sameWith([11, 22, 33]),
            ],
            [
                $iter([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->sameCountWith([11, 22, 33, 44, 55]),
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($item) => $item > 0)
                    ->runningMax()
                    ->isReversed(),
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($item) => $item < 0)
                    ->runningMin()
                    ->isSorted(),
            ],
            [
                $iter([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allMatch(fn ($x) => $x > 3),
            ],
            [
                $iter([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->anyMatch(fn ($x) => $x === 9),
            ],
            [
                $iter([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->noneMatch(fn ($x) => $x === 5),
            ],
            [
                $iter([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 2, 2]),
            ],
            [
                $iter([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 1, 2], [2, 1, 1], [1, 2, 2]),
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 2, 3], [2, 1, 3], [3, 2, 2], [1, 3, 2]),
            ],
            [
                $iter([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith([1, 2, 2]),
            ],
            [
                $iter([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith([1, 1, 2], [2, 1, 1], [1, 2, 2]),
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith([1, 2, 3], [2, 1, 3], [3, 2, 2], [1, 3, 2]),
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(['1', '2', '3']),
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(['1', '3', '2']),
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(['3', '2', '1'], [2.0, 1.0, 3.0]),
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(['3', 2.0, 1], ['2', 1.0, 3]),
            ],
            [
                $iter([1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $iter([1, 1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $iter([1, 2, 2, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $iter([2, 4, 1, 6, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $iter([1, 3, 5, 2, 4, 6]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isEmpty(),
            ],
            [
                $iter([1, 2, 3, 4, 5, 1]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(),
            ],
            [
                $iter([1, 2, 3, 4, 5, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(false),
            ],
            [
                $iter([1, '1', true, [1], (object)[1]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(false),
            ],
        ];
    }

    /**
     * @param \Traversable $input
     * @param callable     $summaryStreamFactoryFunc
     * @return void
     * @dataProvider dataProviderForTraversableTrue
     */
    public function testTraversableTrue(\Traversable $input, callable $summaryStreamFactoryFunc): void
    {
        // When
        $result = $summaryStreamFactoryFunc($input);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForTraversableTrue(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->isSorted(),
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->isReversed(),
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->sameWith([]),
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->sameCountWith([]),
            ],
            [
                $trav([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->isSorted(),
            ],
            [
                $trav([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->sameWith([1, 2, 3]),
            ],
            [
                $trav([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->sameCountWith([11, 22, 33]),
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($item) => $item > 0)
                    ->runningMax()
                    ->isSorted(),
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($item) => $item < 0)
                    ->runningMin()
                    ->isReversed(),
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMin()
                    ->isSorted(),
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMin()
                    ->isReversed(),
            ],
            [
                $trav([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allMatch(fn ($x) => $x > 0),
            ],
            [
                $trav([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->anyMatch(fn ($x) => $x === 3),
            ],
            [
                $trav([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->noneMatch(fn ($x) => $x === 9),
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(),
            ],
            [
                $trav([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 2, 1]),
            ],
            [
                $trav([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 1, 2], [2, 1, 1], [1, 2, 1]),
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 2, 3], [2, 1, 3], [3, 1, 2], [1, 3, 2]),
            ],
            [
                $trav(['i', 't', 'e', 'r']),
                fn (iterable $travable) => Stream::of($travable)
                    ->arePermutationsWith($trav(['r', 'i', 't', 'e']), $trav(['r', 'e', 'i', 't']), $trav(['t', 'i', 'e', 'r']), $trav(['t', 'i', 'r', 'e']), $trav(['t', 'r', 'i', 'e'])),
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith(['1', '2', '3']),
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith(['1', '3', '2']),
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith(['3', '2', '1'], [2.0, 1.0, 3.0]),
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith(['3', 2.0, 1], ['2', 1.0, 3]),
            ],
            [
                $trav([1, 2.0, '3']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith($trav([2.0, '1', 3]), $trav([3, 2, 1])),
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(),
            ],
            [
                $trav([0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(),
            ],
            [
                $trav([1]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(),
            ],
            [
                $trav([1, 1, 0, 0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(),
            ],
            [
                $trav([2, 4, 6]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $trav([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $trav([2, 4, 6, 1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $trav([1, -1, 3, -3, 2, -2, 5, -5, 4, -4]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->isPartitioned(fn ($item) => $item < 0),
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isEmpty(),
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(),
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(false),
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(),
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(false),
            ],
            [
                $trav([1, '1', true, [1], (object)[1]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(),
            ],
        ];
    }

    /**
     * @param \Traversable $input
     * @param callable     $summaryStreamFactoryFunc
     * @return void
     * @dataProvider dataProviderForTraversableFalse
     */
    public function testTraversableFalse(\Traversable $input, callable $summaryStreamFactoryFunc): void
    {
        // When
        $result = $summaryStreamFactoryFunc($input);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForTraversableFalse(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->sameWith([1]),
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->sameCountWith([1]),
            ],
            [
                $trav([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->isReversed(),
            ],
            [
                $trav([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->sameWith([11, 22, 33]),
            ],
            [
                $trav([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->sameCountWith([11, 22, 33, 44, 55]),
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($item) => $item > 0)
                    ->runningMax()
                    ->isReversed(),
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($item) => $item < 0)
                    ->runningMin()
                    ->isSorted(),
            ],
            [
                $trav([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allMatch(fn ($x) => $x > 3),
            ],
            [
                $trav([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->anyMatch(fn ($x) => $x === 9),
            ],
            [
                $trav([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->noneMatch(fn ($x) => $x === 5),
            ],
            [
                $trav([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 2, 2]),
            ],
            [
                $trav([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 1, 2], [2, 1, 1], [1, 2, 2]),
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith([1, 2, 3], [2, 1, 3], [3, 2, 2], [1, 3, 2]),
            ],
            [
                $trav([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith([1, 2, 2]),
            ],
            [
                $trav([1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith([1, 1, 2], [2, 1, 1], [1, 2, 2]),
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsCoerciveWith([1, 2, 3], [2, 1, 3], [3, 2, 2], [1, 3, 2]),
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(['1', '2', '3']),
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(['1', '3', '2']),
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(['3', '2', '1'], [2.0, 1.0, 3.0]),
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->arePermutationsWith(['3', 2.0, 1], ['2', 1.0, 3]),
            ],
            [
                $trav([1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $trav([1, 1, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $trav([1, 2, 2, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $trav([2, 4, 1, 6, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $trav([1, 3, 5, 2, 4, 6]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isPartitioned(fn ($item) => $item % 2 === 0),
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->isEmpty(),
            ],
            [
                $trav([1, 2, 3, 4, 5, 1]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(),
            ],
            [
                $trav([1, 2, 3, 4, 5, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(false),
            ],
            [
                $trav([1, '1', true, [1], (object)[1]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->allUnique(false),
            ],
        ];
    }
}
