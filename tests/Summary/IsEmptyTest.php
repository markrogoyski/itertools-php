<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class IsEmptyTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $iterable
     */
    public function testTrue(iterable $iterable)
    {
        // When
        $result = Summary::isEmpty($iterable);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @dataProvider dataProviderForArrayFalse
     * @param        array $iterable
     */
    public function testArrayFalse(array $iterable)
    {
        // When
        $result = Summary::isEmpty($iterable);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForArrayFalse(): array
    {
        return [
            [[1]],
            [[1, 2]],
            [[1, 2, 3]],
            [[1 => '1']],
            [['a' => 1]],
            [['a' => 1, 'b' => 2, 'c' => 3]],
        ];
    }

    /**
     * @dataProvider dataProviderForGeneratorsFalse
     * @param        \Generator $iterable
     */
    public function testGeneratorsFalse(\Generator $iterable)
    {
        // When
        $result = Summary::isEmpty($iterable);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForGeneratorsFalse(): array
    {
        $gen = fn ($data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [$gen([1])],
            [$gen([1, 2])],
            [$gen([1, 2, 3])],
            [$gen([1 => '1'])],
            [$gen(['a' => 1])],
            [$gen(['a' => 1, 'b' => 2, 'c' => 3])],
        ];
    }

    /**
     * @dataProvider dataProviderForIteratorsFalse
     * @param        \Iterator $iterable
     */
    public function testIteratorsFalse(\Iterator $iterable)
    {
        // When
        $result = Summary::isEmpty($iterable);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForIteratorsFalse(): array
    {
        $iter = fn ($data) => new \ArrayIterator($data);

        return [
            [$iter([1])],
            [$iter([1, 2])],
            [$iter([1, 2, 3])],
            [$iter([1 => '1'])],
            [$iter(['a' => 1])],
            [$iter(['a' => 1, 'b' => 2, 'c' => 3])],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversablesFalse
     * @param        \Traversable $iterable
     */
    public function testTraversablesFalse(\Traversable $iterable)
    {
        // When
        $result = Summary::isEmpty($iterable);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForTraversablesFalse(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [$trav([1])],
            [$trav([1, 2])],
            [$trav([1, 2, 3])],
            [$trav([1 => '1'])],
            [$trav(['a' => 1])],
            [$trav(['a' => 1, 'b' => 2, 'c' => 3])],
        ];
    }
}
