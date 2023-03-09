<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class AllUniqueTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArrayStrictTrue
     * @dataProvider dataProviderForGeneratorsStrictTrue
     * @dataProvider dataProviderForIteratorsStrictTrue
     * @dataProvider dataProviderForTraversablesStrictTrue
     * @param        iterable $data
     */
    public function testStrictTrue(iterable $data): void
    {
        // When
        $result = Summary::allUnique($data);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @dataProvider dataProviderForArrayStrictFalse
     * @dataProvider dataProviderForGeneratorsStrictFalse
     * @dataProvider dataProviderForIteratorsStrictFalse
     * @dataProvider dataProviderForTraversablesStrictFalse
     * @param        iterable $data
     */
    public function testStrictFalse(iterable $data): void
    {
        // When
        $result = Summary::allUnique($data);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @dataProvider dataProviderForArrayCoerciveTrue
     * @dataProvider dataProviderForGeneratorsCoerciveTrue
     * @dataProvider dataProviderForIteratorsCoerciveTrue
     * @dataProvider dataProviderForTraversablesCoerciveTrue
     * @param        iterable $data
     */
    public function testCoerciveTrue(iterable $data): void
    {
        // When
        $result = Summary::allUnique($data, false);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @dataProvider dataProviderForArrayStrictFalse
     * @dataProvider dataProviderForArrayCoerciveFalse
     * @dataProvider dataProviderForGeneratorsStrictFalse
     * @dataProvider dataProviderForGeneratorsCoerciveFalse
     * @dataProvider dataProviderForIteratorsStrictFalse
     * @dataProvider dataProviderForIteratorsCoerciveFalse
     * @dataProvider dataProviderForTraversablesStrictFalse
     * @dataProvider dataProviderForTraversablesCoerciveFalse
     * @param        iterable $data
     */
    public function testCoerciveFalse(iterable $data): void
    {
        // When
        $result = Summary::allUnique($data, false);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForArrayStrictTrue(): array
    {
        return [
            [[]],
            [[1]],
            [[1, 2, 3]],
            [['1', '2', '3']],
            [[1, 1.0, '1.0', '1', true]],
            [[0, 0.0, '0.0', '0', false]],
            [[(object)[], 2, 3, (object)[]]],
            [[(object)[1], 2, 3, (object)[1]]],
            [[1, '1', 2.2, '3', null, true, false, [], [1], (object)[], (object)[1]]],
            [['a' => 1, 'b' => '1', 'c' => 2]],
        ];
    }

    public function dataProviderForArrayStrictFalse(): array
    {
        return [
            [[1, 1]],
            [[1, 2, 1]],
            [['1', '1', '3']],
            [[1, '1', 1, true]],
            [[1.0, 2, 3, 1.0]],
            [[null, 2, 3, null]],
            [[true, 2, 3, true]],
            [[false, 2, 3, false]],
            [[[], 2, 3, []]],
            [[[1], 2, 3, [1]]],
            [[$o = (object)[], 2, 3, $o]],
            [[$o = (object)[1], 2, 3, $o]],
            [['a' => 1, 'b' => '1', 'c' => 1]],
        ];
    }

    public function dataProviderForArrayCoerciveTrue(): array
    {
        return [
            [[]],
            [[1]],
            [[1, 2, 3]],
            [['1', '2', '3']],
            [[1, '1.1', 1.2]],
            [[1, '1.1', 2.2, '3', false, [], [1]]],
            [[2, '1.1', 2.2, '3', true, [], [1]]],
            [[2, '1.1', 2.2, '3', true, (object)[], (object)[1]]],
            [['a' => 1, 'b' => 2, 'c' => 3]],
        ];
    }

    public function dataProviderForArrayCoerciveFalse(): array
    {
        return [
            [[1, 1.0, '1.0', '1', true]],
            [[0, 0.0, '0.0', '0', false]],
            [[(object)[], 2, 3, (object)[]]],
            [[(object)[1], 2, 3, (object)[1]]],
            [[1, '1', 2.2, '3', null, true, false, [], [1], (object)[], (object)[1]]],
            [['a' => 1, 'b' => '1', 'c' => 2]],
        ];
    }

    public function dataProviderForGeneratorsStrictTrue(): array
    {
        $gen = fn ($data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [$gen([])],
            [$gen([1])],
            [$gen([1, 2, 3])],
            [$gen(['1', '2', '3'])],
            [$gen([1, 1.0, '1.0', '1', true])],
            [$gen([0, 0.0, '0.0', '0', false])],
            [$gen([(object)[], 2, 3, (object)[]])],
            [$gen([(object)[1], 2, 3, (object)[1]])],
            [$gen([1, '1', 2.2, '3', null, true, false, [], [1], (object)[], (object)[1]])],
            [$gen(['a' => 1, 'b' => '1', 'c' => 2])],
        ];
    }

    public function dataProviderForGeneratorsStrictFalse(): array
    {
        $gen = fn ($data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [$gen([1, 1])],
            [$gen([1, 2, 1])],
            [$gen(['1', '1', '3'])],
            [$gen([1, '1', 1, true])],
            [$gen([1.0, 2, 3, 1.0])],
            [$gen([null, 2, 3, null])],
            [$gen([true, 2, 3, true])],
            [$gen([false, 2, 3, false])],
            [$gen([[], 2, 3, []])],
            [$gen([[1], 2, 3, [1]])],
            [$gen([$o = (object)[], 2, 3, $o])],
            [$gen([$o = (object)[1], 2, 3, $o])],
            [$gen(['a' => 1, 'b' => '1', 'c' => 1])],
        ];
    }

    public function dataProviderForGeneratorsCoerciveTrue(): array
    {
        $gen = fn ($data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [$gen([])],
            [$gen([1])],
            [$gen([1, 2, 3])],
            [$gen(['1', '2', '3'])],
            [$gen([1, '1.1', 1.2])],
            [$gen([1, '1.1', 2.2, '3', false, [], [1]])],
            [$gen([2, '1.1', 2.2, '3', true, [], [1]])],
            [$gen([2, '1.1', 2.2, '3', true, (object)[], (object)[1]])],
            [$gen(['a' => 1, 'b' => 2, 'c' => 3])],
        ];
    }

    public function dataProviderForGeneratorsCoerciveFalse(): array
    {
        $gen = fn ($data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [$gen([1, 1.0, '1.0', '1', true])],
            [$gen([0, 0.0, '0.0', '0', false])],
            [$gen([(object)[], 2, 3, (object)[]])],
            [$gen([(object)[1], 2, 3, (object)[1]])],
            [$gen([1, '1', 2.2, '3', null, true, false, [], [1], (object)[], (object)[1]])],
            [$gen(['a' => 1, 'b' => '1', 'c' => 2])],
        ];
    }

    public function dataProviderForIteratorsStrictTrue(): array
    {
        $iter = fn ($data) => new \ArrayIterator($data);

        return [
            [$iter([])],
            [$iter([1])],
            [$iter([1, 2, 3])],
            [$iter(['1', '2', '3'])],
            [$iter([1, 1.0, '1.0', '1', true])],
            [$iter([0, 0.0, '0.0', '0', false])],
            [$iter([(object)[], 2, 3, (object)[]])],
            [$iter([(object)[1], 2, 3, (object)[1]])],
            [$iter([1, '1', 2.2, '3', null, true, false, [], [1], (object)[], (object)[1]])],
            [$iter(['a' => 1, 'b' => '1', 'c' => 2])],
        ];
    }

    public function dataProviderForIteratorsStrictFalse(): array
    {
        $iter = fn ($data) => new \ArrayIterator($data);

        return [
            [$iter([1, 1])],
            [$iter([1, 2, 1])],
            [$iter(['1', '1', '3'])],
            [$iter([1, '1', 1, true])],
            [$iter([1.0, 2, 3, 1.0])],
            [$iter([null, 2, 3, null])],
            [$iter([true, 2, 3, true])],
            [$iter([false, 2, 3, false])],
            [$iter([[], 2, 3, []])],
            [$iter([[1], 2, 3, [1]])],
            [$iter([$o = (object)[], 2, 3, $o])],
            [$iter([$o = (object)[1], 2, 3, $o])],
            [$iter(['a' => 1, 'b' => '1', 'c' => 1])],
        ];
    }

    public function dataProviderForIteratorsCoerciveTrue(): array
    {
        $iter = fn ($data) => new \ArrayIterator($data);

        return [
            [$iter([])],
            [$iter([1])],
            [$iter([1, 2, 3])],
            [$iter(['1', '2', '3'])],
            [$iter([1, '1.1', 1.2])],
            [$iter([1, '1.1', 2.2, '3', false, [], [1]])],
            [$iter([2, '1.1', 2.2, '3', true, [], [1]])],
            [$iter([2, '1.1', 2.2, '3', true, (object)[], (object)[1]])],
            [$iter(['a' => 1, 'b' => 2, 'c' => 3])],
        ];
    }

    public function dataProviderForIteratorsCoerciveFalse(): array
    {
        $iter = fn ($data) => new \ArrayIterator($data);

        return [
            [$iter([1, 1.0, '1.0', '1', true])],
            [$iter([0, 0.0, '0.0', '0', false])],
            [$iter([(object)[], 2, 3, (object)[]])],
            [$iter([(object)[1], 2, 3, (object)[1]])],
            [$iter([1, '1', 2.2, '3', null, true, false, [], [1], (object)[], (object)[1]])],
            [$iter(['a' => 1, 'b' => '1', 'c' => 2])],
        ];
    }

    public function dataProviderForTraversablesStrictTrue(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [$trav([])],
            [$trav([1])],
            [$trav([1, 2, 3])],
            [$trav(['1', '2', '3'])],
            [$trav([1, 1.0, '1.0', '1', true])],
            [$trav([0, 0.0, '0.0', '0', false])],
            [$trav([(object)[], 2, 3, (object)[]])],
            [$trav([(object)[1], 2, 3, (object)[1]])],
            [$trav([1, '1', 2.2, '3', null, true, false, [], [1], (object)[], (object)[1]])],
            [$trav(['a' => 1, 'b' => '1', 'c' => 2])],
        ];
    }

    public function dataProviderForTraversablesStrictFalse(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [$trav([1, 1])],
            [$trav([1, 2, 1])],
            [$trav(['1', '1', '3'])],
            [$trav([1, '1', 1, true])],
            [$trav([1.0, 2, 3, 1.0])],
            [$trav([null, 2, 3, null])],
            [$trav([true, 2, 3, true])],
            [$trav([false, 2, 3, false])],
            [$trav([[], 2, 3, []])],
            [$trav([[1], 2, 3, [1]])],
            [$trav([$o = (object)[], 2, 3, $o])],
            [$trav([$o = (object)[1], 2, 3, $o])],
            [$trav(['a' => 1, 'b' => '1', 'c' => 1])],
        ];
    }

    public function dataProviderForTraversablesCoerciveTrue(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [$trav([])],
            [$trav([1])],
            [$trav([1, 2, 3])],
            [$trav(['1', '2', '3'])],
            [$trav([1, '1.1', 1.2])],
            [$trav([1, '1.1', 2.2, '3', false, [], [1]])],
            [$trav([2, '1.1', 2.2, '3', true, [], [1]])],
            [$trav([2, '1.1', 2.2, '3', true, (object)[], (object)[1]])],
            [$trav(['a' => 1, 'b' => 2, 'c' => 3])],
        ];
    }

    public function dataProviderForTraversablesCoerciveFalse(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [$trav([1, 1.0, '1.0', '1', true])],
            [$trav([0, 0.0, '0.0', '0', false])],
            [$trav([(object)[], 2, 3, (object)[]])],
            [$trav([(object)[1], 2, 3, (object)[1]])],
            [$trav([1, '1', 2.2, '3', null, true, false, [], [1], (object)[], (object)[1]])],
            [$trav(['a' => 1, 'b' => '1', 'c' => 2])],
        ];
    }
}
