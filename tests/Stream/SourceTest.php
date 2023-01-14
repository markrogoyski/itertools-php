<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture;

class SourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test count of empty stream
     */
    public function testStreamOfEmptyCount(): void
    {
        // Given
        $stream = Stream::ofEmpty();

        // When
        $count = $stream->toCount();

        // Then
        $this->assertEquals(0, $count);
    }

    /**
     * @test empty stream to array
     */
    public function testStreamOfEmptyToArray(): void
    {
        // Given
        $stream = Stream::ofEmpty();

        // When
        $array = $stream->toArray();

        // Then
        $this->assertEmpty($array);
    }

    /**
     * @test stream of data count
     * @dataProvider dataProviderForSourceCounts
     */
    public function testStreamOfCount(iterable $iterable, int $expectedCount): void
    {
        // Given
        $stream = Stream::of($iterable);

        // When
        $count = $stream->toCount();

        // Then
        $this->assertEquals($expectedCount, $count);
    }

    public function dataProviderForSourceCounts(): array
    {
        return [
            [
                [],
                0,
            ],
            [
                Fixture\GeneratorFixture::getGenerator([]),
                0,
            ],
            [
                new Fixture\ArrayIteratorFixture([]),
                0,
            ],
            [
                new Fixture\IteratorAggregateFixture([]),
                0,
            ],
            [
                [5],
                1,
            ],
            [
                Fixture\GeneratorFixture::getGenerator([5]),
                1,
            ],
            [
                new Fixture\ArrayIteratorFixture([5]),
                1,
            ],
            [
                new Fixture\IteratorAggregateFixture([5]),
                1,
            ],
            [
                [1, 2, 3],
                3,
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                3,
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                3,
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                3,
            ],
        ];
    }

    /**
     * @test stream of data array
     * @dataProvider dataProviderForSourceArray
     */
    public function testStreamOfArray(iterable $iterable, array $expected): void
    {
        // Given
        $stream = Stream::of($iterable);

        // When
        $array = $stream->toArray();

        // Then
        $this->assertEquals($expected, $array);
    }

    public function dataProviderForSourceArray(): array
    {
        return [
            [
                [],
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([]),
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([]),
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([]),
                [],
            ],
            [
                [5],
                [5]
            ],
            [
                Fixture\GeneratorFixture::getGenerator([5]),
                [5]
            ],
            [
                new Fixture\ArrayIteratorFixture([5]),
                [5]
            ],
            [
                new Fixture\IteratorAggregateFixture([5]),
                [5]
            ],
            [
                [1, 2, 3],
                [1, 2, 3],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                [1, 2, 3],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                [1, 2, 3],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                [1, 2, 3],
            ],
        ];
    }
}