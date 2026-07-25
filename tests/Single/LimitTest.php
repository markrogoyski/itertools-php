<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture;

class LimitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         limit array
     * @dataProvider dataProviderForArray
     * @param        array    $iterable
     * @param        int      $limit
     * @param        array    $expected
     */
    public function testArray(array $iterable, int $limit, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::limit($iterable, $limit) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [
                [0, 1, 2, 3, 4, 5],
                0,
                [],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                1,
                [0],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                2,
                [0, 1],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                3,
                [0, 1, 2],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                4,
                [0, 1, 2, 3],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                5,
                [0, 1, 2, 3, 4],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                6,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                7,
                [0, 1, 2, 3, 4, 5],
            ],
        ];
    }


    /**
     * @test         limit generator
     * @dataProvider dataProviderForGenerator
     * @param        \Generator $iterable
     * @param        int        $limit
     * @param        array      $expected
     */
    public function testGenerator(\Generator $iterable, int $limit, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::limit($iterable, $limit) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForGenerator(): array
    {
        return [
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                0,
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                1,
                [0],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                2,
                [0, 1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                3,
                [0, 1, 2],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                4,
                [0, 1, 2, 3],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                5,
                [0, 1, 2, 3, 4],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                6,
                [0, 1, 2, 3, 4, 5],
            ],
        ];
    }

    /**
     * @test         limit iterator
     * @dataProvider dataProviderForIterator
     * @param        \Iterator $iterable
     * @param        int       $limit
     * @param        array     $expected
     */
    public function testIterator(\Iterator $iterable, int $limit, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::limit($iterable, $limit) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForIterator(): array
    {
        return [
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                0,
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                1,
                [0],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                2,
                [0, 1],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                3,
                [0, 1, 2],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                4,
                [0, 1, 2, 3],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                5,
                [0, 1, 2, 3, 4],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                6,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                7,
                [0, 1, 2, 3, 4, 5],
            ],
        ];
    }

    /**
     * @test         limit traversable
     * @dataProvider dataProviderForTraversable
     * @param        \Traversable $iterable
     * @param        int          $limit
     * @param        array        $expected
     */
    public function testTraversable(\Traversable $iterable, int $limit, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::limit($iterable, $limit) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForTraversable(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                0,
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                1,
                [0],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                2,
                [0, 1],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                3,
                [0, 1, 2],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                4,
                [0, 1, 2, 3],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                5,
                [0, 1, 2, 3, 4],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                6,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                7,
                [0, 1, 2, 3, 4, 5],
            ],
        ];
    }

    /**
     * @test invalid limit
     */
    public function testInvalidLimit(): void
    {
        // Given
        $data         = [1, 2, 3];
        $invalidLimit = -1;

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach (Single::limit($data, $invalidLimit) as $_) {
            break;
        }
    }

    /**
     * @test limit with associative array
     */
    public function testAssociativeArray(): void
    {
        // Given
        $iterable = ['a' => 50, 'b' => 60, 'c' => 70, 'd' => 85, 'e' => 65, 'f' => 90];
        $limit    = 2;

        // And
        $expected = ['a' => 50, 'b' => 60];

        // When
        $result = [];
        foreach (Single::limit($iterable, $limit) as $key => $item) {
            $result[$key] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         limit iterator_to_array
     * @dataProvider dataProviderForArray
     * @param        array    $iterable
     * @param        int      $limit
     * @param        array    $expected
     */
    public function testIteratorToArray(array $iterable, int $limit, array $expected): void
    {
        // Given
        $iterator = Single::limit($iterable, $limit);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         limit does not consume the source past the limit
     * @dataProvider dataProviderForLaziness
     * @param        int   $limit
     * @param        array $expected
     * @param        array $expectedConsumed
     */
    public function testDoesNotConsumeSourcePastLimit(int $limit, array $expected, array $expectedConsumed): void
    {
        // Given a source that records every element pulled from it
        $consumed = [];
        $iterable = (static function () use (&$consumed): \Generator {
            foreach ([1, 2, 3, 4, 5] as $datum) {
                $consumed[] = $datum;
                yield $datum;
            }
        })();

        // When
        $result = [];
        foreach (Single::limit($iterable, $limit) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertSame($expected, $result);

        // And the source was never advanced beyond the elements that were yielded
        $this->assertSame($expectedConsumed, $consumed);
    }

    public static function dataProviderForLaziness(): array
    {
        return [
            [0, [], []],
            [1, [1], [1]],
            [2, [1, 2], [1, 2]],
            [3, [1, 2, 3], [1, 2, 3]],
            [4, [1, 2, 3, 4], [1, 2, 3, 4]],
            [5, [1, 2, 3, 4, 5], [1, 2, 3, 4, 5]],
            [6, [1, 2, 3, 4, 5], [1, 2, 3, 4, 5]],
        ];
    }

    /**
     * @test limit does not pull from a source that errors past the limit
     */
    public function testDoesNotPullFromSourceThatErrorsPastLimit(): void
    {
        // Given a source that throws on the pull following the third element
        $iterable = (static function (): \Generator {
            yield 1;
            yield 2;
            yield 3;
            throw new \RuntimeException('Source iterated past the limit');
        })();
        $limit = 3;

        // When
        $result = [];
        foreach (Single::limit($iterable, $limit) as $item) {
            $result[] = $item;
        }

        // Then no exception was thrown
        $this->assertSame([1, 2, 3], $result);
    }

    /**
     * @test limit of zero does not pull from the source at all
     */
    public function testZeroLimitDoesNotPullFromSource(): void
    {
        // Given a source that throws as soon as it is iterated
        $iterable = (static function (): \Generator {
            yield from [];
            throw new \RuntimeException('Source iterated past the limit');
        })();
        $limit = 0;

        // When
        $result = [];
        foreach (Single::limit($iterable, $limit) as $item) {
            $result[] = $item;
        }

        // Then no exception was thrown
        $this->assertSame([], $result);
    }
}
