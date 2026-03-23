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

    public static function dataProviderForArrayFalse(): array
    {
        return [
            [[0]],
            [[[]]],
            [[null]],
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

    public static function dataProviderForGeneratorsFalse(): array
    {
        $gen = fn ($data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [$gen([0])],
            [$gen([[]])],
            [$gen([null])],
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

    public static function dataProviderForIteratorsFalse(): array
    {
        $iter = fn ($data) => new \ArrayIterator($data);

        return [
            [$iter([0])],
            [$iter([[]])],
            [$iter([null])],
            [$iter([1])],
            [$iter([1, 2])],
            [$iter([1, 2, 3])],
            [$iter([1 => '1'])],
            [$iter(['a' => 1])],
            [$iter(['a' => 1, 'b' => 2, 'c' => 3])],
        ];
    }

    /**
     * @test Reports non-empty for an ArrayIterator that has been advanced past all elements.
     */
    public function testAdvancedIteratorFalse(): void
    {
        // Given: a non-empty iterator advanced past its only element
        $iterator = new \ArrayIterator([1]);
        $iterator->next(); // now past end, valid() === false

        // When
        $result = Summary::isEmpty($iterator);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test Reports non-empty for a partially consumed multi-element ArrayIterator.
     */
    public function testPartiallyConsumedIteratorFalse(): void
    {
        // Given: a 3-element iterator advanced by one position
        $iterator = new \ArrayIterator([1, 2, 3]);
        $iterator->next();

        // When
        $result = Summary::isEmpty($iterator);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test A consumed generator reports true (empty) since generators cannot be rewound.
     */
    public function testConsumedGeneratorTrue(): void
    {
        // Given: a non-empty generator that has been fully consumed
        $generator = GeneratorFixture::getGenerator([1]);
        $generator->next(); // advance past the only element

        // When
        $result = Summary::isEmpty($generator);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test A partially consumed generator reports false (not empty) since elements remain.
     */
    public function testPartiallyConsumedGeneratorFalse(): void
    {
        // Given: a 3-element generator advanced by one position
        $generator = GeneratorFixture::getGenerator([1, 2, 3]);
        $generator->next();

        // When
        $result = Summary::isEmpty($generator);

        // Then
        $this->assertFalse($result);
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

    public static function dataProviderForTraversablesFalse(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [$trav([0])],
            [$trav([[]])],
            [$trav([null])],
            [$trav([1])],
            [$trav([1, 2])],
            [$trav([1, 2, 3])],
            [$trav([1 => '1'])],
            [$trav(['a' => 1])],
            [$trav(['a' => 1, 'b' => 2, 'c' => 3])],
        ];
    }
}
