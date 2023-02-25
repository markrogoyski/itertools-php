<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToRandomValueTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        mixed $expected
     */
    public function testArray(array $data, array $expected): void
    {
        // When
        $result = Reduce::toRandomValue($data);

        // Then
        $this->assertContains($result, $expected);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [1],
                [1],
            ],
            [
                [1, 2, 3],
                [1, 2, 3],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                [1, 2, 3],
            ],
            [
                ['1' => 'a', '2' => 'b', '3' => 'c'],
                ['a', 'b', 'c'],
            ],
            [
                [1, 2.2, '3', true, false, null, [1, 2, 3]],
                [1, 2.2, '3', true, false, null, [1, 2, 3]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        mixed $expected
     */
    public function testGenerators(\Generator $data, array $expected): void
    {
        // When
        $result = Reduce::toRandomValue($data);

        // Then
        $this->assertContains($result, $expected);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = fn ($data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [
                $gen([1]),
                [1],
            ],
            [
                $gen([1, 2, 3]),
                [1, 2, 3],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3]),
                [1, 2, 3],
            ],
            [
                $gen(['1' => 'a', '2' => 'b', '3' => 'c']),
                ['a', 'b', 'c'],
            ],
            [
                $gen([1, 2.2, '3', true, false, null, [1, 2, 3]]),
                [1, 2.2, '3', true, false, null, [1, 2, 3]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param        \Iterator $data
     * @param        mixed $expected
     */
    public function testIterators(\Iterator $data, array $expected): void
    {
        // When
        $result = Reduce::toRandomValue($data);

        // Then
        $this->assertContains($result, $expected);
    }

    public function dataProviderForIterators(): array
    {
        $iter = fn ($data) => new \ArrayIterator($data);

        return [
            [
                $iter([1]),
                [1],
            ],
            [
                $iter([1, 2, 3]),
                [1, 2, 3],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3]),
                [1, 2, 3],
            ],
            [
                $iter(['1' => 'a', '2' => 'b', '3' => 'c']),
                ['a', 'b', 'c'],
            ],
            [
                $iter([1, 2.2, '3', true, false, null, [1, 2, 3]]),
                [1, 2.2, '3', true, false, null, [1, 2, 3]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        mixed $expected
     */
    public function testTraversables(\Traversable $data, array $expected): void
    {
        // When
        $result = Reduce::toRandomValue($data);

        // Then
        $this->assertContains($result, $expected);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([1]),
                [1],
            ],
            [
                $trav([1, 2, 3]),
                [1, 2, 3],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3]),
                [1, 2, 3],
            ],
            [
                $trav(['1' => 'a', '2' => 'b', '3' => 'c']),
                ['a', 'b', 'c'],
            ],
            [
                $trav([1, 2.2, '3', true, false, null, [1, 2, 3]]),
                [1, 2.2, '3', true, false, null, [1, 2, 3]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForEmptyIterable
     * @param iterable $data
     * @return void
     */
    public function testErrorOnEmpty(iterable $data): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Given iterable must be non-empty');

        // When
        Reduce::toRandomValue($data);
    }
}
