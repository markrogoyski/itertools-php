<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamPowersetTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test Stream::powerset example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 2];

        // When
        $result = Stream::of($data)
            ->powerset()
            ->toArray();

        // Then
        $this->assertSame(
            [
                [],
                [1],
                [2],
                [1, 2],
            ],
            $result
        );
    }

    /**
     * @test         Stream::powerset (array)
     * @dataProvider dataProviderForPowerset
     * @param        array<mixed>        $data
     * @param        array<array<mixed>> $expected
     */
    public function testPowersetArray(array $data, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->powerset()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::powerset (Generator)
     * @dataProvider dataProviderForPowerset
     * @param        array<mixed>        $data
     * @param        array<array<mixed>> $expected
     */
    public function testPowersetGenerator(array $data, array $expected): void
    {
        // Given
        $gen = GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($gen)
            ->powerset()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::powerset (Iterator)
     * @dataProvider dataProviderForPowerset
     * @param        array<mixed>        $data
     * @param        array<array<mixed>> $expected
     */
    public function testPowersetIterator(array $data, array $expected): void
    {
        // Given
        $iter = new ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iter)
            ->powerset()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::powerset (IteratorAggregate)
     * @dataProvider dataProviderForPowerset
     * @param        array<mixed>        $data
     * @param        array<array<mixed>> $expected
     */
    public function testPowersetIteratorAggregate(array $data, array $expected): void
    {
        // Given
        $agg = new IteratorAggregateFixture($data);

        // When
        $result = Stream::of($agg)
            ->powerset()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForPowerset(): array
    {
        return [
            [
                [],
                [[]],
            ],
            [
                [1],
                [[], [1]],
            ],
            [
                [1, 2],
                [[], [1], [2], [1, 2]],
            ],
            [
                [1, 2, 3],
                [
                    [],
                    [1],
                    [2],
                    [3],
                    [1, 2],
                    [1, 3],
                    [2, 3],
                    [1, 2, 3],
                ],
            ],
            [
                [1, 1],
                [
                    [],
                    [1],
                    [1],
                    [1, 1],
                ],
            ],
        ];
    }

    /**
     * @test         Stream::powerset of empty iterable yields one empty subset
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterable(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->powerset()
            ->toArray();

        // Then
        $this->assertSame([[]], $result);
    }

    /**
     * @test Stream::powerset chains with other stream operations
     */
    public function testChainableWithOtherOperations(): void
    {
        // Given
        $source = [1, 2, 3, 4];

        // When
        $result = Stream::of($source)
            ->filter(fn (int $n): bool => $n % 2 === 0)
            ->powerset()
            ->toArray();

        // Then
        $this->assertSame(
            [
                [],
                [2],
                [4],
                [2, 4],
            ],
            $result
        );
    }
}
