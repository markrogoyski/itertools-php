<?php

declare(strict_types=1);

namespace IterTools\Tests\Transform;

use IterTools\Tests\Fixture;
use IterTools\Transform;

class ToArrayTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test toArray example usage
     */
    public function toArrayExampleUsage(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5];

        // And
        $iterator    = new \ArrayIterator($data);
        $traversable = new Fixture\IteratorAggregateFixture($data);
        $generator   = fn ($a) => yield from $a;

        // When
        $arrayFromIterator    = Transform::toArray($iterator);
        $arrayFromTraversable = Transform::toArray($traversable);
        $arrayFromGenerator   = Transform::toArray($generator($data));

        // Then
        $this->assertEquals($data, $arrayFromIterator);
        $this->assertEquals($data, $arrayFromTraversable);
        $this->assertEquals($data, $arrayFromGenerator);
    }

    /**
     * @dataProvider dataProviderForArray
     * @param array $input
     * @param array $expected
     * @return void
     */
    public function testArray(array $input, array $expected): void
    {
        // When
        $result = Transform::toArray($input);

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                [],
            ],
            [
                [1],
                [1],
            ],
            [
                [1, 1],
                [1, 1],
            ],
            [
                [1, 2],
                [1, 2],
            ],
            [
                [1, 1, 1],
                [1, 1, 1],
            ],
            [
                [1, 2, 3],
                [1, 2, 3],
            ],
            [
                [1 => 1, 2 => 2, 3 => 3],
                [1, 2, 3],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                [1, 2, 3],
            ],
            [
                [1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', true, false, null, [1, 2, 3], (object)[1, 2, 3], 'a' => 1, 'b' => 2, 'c' => 3],
                [1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', true, false, null, [1, 2, 3], (object)[1, 2, 3], 1, 2, 3],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator $input
     * @param array $expected
     * @return void
     */
    public function testGenerators(\Generator $input, array $expected): void
    {
        // When
        $result = Transform::toArray($input);

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = fn (array $input) => Fixture\GeneratorFixture::getKeyValueGenerator($input);

        return [
            [
                $gen([]),
                [],
            ],
            [
                $gen([1]),
                [1],
            ],
            [
                $gen([1, 1]),
                [1, 1],
            ],
            [
                $gen([1, 2]),
                [1, 2],
            ],
            [
                $gen([1, 1, 1]),
                [1, 1, 1],
            ],
            [
                $gen([1, 2, 3]),
                [1, 2, 3],
            ],
            [
                $gen([1 => 1, 2 => 2, 3 => 3]),
                [1, 2, 3],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3]),
                [1, 2, 3],
            ],
            [
                $gen([1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', true, false, null, [1, 2, 3], (object)[1, 2, 3], 'a' => 1, 'b' => 2, 'c' => 3]),
                [1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', true, false, null, [1, 2, 3], (object)[1, 2, 3], 1, 2, 3],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator $input
     * @param array $expected
     * @return void
     */
    public function testIterators(\Iterator $input, array $expected): void
    {
        // When
        $result = Transform::toArray($input);

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = fn (array $input) => new \ArrayIterator($input);

        return [
            [
                $iter([]),
                [],
            ],
            [
                $iter([1]),
                [1],
            ],
            [
                $iter([1, 1]),
                [1, 1],
            ],
            [
                $iter([1, 2]),
                [1, 2],
            ],
            [
                $iter([1, 1, 1]),
                [1, 1, 1],
            ],
            [
                $iter([1, 2, 3]),
                [1, 2, 3],
            ],
            [
                $iter([1 => 1, 2 => 2, 3 => 3]),
                [1, 2, 3],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3]),
                [1, 2, 3],
            ],
            [
                $iter([1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', true, false, null, [1, 2, 3], (object)[1, 2, 3], 'a' => 1, 'b' => 2, 'c' => 3]),
                [1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', true, false, null, [1, 2, 3], (object)[1, 2, 3], 1, 2, 3],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable $input
     * @param array $expected
     * @return void
     */
    public function testTraversables(\Traversable $input, array $expected): void
    {
        // When
        $result = Transform::toArray($input);

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = fn (array $input) => new Fixture\IteratorAggregateFixture($input);

        return [
            [
                $trav([]),
                [],
            ],
            [
                $trav([1]),
                [1],
            ],
            [
                $trav([1, 1]),
                [1, 1],
            ],
            [
                $trav([1, 2]),
                [1, 2],
            ],
            [
                $trav([1, 1, 1]),
                [1, 1, 1],
            ],
            [
                $trav([1, 2, 3]),
                [1, 2, 3],
            ],
            [
                $trav([1 => 1, 2 => 2, 3 => 3]),
                [1, 2, 3],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3]),
                [1, 2, 3],
            ],
            [
                $trav([1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', true, false, null, [1, 2, 3], (object)[1, 2, 3], 'a' => 1, 'b' => 2, 'c' => 3]),
                [1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', true, false, null, [1, 2, 3], (object)[1, 2, 3], 1, 2, 3],
            ],
        ];
    }
}
