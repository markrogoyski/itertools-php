<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class IsPartitionedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArrayByDefaultTrue
     * @dataProvider dataProviderForArrayWithDefaultPredicateTrue
     * @dataProvider dataProviderForArrayWithSpecificPredicateTrue
     * @param array $data
     * @param callable|null $predicate
     */
    public function testArrayTrue(array $data, ?callable $predicate)
    {
        // When
        $result = Summary::isPartitioned($data, $predicate);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForArrayByDefaultTrue(): array
    {
        return [
            [
                [],
                null,
            ],
            [
                [null],
                null,
            ],
            [
                [1],
                null,
            ],
            [
                [0],
                null,
            ],
            [
                [true],
                null,
            ],
            [
                [false],
                null,
            ],
            [
                [true, false],
                null,
            ],
            [
                [1, 0],
                null,
            ],
            [
                [0, 0],
                null,
            ],
            [
                [1, 1],
                null,
            ],
            [
                [1, 1, 0],
                null,
            ],
            [
                [1, 0, 0],
                null,
            ],
            [
                [1, 1, 0, 0],
                null,
            ],
            [
                [true, true, false, false],
                null,
            ],
            [
                [true, 1, '1', 'abc', INF, -INF, [1, 2, 3], (object)[2, 3, 4], false, 0, '0', '', [], null],
                null,
            ],
        ];
    }

    public function dataProviderForArrayWithDefaultPredicateTrue(): array
    {
        return [
            [
                [],
                fn ($item) => \boolval($item),
            ],
            [
                [null],
                fn ($item) => \boolval($item),
            ],
            [
                [1],
                fn ($item) => \boolval($item),
            ],
            [
                [0],
                fn ($item) => \boolval($item),
            ],
            [
                [true],
                fn ($item) => \boolval($item),
            ],
            [
                [false],
                fn ($item) => \boolval($item),
            ],
            [
                [true, false],
                fn ($item) => \boolval($item),
            ],
            [
                [0, 0],
                fn ($item) => \boolval($item),
            ],
            [
                [1, 1],
                fn ($item) => \boolval($item),
            ],
            [
                [1, 0],
                fn ($item) => \boolval($item),
            ],
            [
                [1, 1, 0],
                fn ($item) => \boolval($item),
            ],
            [
                [1, 0, 0],
                fn ($item) => \boolval($item),
            ],
            [
                [1, 1, 0, 0],
                fn ($item) => \boolval($item),
            ],
            [
                [true, true, false, false],
                fn ($item) => \boolval($item),
            ],
            [
                [true, 1, '1', 'abc', INF, -INF, [1, 2, 3], (object)[2, 3, 4], false, 0, '0', '', [], null],
                fn ($item) => \boolval($item),
            ],
        ];
    }

    public function dataProviderForArrayWithSpecificPredicateTrue(): array
    {
        return [
            [
                [],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [0],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [1],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [2],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [0, 0],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [0, 2],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [2, 0],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [2, 2],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [0, 0, 1],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [0, 2, 0, 1],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [2, 2, 1, 3, 5],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [1, 3, 5],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [0, 2, 4, 1, 3, 5],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [4, 0, 2, 3, 1, 5],
                fn ($item) => $item % 2 === 0,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForArrayByDefaultFalse
     * @dataProvider dataProviderForArrayWithDefaultPredicateFalse
     * @dataProvider dataProviderForArrayWithSpecificPredicateFalse
     * @param array $data
     * @param callable|null $predicate
     */
    public function testArrayFalse(array $data, ?callable $predicate)
    {
        // When
        $result = Summary::isPartitioned($data, $predicate);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForArrayByDefaultFalse(): array
    {
        return [
            [
                [false, true],
                null,
            ],
            [
                [0, 1],
                null,
            ],
            [
                [0, 1, 1],
                null,
            ],
            [
                [1, 0, 1],
                null,
            ],
            [
                [0, 0, 1],
                null,
            ],
            [
                [0, 0, 1, 1],
                null,
            ],
            [
                [1, 1, 1, 1, 1, 0, 1, 0, 0, 0, 0, 0, 0],
                fn ($item) => \boolval($item),
            ],
            [
                [false, false, true, true],
                null,
            ],
            [
                [true, false, true, false],
                null,
            ],
            [
                [true, 1, '1', 'abc', INF, -INF, [1, 2, 3], false, (object)[2, 3, 4], 0, '0', '', [], null],
                null,
            ],
            [
                [false, 0, '0', '', [], null, true, 1, '1', 'abc', INF, -INF, [1, 2, 3], (object)[2, 3, 4]],
                null,
            ],
            [
                [1, '1', 'abc', INF, false, 0, '0', '', [], null, true, -INF, [1, 2, 3], (object)[2, 3, 4]],
                null,
            ],
        ];
    }

    public function dataProviderForArrayWithDefaultPredicateFalse(): array
    {
        return [
            [
                [false, true],
                fn ($item) => \boolval($item),
            ],
            [
                [0, 1],
                fn ($item) => \boolval($item),
            ],
            [
                [0, 1, 1],
                fn ($item) => \boolval($item),
            ],
            [
                [1, 0, 1],
                fn ($item) => \boolval($item),
            ],
            [
                [0, 0, 1],
                fn ($item) => \boolval($item),
            ],
            [
                [0, 0, 1, 1],
                fn ($item) => \boolval($item),
            ],
            [
                [1, 1, 1, 1, 1, 0, 1, 0, 0, 0, 0, 0, 0],
                fn ($item) => \boolval($item),
            ],
            [
                [false, false, true, true],
                fn ($item) => \boolval($item),
            ],
            [
                [true, false, true, false],
                fn ($item) => \boolval($item),
            ],
            [
                [true, 1, '1', 'abc', INF, -INF, [1, 2, 3], false, (object)[2, 3, 4], 0, '0', '', [], null],
                fn ($item) => \boolval($item),
            ],
            [
                [false, 0, '0', '', [], null, true, 1, '1', 'abc', INF, -INF, [1, 2, 3], (object)[2, 3, 4]],
                fn ($item) => \boolval($item),
            ],
            [
                [1, '1', 'abc', INF, false, 0, '0', '', [], null, true, -INF, [1, 2, 3], (object)[2, 3, 4]],
                fn ($item) => \boolval($item),
            ],
        ];
    }

    public function dataProviderForArrayWithSpecificPredicateFalse(): array
    {
        return [
            [
                [1, 0],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [1, 2],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [3, 0],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [3, 2],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [1, 0, 1],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [0, 1, 0, 1],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [1, 2, 1, 3, 5],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [1, 3, 5, 0],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [0, 2, 1, 4, 3, 5],
                fn ($item) => $item % 2 === 0,
            ],
            [
                [4, 0, 3, 2, 1, 5],
                fn ($item) => $item % 2 === 0,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGeneratorsByDefaultTrue
     * @dataProvider dataProviderForGeneratorsWithDefaultPredicateTrue
     * @dataProvider dataProviderForGeneratorsWithSpecificPredicateTrue
     * @param \Generator $data
     * @param callable|null $predicate
     */
    public function testGeneratorsTrue(\Generator $data, ?callable $predicate)
    {
        // When
        $result = Summary::isPartitioned($data, $predicate);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForGeneratorsByDefaultTrue(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                null,
            ],
            [
                $gen([null]),
                null,
            ],
            [
                $gen([1]),
                null,
            ],
            [
                $gen([0]),
                null,
            ],
            [
                $gen([true]),
                null,
            ],
            [
                $gen([false]),
                null,
            ],
            [
                $gen([true, false]),
                null,
            ],
            [
                $gen([1, 0]),
                null,
            ],
            [
                $gen([0, 0]),
                null,
            ],
            [
                $gen([1, 1]),
                null,
            ],
            [
                $gen([1, 1, 0]),
                null,
            ],
            [
                $gen([1, 0, 0]),
                null,
            ],
            [
                $gen([1, 1, 0, 0]),
                null,
            ],
            [
                $gen([true, true, false, false]),
                null,
            ],
            [
                $gen([true, 1, '1', 'abc', INF, -INF, [1, 2, 3], (object)[2, 3, 4], false, 0, '0', '', [], null]),
                null,
            ],
        ];
    }

    public function dataProviderForGeneratorsWithDefaultPredicateTrue(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([null]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([1]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([0]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([true]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([false]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([true, false]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([0, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([1, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([1, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([1, 1, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([1, 0, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([1, 1, 0, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([true, true, false, false]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([true, 1, '1', 'abc', INF, -INF, [1, 2, 3], (object)[2, 3, 4], false, 0, '0', '', [], null]),
                fn ($item) => \boolval($item),
            ],
        ];
    }

    public function dataProviderForGeneratorsWithSpecificPredicateTrue(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([1]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([2]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([0, 0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([0, 2]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([2, 0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([2, 2]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([0, 0, 1]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([0, 2, 0, 1]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([2, 2, 1, 3, 5]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([1, 3, 5]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([0, 2, 4, 1, 3, 5]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([4, 0, 2, 3, 1, 5]),
                fn ($item) => $item % 2 === 0,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGeneratorsByDefaultFalse
     * @dataProvider dataProviderForGeneratorsWithDefaultPredicateFalse
     * @dataProvider dataProviderForGeneratorsWithSpecificPredicateFalse
     * @param \Generator $data
     * @param callable|null $predicate
     */
    public function testGeneratorsFalse(\Generator $data, ?callable $predicate)
    {
        // When
        $result = Summary::isPartitioned($data, $predicate);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForGeneratorsByDefaultFalse(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([false, true]),
                null,
            ],
            [
                $gen([0, 1]),
                null,
            ],
            [
                $gen([0, 1, 1]),
                null,
            ],
            [
                $gen([1, 0, 1]),
                null,
            ],
            [
                $gen([0, 0, 1]),
                null,
            ],
            [
                $gen([0, 0, 1, 1]),
                null,
            ],
            [
                $gen([1, 1, 1, 1, 1, 0, 1, 0, 0, 0, 0, 0, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([false, false, true, true]),
                null,
            ],
            [
                $gen([true, false, true, false]),
                null,
            ],
            [
                $gen([true, 1, '1', 'abc', INF, -INF, [1, 2, 3], false, (object)[2, 3, 4], 0, '0', '', [], null]),
                null,
            ],
            [
                $gen([false, 0, '0', '', [], null, true, 1, '1', 'abc', INF, -INF, [1, 2, 3], (object)[2, 3, 4]]),
                null,
            ],
            [
                $gen([1, '1', 'abc', INF, false, 0, '0', '', [], null, true, -INF, [1, 2, 3], (object)[2, 3, 4]]),
                null,
            ],
        ];
    }

    public function dataProviderForGeneratorsWithDefaultPredicateFalse(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([false, true]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([0, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([0, 1, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([1, 0, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([0, 0, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([0, 0, 1, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([1, 1, 1, 1, 1, 0, 1, 0, 0, 0, 0, 0, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([false, false, true, true]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([true, false, true, false]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([true, 1, '1', 'abc', INF, -INF, [1, 2, 3], false, (object)[2, 3, 4], 0, '0', '', [], null]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([false, 0, '0', '', [], null, true, 1, '1', 'abc', INF, -INF, [1, 2, 3], (object)[2, 3, 4]]),
                fn ($item) => \boolval($item),
            ],
            [
                $gen([1, '1', 'abc', INF, false, 0, '0', '', [], null, true, -INF, [1, 2, 3], (object)[2, 3, 4]]),
                fn ($item) => \boolval($item),
            ],
        ];
    }

    public function dataProviderForGeneratorsWithSpecificPredicateFalse(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([1, 0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([1, 2]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([3, 0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([3, 2]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([1, 0, 1]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([0, 1, 0, 1]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([1, 2, 1, 3, 5]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([1, 3, 5, 0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([0, 2, 1, 4, 3, 5]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $gen([4, 0, 3, 2, 1, 5]),
                fn ($item) => $item % 2 === 0,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIteratorsByDefaultTrue
     * @dataProvider dataProviderForIteratorsWithDefaultPredicateTrue
     * @dataProvider dataProviderForIteratorsWithSpecificPredicateTrue
     * @param \Iterator $data
     * @param callable|null $predicate
     */
    public function testIteratorsTrue(\Iterator $data, ?callable $predicate)
    {
        // When
        $result = Summary::isPartitioned($data, $predicate);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForIteratorsByDefaultTrue(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                null,
            ],
            [
                $iter([null]),
                null,
            ],
            [
                $iter([1]),
                null,
            ],
            [
                $iter([0]),
                null,
            ],
            [
                $iter([true]),
                null,
            ],
            [
                $iter([false]),
                null,
            ],
            [
                $iter([true, false]),
                null,
            ],
            [
                $iter([1, 0]),
                null,
            ],
            [
                $iter([0, 0]),
                null,
            ],
            [
                $iter([1, 1]),
                null,
            ],
            [
                $iter([1, 1, 0]),
                null,
            ],
            [
                $iter([1, 0, 0]),
                null,
            ],
            [
                $iter([1, 1, 0, 0]),
                null,
            ],
            [
                $iter([true, true, false, false]),
                null,
            ],
            [
                $iter([true, 1, '1', 'abc', INF, -INF, [1, 2, 3], (object)[2, 3, 4], false, 0, '0', '', [], null]),
                null,
            ],
        ];
    }

    public function dataProviderForIteratorsWithDefaultPredicateTrue(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([null]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([1]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([0]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([true]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([false]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([true, false]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([0, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([1, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([1, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([1, 1, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([1, 0, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([1, 1, 0, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([true, true, false, false]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([true, 1, '1', 'abc', INF, -INF, [1, 2, 3], (object)[2, 3, 4], false, 0, '0', '', [], null]),
                fn ($item) => \boolval($item),
            ],
        ];
    }

    public function dataProviderForIteratorsWithSpecificPredicateTrue(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([1]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([2]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([0, 0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([0, 2]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([2, 0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([2, 2]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([0, 0, 1]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([0, 2, 0, 1]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([2, 2, 1, 3, 5]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([1, 3, 5]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([0, 2, 4, 1, 3, 5]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([4, 0, 2, 3, 1, 5]),
                fn ($item) => $item % 2 === 0,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIteratorsByDefaultFalse
     * @dataProvider dataProviderForIteratorsWithDefaultPredicateFalse
     * @dataProvider dataProviderForIteratorsWithSpecificPredicateFalse
     * @param \Iterator $data
     * @param callable|null $predicate
     */
    public function testIteratorsFalse(\Iterator $data, ?callable $predicate)
    {
        // When
        $result = Summary::isPartitioned($data, $predicate);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForIteratorsByDefaultFalse(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([false, true]),
                null,
            ],
            [
                $iter([0, 1]),
                null,
            ],
            [
                $iter([0, 1, 1]),
                null,
            ],
            [
                $iter([1, 0, 1]),
                null,
            ],
            [
                $iter([0, 0, 1]),
                null,
            ],
            [
                $iter([0, 0, 1, 1]),
                null,
            ],
            [
                $iter([1, 1, 1, 1, 1, 0, 1, 0, 0, 0, 0, 0, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([false, false, true, true]),
                null,
            ],
            [
                $iter([true, false, true, false]),
                null,
            ],
            [
                $iter([true, 1, '1', 'abc', INF, -INF, [1, 2, 3], false, (object)[2, 3, 4], 0, '0', '', [], null]),
                null,
            ],
            [
                $iter([false, 0, '0', '', [], null, true, 1, '1', 'abc', INF, -INF, [1, 2, 3], (object)[2, 3, 4]]),
                null,
            ],
            [
                $iter([1, '1', 'abc', INF, false, 0, '0', '', [], null, true, -INF, [1, 2, 3], (object)[2, 3, 4]]),
                null,
            ],
        ];
    }

    public function dataProviderForIteratorsWithDefaultPredicateFalse(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([false, true]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([0, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([0, 1, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([1, 0, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([0, 0, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([0, 0, 1, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([1, 1, 1, 1, 1, 0, 1, 0, 0, 0, 0, 0, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([false, false, true, true]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([true, false, true, false]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([true, 1, '1', 'abc', INF, -INF, [1, 2, 3], false, (object)[2, 3, 4], 0, '0', '', [], null]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([false, 0, '0', '', [], null, true, 1, '1', 'abc', INF, -INF, [1, 2, 3], (object)[2, 3, 4]]),
                fn ($item) => \boolval($item),
            ],
            [
                $iter([1, '1', 'abc', INF, false, 0, '0', '', [], null, true, -INF, [1, 2, 3], (object)[2, 3, 4]]),
                fn ($item) => \boolval($item),
            ],
        ];
    }

    public function dataProviderForIteratorsWithSpecificPredicateFalse(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([1, 0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([1, 2]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([3, 0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([3, 2]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([1, 0, 1]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([0, 1, 0, 1]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([1, 2, 1, 3, 5]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([1, 3, 5, 0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([0, 2, 1, 4, 3, 5]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $iter([4, 0, 3, 2, 1, 5]),
                fn ($item) => $item % 2 === 0,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversablesByDefaultTrue
     * @dataProvider dataProviderForTraversablesWithDefaultPredicateTrue
     * @dataProvider dataProviderForTraversablesWithSpecificPredicateTrue
     * @param \Traversable $data
     * @param callable|null $predicate
     */
    public function testTraversablesTrue(\Traversable $data, ?callable $predicate)
    {
        // When
        $result = Summary::isPartitioned($data, $predicate);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForTraversablesByDefaultTrue(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                null,
            ],
            [
                $trav([null]),
                null,
            ],
            [
                $trav([1]),
                null,
            ],
            [
                $trav([0]),
                null,
            ],
            [
                $trav([true]),
                null,
            ],
            [
                $trav([false]),
                null,
            ],
            [
                $trav([true, false]),
                null,
            ],
            [
                $trav([1, 0]),
                null,
            ],
            [
                $trav([0, 0]),
                null,
            ],
            [
                $trav([1, 1]),
                null,
            ],
            [
                $trav([1, 1, 0]),
                null,
            ],
            [
                $trav([1, 0, 0]),
                null,
            ],
            [
                $trav([1, 1, 0, 0]),
                null,
            ],
            [
                $trav([true, true, false, false]),
                null,
            ],
            [
                $trav([true, 1, '1', 'abc', INF, -INF, [1, 2, 3], (object)[2, 3, 4], false, 0, '0', '', [], null]),
                null,
            ],
        ];
    }

    public function dataProviderForTraversablesWithDefaultPredicateTrue(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([null]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([1]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([0]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([true]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([false]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([true, false]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([0, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([1, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([1, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([1, 1, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([1, 0, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([1, 1, 0, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([true, true, false, false]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([true, 1, '1', 'abc', INF, -INF, [1, 2, 3], (object)[2, 3, 4], false, 0, '0', '', [], null]),
                fn ($item) => \boolval($item),
            ],
        ];
    }

    public function dataProviderForTraversablesWithSpecificPredicateTrue(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([1]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([2]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([0, 0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([0, 2]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([2, 0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([2, 2]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([0, 0, 1]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([0, 2, 0, 1]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([2, 2, 1, 3, 5]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([1, 3, 5]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([0, 2, 4, 1, 3, 5]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([4, 0, 2, 3, 1, 5]),
                fn ($item) => $item % 2 === 0,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversablesByDefaultFalse
     * @dataProvider dataProviderForTraversablesWithDefaultPredicateFalse
     * @dataProvider dataProviderForTraversablesWithSpecificPredicateFalse
     * @param \Traversable $data
     * @param callable|null $predicate
     */
    public function testTraversablesFalse(\Traversable $data, ?callable $predicate)
    {
        // When
        $result = Summary::isPartitioned($data, $predicate);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForTraversablesByDefaultFalse(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([false, true]),
                null,
            ],
            [
                $trav([0, 1]),
                null,
            ],
            [
                $trav([0, 1, 1]),
                null,
            ],
            [
                $trav([1, 0, 1]),
                null,
            ],
            [
                $trav([0, 0, 1]),
                null,
            ],
            [
                $trav([0, 0, 1, 1]),
                null,
            ],
            [
                $trav([1, 1, 1, 1, 1, 0, 1, 0, 0, 0, 0, 0, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([false, false, true, true]),
                null,
            ],
            [
                $trav([true, false, true, false]),
                null,
            ],
            [
                $trav([true, 1, '1', 'abc', INF, -INF, [1, 2, 3], false, (object)[2, 3, 4], 0, '0', '', [], null]),
                null,
            ],
            [
                $trav([false, 0, '0', '', [], null, true, 1, '1', 'abc', INF, -INF, [1, 2, 3], (object)[2, 3, 4]]),
                null,
            ],
            [
                $trav([1, '1', 'abc', INF, false, 0, '0', '', [], null, true, -INF, [1, 2, 3], (object)[2, 3, 4]]),
                null,
            ],
        ];
    }

    public function dataProviderForTraversablesWithDefaultPredicateFalse(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([false, true]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([0, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([0, 1, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([1, 0, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([0, 0, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([0, 0, 1, 1]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([1, 1, 1, 1, 1, 0, 1, 0, 0, 0, 0, 0, 0]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([false, false, true, true]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([true, false, true, false]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([true, 1, '1', 'abc', INF, -INF, [1, 2, 3], false, (object)[2, 3, 4], 0, '0', '', [], null]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([false, 0, '0', '', [], null, true, 1, '1', 'abc', INF, -INF, [1, 2, 3], (object)[2, 3, 4]]),
                fn ($item) => \boolval($item),
            ],
            [
                $trav([1, '1', 'abc', INF, false, 0, '0', '', [], null, true, -INF, [1, 2, 3], (object)[2, 3, 4]]),
                fn ($item) => \boolval($item),
            ],
        ];
    }

    public function dataProviderForTraversablesWithSpecificPredicateFalse(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([1, 0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([1, 2]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([3, 0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([3, 2]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([1, 0, 1]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([0, 1, 0, 1]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([1, 2, 1, 3, 5]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([1, 3, 5, 0]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([0, 2, 1, 4, 3, 5]),
                fn ($item) => $item % 2 === 0,
            ],
            [
                $trav([4, 0, 3, 2, 1, 5]),
                fn ($item) => $item % 2 === 0,
            ],
        ];
    }
}
