<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ArePermutationsCoerciveTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArrayTrue
     * @param        iterable ...$iterables
     */
    public function testArrayTrue(iterable ...$iterables)
    {
        // When
        $result = Summary::arePermutationsCoercive(...$iterables);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForArrayTrue(): array
    {
        return [
            [],
            [[]],
            [[], []],
            [[], [], []],
            [[1]],
            [[1], [1]],
            [[1], [1], [1]],
            [[1, 1]],
            [[1, 1], [1, 1]],
            [[1, 1], [1, 1], [1, 1]],
            [[1, 2]],
            [[1, 2], [1, 2]],
            [[1, 2], [2, 1]],
            [[2, 1], [1, 2]],
            [[1, 2], [1, 2], [1, 2]],
            [[1, 2], [1, 2], [2, 1]],
            [[1, 2], [2, 1], [1, 2]],
            [[1, 2], [2, 1], [2, 1]],
            [[2, 1], [1, 2], [1, 2]],
            [[2, 1], [1, 2], [2, 1]],
            [[2, 1], [2, 1], [1, 2]],
            [[2, 1], [2, 1], [2, 1]],
            [[1, 1, 1]],
            [[1, 1, 1], [1, 1, 1]],
            [[1, 1, 1], [1, 1, 1], [1, 1, 1]],
            [[1, 1, 2]],
            [[1, 1, 2], [1, 1, 2]],
            [[1, 1, 2], [1, 2, 1]],
            [[1, 1, 2], [2, 1, 1]],
            [[1, 1, 2], [1, 1, 2], [1, 1, 2]],
            [[1, 1, 2], [1, 1, 2], [1, 2, 1]],
            [[1, 1, 2], [1, 1, 2], [2, 1, 1]],
            [[1, 1, 2], [1, 2, 1], [1, 1, 2]],
            [[1, 1, 2], [1, 2, 1], [1, 2, 1]],
            [[1, 1, 2], [1, 2, 1], [2, 1, 1]],
            [[1, 1, 2], [2, 1, 1], [1, 1, 2]],
            [[1, 1, 2], [2, 1, 1], [1, 2, 1]],
            [[1, 1, 2], [2, 1, 1], [2, 1, 1]],
            [[1, 2, 1], [1, 1, 2], [1, 1, 2]],
            [[1, 2, 1], [1, 1, 2], [1, 2, 1]],
            [[1, 2, 1], [1, 1, 2], [2, 1, 1]],
            [[1, 2, 1], [1, 2, 1], [1, 1, 2]],
            [[1, 2, 1], [1, 2, 1], [1, 2, 1]],
            [[1, 2, 1], [1, 2, 1], [2, 1, 1]],
            [[1, 2, 1], [2, 1, 1], [1, 1, 2]],
            [[1, 2, 1], [2, 1, 1], [1, 2, 1]],
            [[1, 2, 1], [2, 1, 1], [2, 1, 1]],
            [[2, 1, 1], [1, 1, 2], [1, 1, 2]],
            [[2, 1, 1], [1, 1, 2], [1, 2, 1]],
            [[2, 1, 1], [1, 1, 2], [2, 1, 1]],
            [[2, 1, 1], [1, 2, 1], [1, 1, 2]],
            [[2, 1, 1], [1, 2, 1], [1, 2, 1]],
            [[2, 1, 1], [1, 2, 1], [2, 1, 1]],
            [[2, 1, 1], [2, 1, 1], [1, 1, 2]],
            [[2, 1, 1], [2, 1, 1], [1, 2, 1]],
            [[2, 1, 1], [2, 1, 1], [2, 1, 1]],
            [[1, 2, 3]],
            [[1, 2, 3], [1, 2, 3]],
            [[1, 2, 3], [1, 3, 2]],
            [[1, 2, 3], [2, 1, 3]],
            [[1, 2, 3], [2, 3, 1]],
            [[1, 2, 3], [3, 1, 2]],
            [[1, 2, 3], [3, 2, 1]],
            [[1, 2, 3], [1, 2, 3], [1, 2, 3]],
            [[1, 2, 3], [1, 2, 3], [1, 3, 2]],
            [[1, 2, 3], [1, 2, 3], [2, 1, 3]],
            [[1, 2, 3], [1, 2, 3], [2, 3, 1]],
            [[1, 2, 3], [1, 2, 3], [3, 1, 2]],
            [[1, 2, 3], [1, 2, 3], [3, 2, 1]],
            [[1, 2, 3], [1, 3, 2], [1, 2, 3]],
            [[1, 2, 3], [1, 3, 2], [1, 3, 2]],
            [[1, 2, 3], [1, 3, 2], [2, 1, 3]],
            [[1, 2, 3], [1, 3, 2], [2, 3, 1]],
            [[1, 2, 3], [1, 3, 2], [3, 1, 2]],
            [[1, 2, 3], [1, 3, 2], [3, 2, 1]],
            [[1, 2, 3], [2, 1, 3], [1, 2, 3]],
            [[1, 2, 3], [2, 1, 3], [1, 3, 2]],
            [[1, 2, 3], [2, 1, 3], [2, 1, 3]],
            [[1, 2, 3], [2, 1, 3], [2, 3, 1]],
            [[1, 2, 3], [2, 1, 3], [3, 1, 2]],
            [[1, 2, 3], [2, 1, 3], [3, 2, 1]],
            [[1, 2, 3], [2, 3, 1], [1, 2, 3]],
            [[1, 2, 3], [2, 3, 1], [1, 3, 2]],
            [[1, 2, 3], [2, 3, 1], [2, 1, 3]],
            [[1, 2, 3], [2, 3, 1], [2, 3, 1]],
            [[1, 2, 3], [2, 3, 1], [3, 1, 2]],
            [[1, 2, 3], [2, 3, 1], [3, 2, 1]],
            [[1, 2, 3], [3, 1, 2], [1, 2, 3]],
            [[1, 2, 3], [3, 1, 2], [1, 3, 2]],
            [[1, 2, 3], [3, 1, 2], [2, 1, 3]],
            [[1, 2, 3], [3, 1, 2], [2, 3, 1]],
            [[1, 2, 3], [3, 1, 2], [3, 1, 2]],
            [[1, 2, 3], [3, 1, 2], [3, 2, 1]],
            [[1, 2, 3], [3, 2, 1], [1, 2, 3]],
            [[1, 2, 3], [3, 2, 1], [1, 3, 2]],
            [[1, 2, 3], [3, 2, 1], [2, 1, 3]],
            [[1, 2, 3], [3, 2, 1], [2, 3, 1]],
            [[1, 2, 3], [3, 2, 1], [3, 1, 2]],
            [[1, 2, 3], [3, 2, 1], [3, 2, 1]],
            [['1', 2.2, 3], ['1', 2.2, 3]],
            [['1', 2.2, 3], ['1', 3, 2.2]],
            [['1', 2.2, 3], [2.2, '1', 3]],
            [['1', 2.2, 3], [2.2, 3, '1']],
            [['1', 2.2, 3], [3, '1', 2.2]],
            [['1', 2.2, 3], [3, 2.2, '1']],
            [[true, [2], null], [true, [2], null]],
            [[true, [2], null], [true, null, [2]]],
            [[true, [2], null], [[2], true, null]],
            [[true, [2], null], [[2], null, true]],
            [[true, [2], null], [null, true, [2]]],
            [[true, [2], null], [null, [2], true]],
            [[1, 2, 1], [1, 2, '1']],
            [[1, 2, 1, 2, 1], [1, 2, 1, 2, '1']],
            [[1, 2, 3], [1, 2, 3], [1, 2, '3']],
            [['1', 2, 3], [1, '2', 3], [1, 2, '3']],
            [[1, 1, 1], [true, 1.0, 1], [true, 1.0, true]],
            [[0, 0, 0], [false, null, 0], [0.0, 0, false]],
        ];
    }

    /**
     * @dataProvider dataProviderForArrayFalse
     * @param        iterable ...$iterables
     */
    public function testArrayFalse(iterable ...$iterables)
    {
        // When
        $result = Summary::arePermutationsCoercive(...$iterables);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForArrayFalse(): array
    {
        return [
            [[1], []],
            [[], [1]],
            [[], [1], []],
            [[1, 2], [1, 2, 1]],
            [[1, 2, 2], [1, 2, 1]],
            [[1, 2, 1], [1, 2, 2]],
            [[1, 2, 1, 2, 1], [1, 2, 1, 2, 2]],
            [[1, 2, 3], [1, 2, 3], [2, 2, 3]],
            [[1, 2, 3], [2, 2, 3], [2, 2, 3]],
            [[2, 2, 3], [1, 2, 3], [2, 2, 3]],
            [[2, 2, 3], [1, 3, 2], [2, 2, 3]],
            [[2, 2, 3], [2, 2, 3], [1, 2, 3]],
            [[1, 2, 3], [1, 2, 3], [2, 3, 4]],
            [[1, 2, 3], [2, 1, 3], [2, 3, 4]],
            [[1, 2, 3], [2, 3, 4], [3, 4, 5]],
            [[1.1, '2', [3], null], [1.1, '2', [3], null], [1.1, '2', [], null]],
            [[1.1, '2', [3], null], [1.1, '2', [], null], [1.1, '2', [], null]],
            [[1.1, '2', [3], null], [1.1, '2', [3], null], [1.1, '1', [3], null]],
            [[1.0, '2', [3], null], [1.1, '2', [3], null], [1.1, '2', [3], null]],
            [[1.0, '2', [3], null], [1.1, '2.0', [3], null], [1.1, '1', [], null]],
            [[1.0, '2', [3], null], [1.1, '2.0', [3], null], [1.1, '1', [], false]],
            [[0, 1, 1], [true, 1.0, 1], [true, 1.0, true]],
            [[1, 0, 0], [false, null, 0], [0.0, 0, false]],
        ];
    }

    /**
     * @dataProvider dataProviderForGeneratorsTrue
     * @param        iterable ...$iterables
     */
    public function testGeneratorsTrue(iterable ...$iterables)
    {
        // When
        $result = Summary::arePermutationsCoercive(...$iterables);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForGeneratorsTrue(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [],
            [$gen([])],
            [$gen([]), $gen([])],
            [$gen([]), $gen([]), $gen([])],
            [$gen([1])],
            [$gen([1]), $gen([1])],
            [$gen([1]), $gen([1]), $gen([1])],
            [$gen([1, 1])],
            [$gen([1, 1]), $gen([1, 1])],
            [$gen([1, 1]), $gen([1, 1]), $gen([1, 1])],
            [$gen([1, 2])],
            [$gen([1, 2]), $gen([1, 2])],
            [$gen([1, 2]), $gen([2, 1])],
            [$gen([2, 1]), $gen([1, 2])],
            [$gen([1, 2]), $gen([1, 2]), $gen([1, 2])],
            [$gen([1, 2]), $gen([1, 2]), $gen([2, 1])],
            [$gen([1, 2]), $gen([2, 1]), $gen([1, 2])],
            [$gen([1, 2]), $gen([2, 1]), $gen([2, 1])],
            [$gen([2, 1]), $gen([1, 2]), $gen([1, 2])],
            [$gen([2, 1]), $gen([1, 2]), $gen([2, 1])],
            [$gen([2, 1]), $gen([2, 1]), $gen([1, 2])],
            [$gen([2, 1]), $gen([2, 1]), $gen([2, 1])],
            [$gen([1, 1, 1])],
            [$gen([1, 1, 1]), $gen([1, 1, 1])],
            [$gen([1, 1, 1]), $gen([1, 1, 1]), $gen([1, 1, 1])],
            [$gen([1, 1, 2])],
            [$gen([1, 1, 2]), $gen([1, 1, 2])],
            [$gen([1, 1, 2]), $gen([1, 2, 1])],
            [$gen([1, 1, 2]), $gen([2, 1, 1])],
            [$gen([1, 1, 2]), $gen([1, 1, 2]), $gen([1, 1, 2])],
            [$gen([1, 1, 2]), $gen([1, 1, 2]), $gen([1, 2, 1])],
            [$gen([1, 1, 2]), $gen([1, 1, 2]), $gen([2, 1, 1])],
            [$gen([1, 1, 2]), $gen([1, 2, 1]), $gen([1, 1, 2])],
            [$gen([1, 1, 2]), $gen([1, 2, 1]), $gen([1, 2, 1])],
            [$gen([1, 1, 2]), $gen([1, 2, 1]), $gen([2, 1, 1])],
            [$gen([1, 1, 2]), $gen([2, 1, 1]), $gen([1, 1, 2])],
            [$gen([1, 1, 2]), $gen([2, 1, 1]), $gen([1, 2, 1])],
            [$gen([1, 1, 2]), $gen([2, 1, 1]), $gen([2, 1, 1])],
            [$gen([1, 2, 1]), $gen([1, 1, 2]), $gen([1, 1, 2])],
            [$gen([1, 2, 1]), $gen([1, 1, 2]), $gen([1, 2, 1])],
            [$gen([1, 2, 1]), $gen([1, 1, 2]), $gen([2, 1, 1])],
            [$gen([1, 2, 1]), $gen([1, 2, 1]), $gen([1, 1, 2])],
            [$gen([1, 2, 1]), $gen([1, 2, 1]), $gen([1, 2, 1])],
            [$gen([1, 2, 1]), $gen([1, 2, 1]), $gen([2, 1, 1])],
            [$gen([1, 2, 1]), $gen([2, 1, 1]), $gen([1, 1, 2])],
            [$gen([1, 2, 1]), $gen([2, 1, 1]), $gen([1, 2, 1])],
            [$gen([1, 2, 1]), $gen([2, 1, 1]), $gen([2, 1, 1])],
            [$gen([2, 1, 1]), $gen([1, 1, 2]), $gen([1, 1, 2])],
            [$gen([2, 1, 1]), $gen([1, 1, 2]), $gen([1, 2, 1])],
            [$gen([2, 1, 1]), $gen([1, 1, 2]), $gen([2, 1, 1])],
            [$gen([2, 1, 1]), $gen([1, 2, 1]), $gen([1, 1, 2])],
            [$gen([2, 1, 1]), $gen([1, 2, 1]), $gen([1, 2, 1])],
            [$gen([2, 1, 1]), $gen([1, 2, 1]), $gen([2, 1, 1])],
            [$gen([2, 1, 1]), $gen([2, 1, 1]), $gen([1, 1, 2])],
            [$gen([2, 1, 1]), $gen([2, 1, 1]), $gen([1, 2, 1])],
            [$gen([2, 1, 1]), $gen([2, 1, 1]), $gen([2, 1, 1])],
            [$gen([1, 2, 3])],
            [$gen([1, 2, 3]), $gen([1, 2, 3])],
            [$gen([1, 2, 3]), $gen([1, 3, 2])],
            [$gen([1, 2, 3]), $gen([2, 1, 3])],
            [$gen([1, 2, 3]), $gen([2, 3, 1])],
            [$gen([1, 2, 3]), $gen([3, 1, 2])],
            [$gen([1, 2, 3]), $gen([3, 2, 1])],
            [$gen([1, 2, 3]), $gen([1, 2, 3]), $gen([1, 2, 3])],
            [$gen([1, 2, 3]), $gen([1, 2, 3]), $gen([1, 3, 2])],
            [$gen([1, 2, 3]), $gen([1, 2, 3]), $gen([2, 1, 3])],
            [$gen([1, 2, 3]), $gen([1, 2, 3]), $gen([2, 3, 1])],
            [$gen([1, 2, 3]), $gen([1, 2, 3]), $gen([3, 1, 2])],
            [$gen([1, 2, 3]), $gen([1, 2, 3]), $gen([3, 2, 1])],
            [$gen([1, 2, 3]), $gen([1, 3, 2]), $gen([1, 2, 3])],
            [$gen([1, 2, 3]), $gen([1, 3, 2]), $gen([1, 3, 2])],
            [$gen([1, 2, 3]), $gen([1, 3, 2]), $gen([2, 1, 3])],
            [$gen([1, 2, 3]), $gen([1, 3, 2]), $gen([2, 3, 1])],
            [$gen([1, 2, 3]), $gen([1, 3, 2]), $gen([3, 1, 2])],
            [$gen([1, 2, 3]), $gen([1, 3, 2]), $gen([3, 2, 1])],
            [$gen([1, 2, 3]), $gen([2, 1, 3]), $gen([1, 2, 3])],
            [$gen([1, 2, 3]), $gen([2, 1, 3]), $gen([1, 3, 2])],
            [$gen([1, 2, 3]), $gen([2, 1, 3]), $gen([2, 1, 3])],
            [$gen([1, 2, 3]), $gen([2, 1, 3]), $gen([2, 3, 1])],
            [$gen([1, 2, 3]), $gen([2, 1, 3]), $gen([3, 1, 2])],
            [$gen([1, 2, 3]), $gen([2, 1, 3]), $gen([3, 2, 1])],
            [$gen([1, 2, 3]), $gen([2, 3, 1]), $gen([1, 2, 3])],
            [$gen([1, 2, 3]), $gen([2, 3, 1]), $gen([1, 3, 2])],
            [$gen([1, 2, 3]), $gen([2, 3, 1]), $gen([2, 1, 3])],
            [$gen([1, 2, 3]), $gen([2, 3, 1]), $gen([2, 3, 1])],
            [$gen([1, 2, 3]), $gen([2, 3, 1]), $gen([3, 1, 2])],
            [$gen([1, 2, 3]), $gen([2, 3, 1]), $gen([3, 2, 1])],
            [$gen([1, 2, 3]), $gen([3, 1, 2]), $gen([1, 2, 3])],
            [$gen([1, 2, 3]), $gen([3, 1, 2]), $gen([1, 3, 2])],
            [$gen([1, 2, 3]), $gen([3, 1, 2]), $gen([2, 1, 3])],
            [$gen([1, 2, 3]), $gen([3, 1, 2]), $gen([2, 3, 1])],
            [$gen([1, 2, 3]), $gen([3, 1, 2]), $gen([3, 1, 2])],
            [$gen([1, 2, 3]), $gen([3, 1, 2]), $gen([3, 2, 1])],
            [$gen([1, 2, 3]), $gen([3, 2, 1]), $gen([1, 2, 3])],
            [$gen([1, 2, 3]), $gen([3, 2, 1]), $gen([1, 3, 2])],
            [$gen([1, 2, 3]), $gen([3, 2, 1]), $gen([2, 1, 3])],
            [$gen([1, 2, 3]), $gen([3, 2, 1]), $gen([2, 3, 1])],
            [$gen([1, 2, 3]), $gen([3, 2, 1]), $gen([3, 1, 2])],
            [$gen([1, 2, 3]), $gen([3, 2, 1]), $gen([3, 2, 1])],
            [$gen(['1', 2.2, 3]), $gen(['1', 2.2, 3])],
            [$gen(['1', 2.2, 3]), $gen(['1', 3, 2.2])],
            [$gen(['1', 2.2, 3]), $gen([2.2, '1', 3])],
            [$gen(['1', 2.2, 3]), $gen([2.2, 3, '1'])],
            [$gen(['1', 2.2, 3]), $gen([3, '1', 2.2])],
            [$gen(['1', 2.2, 3]), $gen([3, 2.2, '1'])],
            [$gen([true, [2], null]), $gen([true, [2], null])],
            [$gen([true, [2], null]), $gen([true, null, [2]])],
            [$gen([true, [2], null]), $gen([[2], true, null])],
            [$gen([true, [2], null]), $gen([[2], null, true])],
            [$gen([true, [2], null]), $gen([null, true, [2]])],
            [$gen([true, [2], null]), $gen([null, [2], true])],
            [$gen([1, 2, 1]), $gen([1, 2, '1'])],
            [$gen([1, 2, 1, 2, 1]), $gen([1, 2, 1, 2, '1'])],
            [$gen([1, 2, 3]), $gen([1, 2, 3]), $gen([1, 2, '3'])],
            [$gen(['1', 2, 3]), $gen([1, '2', 3]), $gen([1, 2, '3'])],
            [$gen([1, 1, 1]), $gen([true, 1.0, 1]), $gen([true, 1.0, true])],
            [$gen([0, 0, 0]), $gen([false, null, 0]), $gen([0.0, 0, false])],
        ];
    }

    /**
     * @dataProvider dataProviderForGeneratorsFalse
     * @param        iterable ...$iterables
     */
    public function testArrayGeneratorsFalse(iterable ...$iterables)
    {
        // When
        $result = Summary::arePermutationsCoercive(...$iterables);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForGeneratorsFalse(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [$gen([1]), $gen([])],
            [$gen([]), $gen([1])],
            [$gen([]), $gen([1]), $gen([])],
            [$gen([1, 2]), $gen([1, 2, 1])],
            [$gen([1, 2, 2]), $gen([1, 2, 1])],
            [$gen([1, 2, 1]), $gen([1, 2, 2])],
            [$gen([1, 2, 1, 2, 1]), $gen([1, 2, 1, 2, 2])],
            [$gen([1, 2, 3]), $gen([1, 2, 3]), $gen([2, 2, 3])],
            [$gen([1, 2, 3]), $gen([2, 2, 3]), $gen([2, 2, 3])],
            [$gen([2, 2, 3]), $gen([1, 2, 3]), $gen([2, 2, 3])],
            [$gen([2, 2, 3]), $gen([1, 3, 2]), $gen([2, 2, 3])],
            [$gen([2, 2, 3]), $gen([2, 2, 3]), $gen([1, 2, 3])],
            [$gen([1, 2, 3]), $gen([1, 2, 3]), $gen([2, 3, 4])],
            [$gen([1, 2, 3]), $gen([2, 1, 3]), $gen([2, 3, 4])],
            [$gen([1, 2, 3]), $gen([2, 3, 4]), $gen([3, 4, 5])],
            [$gen([1.1, '2', [3], null]), $gen([1.1, '2', [3], null]), $gen([1.1, '2', [], null])],
            [$gen([1.1, '2', [3], null]), $gen([1.1, '2', [], null]), $gen([1.1, '2', [], null])],
            [$gen([1.1, '2', [3], null]), $gen([1.1, '2', [3], null]), $gen([1.1, '1', [3], null])],
            [$gen([1.0, '2', [3], null]), $gen([1.1, '2', [3], null]), $gen([1.1, '2', [3], null])],
            [$gen([1.0, '2', [3], null]), $gen([1.1, '2.0', [3], null]), $gen([1.1, '1', [], null])],
            [$gen([1.0, '2', [3], null]), $gen([1.1, '2.0', [3], null]), $gen([1.1, '1', [], false])],
            [$gen([0, 1, 1]), $gen([true, 1.0, 1]), $gen([true, 1.0, true])],
            [$gen([1, 0, 0]), $gen([false, null, 0]), $gen([0.0, 0, false])],
        ];
    }

    /**
     * @dataProvider dataProviderForIteratorsTrue
     * @param        iterable ...$iterables
     */
    public function testIteratorsTrue(iterable ...$iterables)
    {
        // When
        $result = Summary::arePermutationsCoercive(...$iterables);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForIteratorsTrue(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [],
            [$iter([])],
            [$iter([]), $iter([])],
            [$iter([]), $iter([]), $iter([])],
            [$iter([1])],
            [$iter([1]), $iter([1])],
            [$iter([1]), $iter([1]), $iter([1])],
            [$iter([1, 1])],
            [$iter([1, 1]), $iter([1, 1])],
            [$iter([1, 1]), $iter([1, 1]), $iter([1, 1])],
            [$iter([1, 2])],
            [$iter([1, 2]), $iter([1, 2])],
            [$iter([1, 2]), $iter([2, 1])],
            [$iter([2, 1]), $iter([1, 2])],
            [$iter([1, 2]), $iter([1, 2]), $iter([1, 2])],
            [$iter([1, 2]), $iter([1, 2]), $iter([2, 1])],
            [$iter([1, 2]), $iter([2, 1]), $iter([1, 2])],
            [$iter([1, 2]), $iter([2, 1]), $iter([2, 1])],
            [$iter([2, 1]), $iter([1, 2]), $iter([1, 2])],
            [$iter([2, 1]), $iter([1, 2]), $iter([2, 1])],
            [$iter([2, 1]), $iter([2, 1]), $iter([1, 2])],
            [$iter([2, 1]), $iter([2, 1]), $iter([2, 1])],
            [$iter([1, 1, 1])],
            [$iter([1, 1, 1]), $iter([1, 1, 1])],
            [$iter([1, 1, 1]), $iter([1, 1, 1]), $iter([1, 1, 1])],
            [$iter([1, 1, 2])],
            [$iter([1, 1, 2]), $iter([1, 1, 2])],
            [$iter([1, 1, 2]), $iter([1, 2, 1])],
            [$iter([1, 1, 2]), $iter([2, 1, 1])],
            [$iter([1, 1, 2]), $iter([1, 1, 2]), $iter([1, 1, 2])],
            [$iter([1, 1, 2]), $iter([1, 1, 2]), $iter([1, 2, 1])],
            [$iter([1, 1, 2]), $iter([1, 1, 2]), $iter([2, 1, 1])],
            [$iter([1, 1, 2]), $iter([1, 2, 1]), $iter([1, 1, 2])],
            [$iter([1, 1, 2]), $iter([1, 2, 1]), $iter([1, 2, 1])],
            [$iter([1, 1, 2]), $iter([1, 2, 1]), $iter([2, 1, 1])],
            [$iter([1, 1, 2]), $iter([2, 1, 1]), $iter([1, 1, 2])],
            [$iter([1, 1, 2]), $iter([2, 1, 1]), $iter([1, 2, 1])],
            [$iter([1, 1, 2]), $iter([2, 1, 1]), $iter([2, 1, 1])],
            [$iter([1, 2, 1]), $iter([1, 1, 2]), $iter([1, 1, 2])],
            [$iter([1, 2, 1]), $iter([1, 1, 2]), $iter([1, 2, 1])],
            [$iter([1, 2, 1]), $iter([1, 1, 2]), $iter([2, 1, 1])],
            [$iter([1, 2, 1]), $iter([1, 2, 1]), $iter([1, 1, 2])],
            [$iter([1, 2, 1]), $iter([1, 2, 1]), $iter([1, 2, 1])],
            [$iter([1, 2, 1]), $iter([1, 2, 1]), $iter([2, 1, 1])],
            [$iter([1, 2, 1]), $iter([2, 1, 1]), $iter([1, 1, 2])],
            [$iter([1, 2, 1]), $iter([2, 1, 1]), $iter([1, 2, 1])],
            [$iter([1, 2, 1]), $iter([2, 1, 1]), $iter([2, 1, 1])],
            [$iter([2, 1, 1]), $iter([1, 1, 2]), $iter([1, 1, 2])],
            [$iter([2, 1, 1]), $iter([1, 1, 2]), $iter([1, 2, 1])],
            [$iter([2, 1, 1]), $iter([1, 1, 2]), $iter([2, 1, 1])],
            [$iter([2, 1, 1]), $iter([1, 2, 1]), $iter([1, 1, 2])],
            [$iter([2, 1, 1]), $iter([1, 2, 1]), $iter([1, 2, 1])],
            [$iter([2, 1, 1]), $iter([1, 2, 1]), $iter([2, 1, 1])],
            [$iter([2, 1, 1]), $iter([2, 1, 1]), $iter([1, 1, 2])],
            [$iter([2, 1, 1]), $iter([2, 1, 1]), $iter([1, 2, 1])],
            [$iter([2, 1, 1]), $iter([2, 1, 1]), $iter([2, 1, 1])],
            [$iter([1, 2, 3])],
            [$iter([1, 2, 3]), $iter([1, 2, 3])],
            [$iter([1, 2, 3]), $iter([1, 3, 2])],
            [$iter([1, 2, 3]), $iter([2, 1, 3])],
            [$iter([1, 2, 3]), $iter([2, 3, 1])],
            [$iter([1, 2, 3]), $iter([3, 1, 2])],
            [$iter([1, 2, 3]), $iter([3, 2, 1])],
            [$iter([1, 2, 3]), $iter([1, 2, 3]), $iter([1, 2, 3])],
            [$iter([1, 2, 3]), $iter([1, 2, 3]), $iter([1, 3, 2])],
            [$iter([1, 2, 3]), $iter([1, 2, 3]), $iter([2, 1, 3])],
            [$iter([1, 2, 3]), $iter([1, 2, 3]), $iter([2, 3, 1])],
            [$iter([1, 2, 3]), $iter([1, 2, 3]), $iter([3, 1, 2])],
            [$iter([1, 2, 3]), $iter([1, 2, 3]), $iter([3, 2, 1])],
            [$iter([1, 2, 3]), $iter([1, 3, 2]), $iter([1, 2, 3])],
            [$iter([1, 2, 3]), $iter([1, 3, 2]), $iter([1, 3, 2])],
            [$iter([1, 2, 3]), $iter([1, 3, 2]), $iter([2, 1, 3])],
            [$iter([1, 2, 3]), $iter([1, 3, 2]), $iter([2, 3, 1])],
            [$iter([1, 2, 3]), $iter([1, 3, 2]), $iter([3, 1, 2])],
            [$iter([1, 2, 3]), $iter([1, 3, 2]), $iter([3, 2, 1])],
            [$iter([1, 2, 3]), $iter([2, 1, 3]), $iter([1, 2, 3])],
            [$iter([1, 2, 3]), $iter([2, 1, 3]), $iter([1, 3, 2])],
            [$iter([1, 2, 3]), $iter([2, 1, 3]), $iter([2, 1, 3])],
            [$iter([1, 2, 3]), $iter([2, 1, 3]), $iter([2, 3, 1])],
            [$iter([1, 2, 3]), $iter([2, 1, 3]), $iter([3, 1, 2])],
            [$iter([1, 2, 3]), $iter([2, 1, 3]), $iter([3, 2, 1])],
            [$iter([1, 2, 3]), $iter([2, 3, 1]), $iter([1, 2, 3])],
            [$iter([1, 2, 3]), $iter([2, 3, 1]), $iter([1, 3, 2])],
            [$iter([1, 2, 3]), $iter([2, 3, 1]), $iter([2, 1, 3])],
            [$iter([1, 2, 3]), $iter([2, 3, 1]), $iter([2, 3, 1])],
            [$iter([1, 2, 3]), $iter([2, 3, 1]), $iter([3, 1, 2])],
            [$iter([1, 2, 3]), $iter([2, 3, 1]), $iter([3, 2, 1])],
            [$iter([1, 2, 3]), $iter([3, 1, 2]), $iter([1, 2, 3])],
            [$iter([1, 2, 3]), $iter([3, 1, 2]), $iter([1, 3, 2])],
            [$iter([1, 2, 3]), $iter([3, 1, 2]), $iter([2, 1, 3])],
            [$iter([1, 2, 3]), $iter([3, 1, 2]), $iter([2, 3, 1])],
            [$iter([1, 2, 3]), $iter([3, 1, 2]), $iter([3, 1, 2])],
            [$iter([1, 2, 3]), $iter([3, 1, 2]), $iter([3, 2, 1])],
            [$iter([1, 2, 3]), $iter([3, 2, 1]), $iter([1, 2, 3])],
            [$iter([1, 2, 3]), $iter([3, 2, 1]), $iter([1, 3, 2])],
            [$iter([1, 2, 3]), $iter([3, 2, 1]), $iter([2, 1, 3])],
            [$iter([1, 2, 3]), $iter([3, 2, 1]), $iter([2, 3, 1])],
            [$iter([1, 2, 3]), $iter([3, 2, 1]), $iter([3, 1, 2])],
            [$iter([1, 2, 3]), $iter([3, 2, 1]), $iter([3, 2, 1])],
            [$iter(['1', 2.2, 3]), $iter(['1', 2.2, 3])],
            [$iter(['1', 2.2, 3]), $iter(['1', 3, 2.2])],
            [$iter(['1', 2.2, 3]), $iter([2.2, '1', 3])],
            [$iter(['1', 2.2, 3]), $iter([2.2, 3, '1'])],
            [$iter(['1', 2.2, 3]), $iter([3, '1', 2.2])],
            [$iter(['1', 2.2, 3]), $iter([3, 2.2, '1'])],
            [$iter([true, [2], null]), $iter([true, [2], null])],
            [$iter([true, [2], null]), $iter([true, null, [2]])],
            [$iter([true, [2], null]), $iter([[2], true, null])],
            [$iter([true, [2], null]), $iter([[2], null, true])],
            [$iter([true, [2], null]), $iter([null, true, [2]])],
            [$iter([true, [2], null]), $iter([null, [2], true])],
            [$iter([1, 2, 1]), $iter([1, 2, '1'])],
            [$iter([1, 2, 1, 2, 1]), $iter([1, 2, 1, 2, '1'])],
            [$iter([1, 2, 3]), $iter([1, 2, 3]), $iter([1, 2, '3'])],
            [$iter(['1', 2, 3]), $iter([1, '2', 3]), $iter([1, 2, '3'])],
            [$iter([1, 1, 1]), $iter([true, 1.0, 1]), $iter([true, 1.0, true])],
            [$iter([0, 0, 0]), $iter([false, null, 0]), $iter([0.0, 0, false])],
        ];
    }

    /**
     * @dataProvider dataProviderForIteratorsFalse
     * @param        iterable ...$iterables
     */
    public function testArrayIteratorsFalse(iterable ...$iterables)
    {
        // When
        $result = Summary::arePermutationsCoercive(...$iterables);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForIteratorsFalse(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [$iter([1]), $iter([])],
            [$iter([]), $iter([1])],
            [$iter([]), $iter([1]), $iter([])],
            [$iter([1, 2]), $iter([1, 2, 1])],
            [$iter([1, 2, 2]), $iter([1, 2, 1])],
            [$iter([1, 2, 1]), $iter([1, 2, 2])],
            [$iter([1, 2, 1, 2, 1]), $iter([1, 2, 1, 2, 2])],
            [$iter([1, 2, 3]), $iter([1, 2, 3]), $iter([2, 2, 3])],
            [$iter([1, 2, 3]), $iter([2, 2, 3]), $iter([2, 2, 3])],
            [$iter([2, 2, 3]), $iter([1, 2, 3]), $iter([2, 2, 3])],
            [$iter([2, 2, 3]), $iter([1, 3, 2]), $iter([2, 2, 3])],
            [$iter([2, 2, 3]), $iter([2, 2, 3]), $iter([1, 2, 3])],
            [$iter([1, 2, 3]), $iter([1, 2, 3]), $iter([2, 3, 4])],
            [$iter([1, 2, 3]), $iter([2, 1, 3]), $iter([2, 3, 4])],
            [$iter([1, 2, 3]), $iter([2, 3, 4]), $iter([3, 4, 5])],
            [$iter([1.1, '2', [3], null]), $iter([1.1, '2', [3], null]), $iter([1.1, '2', [], null])],
            [$iter([1.1, '2', [3], null]), $iter([1.1, '2', [], null]), $iter([1.1, '2', [], null])],
            [$iter([1.1, '2', [3], null]), $iter([1.1, '2', [3], null]), $iter([1.1, '1', [3], null])],
            [$iter([1.0, '2', [3], null]), $iter([1.1, '2', [3], null]), $iter([1.1, '2', [3], null])],
            [$iter([1.0, '2', [3], null]), $iter([1.1, '2.0', [3], null]), $iter([1.1, '1', [], null])],
            [$iter([1.0, '2', [3], null]), $iter([1.1, '2.0', [3], null]), $iter([1.1, '1', [], false])],
            [$iter([0, 1, 1]), $iter([true, 1.0, 1]), $iter([true, 1.0, true])],
            [$iter([1, 0, 0]), $iter([false, null, 0]), $iter([0.0, 0, false])],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversablesTrue
     * @param        iterable ...$iterables
     */
    public function testTraversablesTrue(iterable ...$iterables)
    {
        // When
        $result = Summary::arePermutationsCoercive(...$iterables);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForTraversablesTrue(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [],
            [$trav([])],
            [$trav([]), $trav([])],
            [$trav([]), $trav([]), $trav([])],
            [$trav([1])],
            [$trav([1]), $trav([1])],
            [$trav([1]), $trav([1]), $trav([1])],
            [$trav([1, 1])],
            [$trav([1, 1]), $trav([1, 1])],
            [$trav([1, 1]), $trav([1, 1]), $trav([1, 1])],
            [$trav([1, 2])],
            [$trav([1, 2]), $trav([1, 2])],
            [$trav([1, 2]), $trav([2, 1])],
            [$trav([2, 1]), $trav([1, 2])],
            [$trav([1, 2]), $trav([1, 2]), $trav([1, 2])],
            [$trav([1, 2]), $trav([1, 2]), $trav([2, 1])],
            [$trav([1, 2]), $trav([2, 1]), $trav([1, 2])],
            [$trav([1, 2]), $trav([2, 1]), $trav([2, 1])],
            [$trav([2, 1]), $trav([1, 2]), $trav([1, 2])],
            [$trav([2, 1]), $trav([1, 2]), $trav([2, 1])],
            [$trav([2, 1]), $trav([2, 1]), $trav([1, 2])],
            [$trav([2, 1]), $trav([2, 1]), $trav([2, 1])],
            [$trav([1, 1, 1])],
            [$trav([1, 1, 1]), $trav([1, 1, 1])],
            [$trav([1, 1, 1]), $trav([1, 1, 1]), $trav([1, 1, 1])],
            [$trav([1, 1, 2])],
            [$trav([1, 1, 2]), $trav([1, 1, 2])],
            [$trav([1, 1, 2]), $trav([1, 2, 1])],
            [$trav([1, 1, 2]), $trav([2, 1, 1])],
            [$trav([1, 1, 2]), $trav([1, 1, 2]), $trav([1, 1, 2])],
            [$trav([1, 1, 2]), $trav([1, 1, 2]), $trav([1, 2, 1])],
            [$trav([1, 1, 2]), $trav([1, 1, 2]), $trav([2, 1, 1])],
            [$trav([1, 1, 2]), $trav([1, 2, 1]), $trav([1, 1, 2])],
            [$trav([1, 1, 2]), $trav([1, 2, 1]), $trav([1, 2, 1])],
            [$trav([1, 1, 2]), $trav([1, 2, 1]), $trav([2, 1, 1])],
            [$trav([1, 1, 2]), $trav([2, 1, 1]), $trav([1, 1, 2])],
            [$trav([1, 1, 2]), $trav([2, 1, 1]), $trav([1, 2, 1])],
            [$trav([1, 1, 2]), $trav([2, 1, 1]), $trav([2, 1, 1])],
            [$trav([1, 2, 1]), $trav([1, 1, 2]), $trav([1, 1, 2])],
            [$trav([1, 2, 1]), $trav([1, 1, 2]), $trav([1, 2, 1])],
            [$trav([1, 2, 1]), $trav([1, 1, 2]), $trav([2, 1, 1])],
            [$trav([1, 2, 1]), $trav([1, 2, 1]), $trav([1, 1, 2])],
            [$trav([1, 2, 1]), $trav([1, 2, 1]), $trav([1, 2, 1])],
            [$trav([1, 2, 1]), $trav([1, 2, 1]), $trav([2, 1, 1])],
            [$trav([1, 2, 1]), $trav([2, 1, 1]), $trav([1, 1, 2])],
            [$trav([1, 2, 1]), $trav([2, 1, 1]), $trav([1, 2, 1])],
            [$trav([1, 2, 1]), $trav([2, 1, 1]), $trav([2, 1, 1])],
            [$trav([2, 1, 1]), $trav([1, 1, 2]), $trav([1, 1, 2])],
            [$trav([2, 1, 1]), $trav([1, 1, 2]), $trav([1, 2, 1])],
            [$trav([2, 1, 1]), $trav([1, 1, 2]), $trav([2, 1, 1])],
            [$trav([2, 1, 1]), $trav([1, 2, 1]), $trav([1, 1, 2])],
            [$trav([2, 1, 1]), $trav([1, 2, 1]), $trav([1, 2, 1])],
            [$trav([2, 1, 1]), $trav([1, 2, 1]), $trav([2, 1, 1])],
            [$trav([2, 1, 1]), $trav([2, 1, 1]), $trav([1, 1, 2])],
            [$trav([2, 1, 1]), $trav([2, 1, 1]), $trav([1, 2, 1])],
            [$trav([2, 1, 1]), $trav([2, 1, 1]), $trav([2, 1, 1])],
            [$trav([1, 2, 3])],
            [$trav([1, 2, 3]), $trav([1, 2, 3])],
            [$trav([1, 2, 3]), $trav([1, 3, 2])],
            [$trav([1, 2, 3]), $trav([2, 1, 3])],
            [$trav([1, 2, 3]), $trav([2, 3, 1])],
            [$trav([1, 2, 3]), $trav([3, 1, 2])],
            [$trav([1, 2, 3]), $trav([3, 2, 1])],
            [$trav([1, 2, 3]), $trav([1, 2, 3]), $trav([1, 2, 3])],
            [$trav([1, 2, 3]), $trav([1, 2, 3]), $trav([1, 3, 2])],
            [$trav([1, 2, 3]), $trav([1, 2, 3]), $trav([2, 1, 3])],
            [$trav([1, 2, 3]), $trav([1, 2, 3]), $trav([2, 3, 1])],
            [$trav([1, 2, 3]), $trav([1, 2, 3]), $trav([3, 1, 2])],
            [$trav([1, 2, 3]), $trav([1, 2, 3]), $trav([3, 2, 1])],
            [$trav([1, 2, 3]), $trav([1, 3, 2]), $trav([1, 2, 3])],
            [$trav([1, 2, 3]), $trav([1, 3, 2]), $trav([1, 3, 2])],
            [$trav([1, 2, 3]), $trav([1, 3, 2]), $trav([2, 1, 3])],
            [$trav([1, 2, 3]), $trav([1, 3, 2]), $trav([2, 3, 1])],
            [$trav([1, 2, 3]), $trav([1, 3, 2]), $trav([3, 1, 2])],
            [$trav([1, 2, 3]), $trav([1, 3, 2]), $trav([3, 2, 1])],
            [$trav([1, 2, 3]), $trav([2, 1, 3]), $trav([1, 2, 3])],
            [$trav([1, 2, 3]), $trav([2, 1, 3]), $trav([1, 3, 2])],
            [$trav([1, 2, 3]), $trav([2, 1, 3]), $trav([2, 1, 3])],
            [$trav([1, 2, 3]), $trav([2, 1, 3]), $trav([2, 3, 1])],
            [$trav([1, 2, 3]), $trav([2, 1, 3]), $trav([3, 1, 2])],
            [$trav([1, 2, 3]), $trav([2, 1, 3]), $trav([3, 2, 1])],
            [$trav([1, 2, 3]), $trav([2, 3, 1]), $trav([1, 2, 3])],
            [$trav([1, 2, 3]), $trav([2, 3, 1]), $trav([1, 3, 2])],
            [$trav([1, 2, 3]), $trav([2, 3, 1]), $trav([2, 1, 3])],
            [$trav([1, 2, 3]), $trav([2, 3, 1]), $trav([2, 3, 1])],
            [$trav([1, 2, 3]), $trav([2, 3, 1]), $trav([3, 1, 2])],
            [$trav([1, 2, 3]), $trav([2, 3, 1]), $trav([3, 2, 1])],
            [$trav([1, 2, 3]), $trav([3, 1, 2]), $trav([1, 2, 3])],
            [$trav([1, 2, 3]), $trav([3, 1, 2]), $trav([1, 3, 2])],
            [$trav([1, 2, 3]), $trav([3, 1, 2]), $trav([2, 1, 3])],
            [$trav([1, 2, 3]), $trav([3, 1, 2]), $trav([2, 3, 1])],
            [$trav([1, 2, 3]), $trav([3, 1, 2]), $trav([3, 1, 2])],
            [$trav([1, 2, 3]), $trav([3, 1, 2]), $trav([3, 2, 1])],
            [$trav([1, 2, 3]), $trav([3, 2, 1]), $trav([1, 2, 3])],
            [$trav([1, 2, 3]), $trav([3, 2, 1]), $trav([1, 3, 2])],
            [$trav([1, 2, 3]), $trav([3, 2, 1]), $trav([2, 1, 3])],
            [$trav([1, 2, 3]), $trav([3, 2, 1]), $trav([2, 3, 1])],
            [$trav([1, 2, 3]), $trav([3, 2, 1]), $trav([3, 1, 2])],
            [$trav([1, 2, 3]), $trav([3, 2, 1]), $trav([3, 2, 1])],
            [$trav(['1', 2.2, 3]), $trav(['1', 2.2, 3])],
            [$trav(['1', 2.2, 3]), $trav(['1', 3, 2.2])],
            [$trav(['1', 2.2, 3]), $trav([2.2, '1', 3])],
            [$trav(['1', 2.2, 3]), $trav([2.2, 3, '1'])],
            [$trav(['1', 2.2, 3]), $trav([3, '1', 2.2])],
            [$trav(['1', 2.2, 3]), $trav([3, 2.2, '1'])],
            [$trav([true, [2], null]), $trav([true, [2], null])],
            [$trav([true, [2], null]), $trav([true, null, [2]])],
            [$trav([true, [2], null]), $trav([[2], true, null])],
            [$trav([true, [2], null]), $trav([[2], null, true])],
            [$trav([true, [2], null]), $trav([null, true, [2]])],
            [$trav([true, [2], null]), $trav([null, [2], true])],
            [$trav([1, 2, 1]), $trav([1, 2, '1'])],
            [$trav([1, 2, 1, 2, 1]), $trav([1, 2, 1, 2, '1'])],
            [$trav([1, 2, 3]), $trav([1, 2, 3]), $trav([1, 2, '3'])],
            [$trav(['1', 2, 3]), $trav([1, '2', 3]), $trav([1, 2, '3'])],
            [$trav([1, 1, 1]), $trav([true, 1.0, 1]), $trav([true, 1.0, true])],
            [$trav([0, 0, 0]), $trav([false, null, 0]), $trav([0.0, 0, false])],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversablesFalse
     * @param        iterable ...$iterables
     */
    public function testArrayTraversablesFalse(iterable ...$iterables)
    {
        // When
        $result = Summary::arePermutationsCoercive(...$iterables);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForTraversablesFalse(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [$trav([1]), $trav([])],
            [$trav([]), $trav([1])],
            [$trav([]), $trav([1]), $trav([])],
            [$trav([1, 2]), $trav([1, 2, 1])],
            [$trav([1, 2, 2]), $trav([1, 2, 1])],
            [$trav([1, 2, 1]), $trav([1, 2, 2])],
            [$trav([1, 2, 1, 2, 1]), $trav([1, 2, 1, 2, 2])],
            [$trav([1, 2, 3]), $trav([1, 2, 3]), $trav([2, 2, 3])],
            [$trav([1, 2, 3]), $trav([2, 2, 3]), $trav([2, 2, 3])],
            [$trav([2, 2, 3]), $trav([1, 2, 3]), $trav([2, 2, 3])],
            [$trav([2, 2, 3]), $trav([1, 3, 2]), $trav([2, 2, 3])],
            [$trav([2, 2, 3]), $trav([2, 2, 3]), $trav([1, 2, 3])],
            [$trav([1, 2, 3]), $trav([1, 2, 3]), $trav([2, 3, 4])],
            [$trav([1, 2, 3]), $trav([2, 1, 3]), $trav([2, 3, 4])],
            [$trav([1, 2, 3]), $trav([2, 3, 4]), $trav([3, 4, 5])],
            [$trav([1.1, '2', [3], null]), $trav([1.1, '2', [3], null]), $trav([1.1, '2', [], null])],
            [$trav([1.1, '2', [3], null]), $trav([1.1, '2', [], null]), $trav([1.1, '2', [], null])],
            [$trav([1.1, '2', [3], null]), $trav([1.1, '2', [3], null]), $trav([1.1, '1', [3], null])],
            [$trav([1.0, '2', [3], null]), $trav([1.1, '2', [3], null]), $trav([1.1, '2', [3], null])],
            [$trav([1.0, '2', [3], null]), $trav([1.1, '2.0', [3], null]), $trav([1.1, '1', [], null])],
            [$trav([1.0, '2', [3], null]), $trav([1.1, '2.0', [3], null]), $trav([1.1, '1', [], false])],
            [$trav([0, 1, 1]), $trav([true, 1.0, 1]), $trav([true, 1.0, true])],
            [$trav([1, 0, 0]), $trav([false, null, 0]), $trav([0.0, 0, false])],
        ];
    }
}
