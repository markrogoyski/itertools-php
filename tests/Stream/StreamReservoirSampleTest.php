<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture;

class StreamReservoirSampleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test Stream::reservoirSample example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        // When
        $result = Stream::of($data)
            ->reservoirSample(3)
            ->toArray();

        // Then
        $this->assertCount(3, $result);
        foreach ($result as $item) {
            $this->assertContains($item, $data);
        }
    }

    /**
     * @test         Stream::reservoirSample array
     * @dataProvider dataProviderForReservoirSample
     * @param        array $data
     * @param        int   $size
     */
    public function testArray(array $data, int $size): void
    {
        // When
        $result = Stream::of($data)
            ->reservoirSample($size)
            ->toArray();

        // Then
        $this->assertCount(\min($size, \count($data)), $result);
        foreach ($result as $item) {
            $this->assertContains($item, $data);
        }
    }

    /**
     * @test         Stream::reservoirSample generator
     * @dataProvider dataProviderForReservoirSample
     * @param        array $data
     * @param        int   $size
     */
    public function testGenerator(array $data, int $size): void
    {
        // Given
        $iterable = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($iterable)
            ->reservoirSample($size)
            ->toArray();

        // Then
        $this->assertCount(\min($size, \count($data)), $result);
        foreach ($result as $item) {
            $this->assertContains($item, $data);
        }
    }

    /**
     * @test         Stream::reservoirSample iterator
     * @dataProvider dataProviderForReservoirSample
     * @param        array $data
     * @param        int   $size
     */
    public function testIterator(array $data, int $size): void
    {
        // Given
        $iterable = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iterable)
            ->reservoirSample($size)
            ->toArray();

        // Then
        $this->assertCount(\min($size, \count($data)), $result);
        foreach ($result as $item) {
            $this->assertContains($item, $data);
        }
    }

    /**
     * @test         Stream::reservoirSample traversable
     * @dataProvider dataProviderForReservoirSample
     * @param        array $data
     * @param        int   $size
     */
    public function testTraversable(array $data, int $size): void
    {
        // Given
        $iterable = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Stream::of($iterable)
            ->reservoirSample($size)
            ->toArray();

        // Then
        $this->assertCount(\min($size, \count($data)), $result);
        foreach ($result as $item) {
            $this->assertContains($item, $data);
        }
    }

    public static function dataProviderForReservoirSample(): array
    {
        return [
            [[1, 2, 3, 4, 5], 0],
            [[1, 2, 3, 4, 5], 1],
            [[1, 2, 3, 4, 5], 3],
            [[1, 2, 3, 4, 5], 5],
            [[1, 2, 3, 4, 5], 10],
            [['a', 'b', 'c', 'd', 'e', 'f'], 4],
        ];
    }

    /**
     * @test Stream::reservoirSample chains with downstream operations
     */
    public function testChaining(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        // When
        $result = Stream::of($data)
            ->reservoirSample(3)
            ->map(fn (int $value) => $value * 10)
            ->toArray();

        // Then
        $this->assertCount(3, $result);
        foreach ($result as $item) {
            $this->assertContains($item, [10, 20, 30, 40, 50, 60, 70, 80, 90, 100]);
        }
    }

    /**
     * @test Stream::reservoirSample on empty input chains to an empty downstream
     */
    public function testEmptyInput(): void
    {
        // When
        $result = Stream::of([])
            ->reservoirSample(3)
            ->map(fn ($value) => $value)
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test Stream::reservoirSample drains the upstream immediately (eager consumption)
     */
    public function testEagerConsumption(): void
    {
        // Given a generator that counts how many elements have been pulled
        $consumedCount = 0;
        $gen = (function () use (&$consumedCount) {
            foreach ([1, 2, 3, 4, 5] as $value) {
                $consumedCount++;
                yield $value;
            }
        })();
        $stream = Stream::of($gen);

        // Precondition: nothing consumed yet
        $this->assertSame(0, $consumedCount);

        // When the reservoirSample operation is applied (no terminal operation follows)
        $stream->reservoirSample(3);

        // Then the upstream was drained immediately at call time
        $this->assertSame(5, $consumedCount);
    }
}
