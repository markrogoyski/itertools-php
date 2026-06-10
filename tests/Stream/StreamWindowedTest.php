<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture;

class StreamWindowedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test Stream::windowed example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5];

        // When
        $result = Stream::of($data)
            ->windowed(3)
            ->toArray();

        // Then
        $this->assertEquals([[1, 2, 3], [2, 3, 4], [3, 4, 5]], $result);
    }

    /**
     * @test         Stream::windowed array
     * @dataProvider dataProviderForWindowed
     * @param        array $data
     * @param        int   $size
     * @param        int   $step
     * @param        bool  $partial
     * @param        array $expected
     */
    public function testArray(array $data, int $size, int $step, bool $partial, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->windowed($size, $step, $partial)
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         Stream::windowed generator
     * @dataProvider dataProviderForWindowed
     * @param        array $data
     * @param        int   $size
     * @param        int   $step
     * @param        bool  $partial
     * @param        array $expected
     */
    public function testGenerator(array $data, int $size, int $step, bool $partial, array $expected): void
    {
        // Given
        $iterable = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($iterable)
            ->windowed($size, $step, $partial)
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         Stream::windowed iterator
     * @dataProvider dataProviderForWindowed
     * @param        array $data
     * @param        int   $size
     * @param        int   $step
     * @param        bool  $partial
     * @param        array $expected
     */
    public function testIterator(array $data, int $size, int $step, bool $partial, array $expected): void
    {
        // Given
        $iterable = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iterable)
            ->windowed($size, $step, $partial)
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         Stream::windowed traversable
     * @dataProvider dataProviderForWindowed
     * @param        array $data
     * @param        int   $size
     * @param        int   $step
     * @param        bool  $partial
     * @param        array $expected
     */
    public function testTraversable(array $data, int $size, int $step, bool $partial, array $expected): void
    {
        // Given
        $iterable = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Stream::of($iterable)
            ->windowed($size, $step, $partial)
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForWindowed(): array
    {
        return [
            [[], 2, 1, true, []],
            [[1, 2, 3, 4, 5], 3, 1, false, [[1, 2, 3], [2, 3, 4], [3, 4, 5]]],
            // step == size tiling, partial governs the tail
            [[1, 2, 3, 4, 5], 2, 2, true, [[1, 2], [3, 4], [5]]],
            [[1, 2, 3, 4, 5], 2, 2, false, [[1, 2], [3, 4]]],
            // step > size gapped windows
            [[1, 2, 3, 4, 5, 6], 2, 5, true, [[1, 2], [6]]],
            [[1, 2, 3, 4, 5, 6, 7, 8], 2, 5, true, [[1, 2], [6, 7]]],
            // size > input length
            [[1, 2, 3], 5, 1, false, []],
            [[1, 2, 3], 5, 1, true, [[1, 2, 3]]],
        ];
    }

    /**
     * @test Stream::windowed chains with other operations
     */
    public function testChaining(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5];

        // When
        $result = Stream::of($data)
            ->windowed(2)
            ->map(fn (array $window) => \array_sum($window))
            ->toArray();

        // Then
        $this->assertEquals([3, 5, 7, 9], $result);
    }

    /**
     * @test         Stream::windowed throws on invalid size
     * @dataProvider dataProviderForInvalidSize
     * @param        int $size
     */
    public function testInvalidSizeThrows(int $size): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Window size must be ≥ 1. Got {$size}");

        // When
        Stream::of([1, 2, 3])
            ->windowed($size, 1)
            ->toArray();
    }

    public static function dataProviderForInvalidSize(): array
    {
        return [
            [0],
            [-1],
        ];
    }

    /**
     * @test         Stream::windowed throws on invalid step
     * @dataProvider dataProviderForInvalidStep
     * @param        int $step
     */
    public function testInvalidStepThrows(int $step): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Step must be ≥ 1. Got {$step}");

        // When
        Stream::of([1, 2, 3])
            ->windowed(2, $step)
            ->toArray();
    }

    public static function dataProviderForInvalidStep(): array
    {
        return [
            [0],
            [-1],
        ];
    }
}
