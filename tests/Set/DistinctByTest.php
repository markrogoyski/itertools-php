<?php

declare(strict_types=1);

namespace IterTools\Tests\Set;

use IterTools\Set;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class DistinctByTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test distinctBy example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $streetFighterConsoleReleases = [
            ['id' => '112233', 'name' => 'Street Fighter 3 3rd Strike', 'console' => 'Dreamcast'],
            ['id' => '223344', 'name' => 'Street Fighter 3 3rd Strike', 'console' => 'PS4'],
            ['id' => '334455', 'name' => 'Street Fighter 3 3rd Strike', 'console' => 'PS5'],
            ['id' => '445566', 'name' => 'Street Fighter VI', 'console' => 'PS4'],
            ['id' => '556677', 'name' => 'Street Fighter VI', 'console' => 'PS5'],
            ['id' => '667788', 'name' => 'Street Fighter VI', 'console' => 'PC'],
        ];

        // And
        $compareBy = fn ($sfTitle) => $sfTitle['name'];

        // When
        $uniqueTitles = [];
        foreach (Set::distinctBy($streetFighterConsoleReleases, $compareBy) as $sfTitle) {
            $uniqueTitles[] = $sfTitle;
        }

        // Then
        $this->assertCount(2, $uniqueTitles);
        $this->assertContains('Street Fighter 3 3rd Strike', [$uniqueTitles[0]['name'], $uniqueTitles[1]['name']]);
        $this->assertContains('Street Fighter VI', [$uniqueTitles[0]['name'], $uniqueTitles[1]['name']]);
    }

    /**
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        callable $compareBy
     * @param        array $expected
     */
    public function testArray(array $data, callable $compareBy, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::distinctBy($data, $compareBy) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                fn ($item) => $item,
                [],
            ],
            [
                [],
                fn ($item) => $item['name'],
                [],
            ],
            [
                [1],
                fn ($item) => $item,
                [1],
            ],
            [
                [1, 1],
                fn ($item) => $item,
                [1],
            ],
            [
                [1, '1'],
                fn ($item) => $item,
                [1, '1'],
            ],
            [
                ['1', 1],
                fn ($item) => $item,
                ['1', 1],
            ],
            [
                ['aa', 'bb', 'aa'],
                fn ($item) => $item,
                ['aa', 'bb'],
            ],
            [
                [1, 2, 1, 2, 3],
                fn ($item) => $item,
                [1, 2, 3],
            ],
            [
                ['1', 2, '1', '2', 3],
                fn ($item) => $item,
                ['1', 2, '2', 3],
            ],
            [
                ['1', 2, '1', '2', 3],
                fn ($item) => (int)$item,
                ['1', 2, '3'],
            ],
            [
                [[1], [1], [1, 2]],
                fn ($item) => $item,
                [[1], [1, 2]],
            ],
            [
                [[1], [1], [1, 2]],
                fn ($item) => $item[0],
                [[1]],
            ],
            [
                [[1], 'a' => [1]],
                fn ($item) => $item,
                [[1]],
            ],
            [
                [false, null, 0, 0.0, ''],
                fn ($item) => $item,
                [false, null, 0, 0.0, ''],
            ],
            [
                [true, 1, '1', 1.0, '1.0'],
                fn ($item) => $item,
                [true, 1, '1', 1.0, '1.0'],
            ],
            [
                [['a' => 1, 'b' => 2], ['a' => 1], ['a' => 1], 2, 2],
                fn ($item) => $item,
                [['a' => 1, 'b' => 2], ['a' => 1], 2],
            ],
            [
                [['a' => 1, 'b' => 2], ['a' => 1], ['a' => 1], ['a' => 2]],
                fn ($item) => $item['a'],
                [['a' => 1, 'b' => 2], ['a' => 2]],
            ],
            [
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ],
                fn ($item) => $item,
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ],
            ],
            [
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ],
                fn ($item) => $item['id'],
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ],
            ],
            [
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ],
                fn ($item) => $item['name'],
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Jane', 'id' => 5],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        callable $compareBy
     * @param        array $expected
     */
    public function testGenerators(\Generator $data, callable $compareBy, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::distinctBy($data, $compareBy) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = fn ($data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                fn ($item) => $item,
                [],
            ],
            [
                $gen([]),
                fn ($item) => $item['name'],
                [],
            ],
            [
                $gen([1]),
                fn ($item) => $item,
                [1],
            ],
            [
                $gen([1, 1]),
                fn ($item) => $item,
                [1],
            ],
            [
                $gen([1, '1']),
                fn ($item) => $item,
                [1, '1'],
            ],
            [
                $gen(['1', 1]),
                fn ($item) => $item,
                ['1', 1],
            ],
            [
                $gen(['aa', 'bb', 'aa']),
                fn ($item) => $item,
                ['aa', 'bb'],
            ],
            [
                $gen([1, 2, 1, 2, 3]),
                fn ($item) => $item,
                [1, 2, 3],
            ],
            [
                $gen(['1', 2, '1', '2', 3]),
                fn ($item) => $item,
                ['1', 2, '2', 3],
            ],
            [
                $gen(['1', 2, '1', '2', 3]),
                fn ($item) => (int)$item,
                ['1', 2, '3'],
            ],
            [
                $gen([[1], [1], [1, 2]]),
                fn ($item) => $item,
                [[1], [1, 2]],
            ],
            [
                $gen([[1], [1], [1, 2]]),
                fn ($item) => $item[0],
                [[1]],
            ],
            [
                $gen([[1], 'a' => [1]]),
                fn ($item) => $item,
                [[1]],
            ],
            [
                $gen([false, null, 0, 0.0, '']),
                fn ($item) => $item,
                [false, null, 0, 0.0, ''],
            ],
            [
                $gen([true, 1, '1', 1.0, '1.0']),
                fn ($item) => $item,
                [true, 1, '1', 1.0, '1.0'],
            ],
            [
                $gen([['a' => 1, 'b' => 2], ['a' => 1], ['a' => 1], 2, 2]),
                fn ($item) => $item,
                [['a' => 1, 'b' => 2], ['a' => 1], 2],
            ],
            [
                $gen([['a' => 1, 'b' => 2], ['a' => 1], ['a' => 1], ['a' => 2]]),
                fn ($item) => $item['a'],
                [['a' => 1, 'b' => 2], ['a' => 2]],
            ],
            [
                $gen([
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ]),
                fn ($item) => $item,
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ],
            ],
            [
                $gen([
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ]),
                fn ($item) => $item['id'],
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ],
            ],
            [
                $gen([
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ]),
                fn ($item) => $item['name'],
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Jane', 'id' => 5],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param        \Iterator $data
     * @param        callable $compareBy
     * @param        array $expected
     */
    public function testIterators(\Iterator $data, callable $compareBy, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::distinctBy($data, $compareBy) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = fn ($data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                fn ($item) => $item,
                [],
            ],
            [
                $iter([]),
                fn ($item) => $item['name'],
                [],
            ],
            [
                $iter([1]),
                fn ($item) => $item,
                [1],
            ],
            [
                $iter([1, 1]),
                fn ($item) => $item,
                [1],
            ],
            [
                $iter([1, '1']),
                fn ($item) => $item,
                [1, '1'],
            ],
            [
                $iter(['1', 1]),
                fn ($item) => $item,
                ['1', 1],
            ],
            [
                $iter(['aa', 'bb', 'aa']),
                fn ($item) => $item,
                ['aa', 'bb'],
            ],
            [
                $iter([1, 2, 1, 2, 3]),
                fn ($item) => $item,
                [1, 2, 3],
            ],
            [
                $iter(['1', 2, '1', '2', 3]),
                fn ($item) => $item,
                ['1', 2, '2', 3],
            ],
            [
                $iter(['1', 2, '1', '2', 3]),
                fn ($item) => (int)$item,
                ['1', 2, '3'],
            ],
            [
                $iter([[1], [1], [1, 2]]),
                fn ($item) => $item,
                [[1], [1, 2]],
            ],
            [
                $iter([[1], [1], [1, 2]]),
                fn ($item) => $item[0],
                [[1]],
            ],
            [
                $iter([[1], 'a' => [1]]),
                fn ($item) => $item,
                [[1]],
            ],
            [
                $iter([false, null, 0, 0.0, '']),
                fn ($item) => $item,
                [false, null, 0, 0.0, ''],
            ],
            [
                $iter([true, 1, '1', 1.0, '1.0']),
                fn ($item) => $item,
                [true, 1, '1', 1.0, '1.0'],
            ],
            [
                $iter([['a' => 1, 'b' => 2], ['a' => 1], ['a' => 1], 2, 2]),
                fn ($item) => $item,
                [['a' => 1, 'b' => 2], ['a' => 1], 2],
            ],
            [
                $iter([['a' => 1, 'b' => 2], ['a' => 1], ['a' => 1], ['a' => 2]]),
                fn ($item) => $item['a'],
                [['a' => 1, 'b' => 2], ['a' => 2]],
            ],
            [
                $iter([
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ]),
                fn ($item) => $item,
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ],
            ],
            [
                $iter([
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ]),
                fn ($item) => $item['id'],
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ],
            ],
            [
                $iter([
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ]),
                fn ($item) => $item['name'],
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Jane', 'id' => 5],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        callable $compareBy
     * @param        array $expected
     */
    public function testTraversables(\Traversable $data, callable $compareBy, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::distinctBy($data, $compareBy) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $iter = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                $iter([]),
                fn ($item) => $item,
                [],
            ],
            [
                $iter([]),
                fn ($item) => $item['name'],
                [],
            ],
            [
                $iter([1]),
                fn ($item) => $item,
                [1],
            ],
            [
                $iter([1, 1]),
                fn ($item) => $item,
                [1],
            ],
            [
                $iter([1, '1']),
                fn ($item) => $item,
                [1, '1'],
            ],
            [
                $iter(['1', 1]),
                fn ($item) => $item,
                ['1', 1],
            ],
            [
                $iter(['aa', 'bb', 'aa']),
                fn ($item) => $item,
                ['aa', 'bb'],
            ],
            [
                $iter([1, 2, 1, 2, 3]),
                fn ($item) => $item,
                [1, 2, 3],
            ],
            [
                $iter(['1', 2, '1', '2', 3]),
                fn ($item) => $item,
                ['1', 2, '2', 3],
            ],
            [
                $iter(['1', 2, '1', '2', 3]),
                fn ($item) => (int)$item,
                ['1', 2, '3'],
            ],
            [
                $iter([[1], [1], [1, 2]]),
                fn ($item) => $item,
                [[1], [1, 2]],
            ],
            [
                $iter([[1], [1], [1, 2]]),
                fn ($item) => $item[0],
                [[1]],
            ],
            [
                $iter([[1], 'a' => [1]]),
                fn ($item) => $item,
                [[1]],
            ],
            [
                $iter([false, null, 0, 0.0, '']),
                fn ($item) => $item,
                [false, null, 0, 0.0, ''],
            ],
            [
                $iter([true, 1, '1', 1.0, '1.0']),
                fn ($item) => $item,
                [true, 1, '1', 1.0, '1.0'],
            ],
            [
                $iter([['a' => 1, 'b' => 2], ['a' => 1], ['a' => 1], 2, 2]),
                fn ($item) => $item,
                [['a' => 1, 'b' => 2], ['a' => 1], 2],
            ],
            [
                $iter([['a' => 1, 'b' => 2], ['a' => 1], ['a' => 1], ['a' => 2]]),
                fn ($item) => $item['a'],
                [['a' => 1, 'b' => 2], ['a' => 2]],
            ],
            [
                $iter([
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ]),
                fn ($item) => $item,
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ],
            ],
            [
                $iter([
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ]),
                fn ($item) => $item['id'],
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ],
            ],
            [
                $iter([
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ]),
                fn ($item) => $item['name'],
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Jane', 'id' => 5],
                ],
            ],
        ];
    }
}
