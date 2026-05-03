<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\GeneratorFixture;

class StreamSingleTier3Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @test splitWhen delegates to Single::splitWhen
     */
    public function testSplitWhen(): void
    {
        // When
        $result = Stream::of([1, 2, 0, 3, 0, 4])
            ->splitWhen(fn (int $x): bool => $x === 0)
            ->toArray();

        // Then
        $this->assertSame([[1, 2], [0, 3], [0, 4]], $result);
    }

    /**
     * @test splitWhen composes with upstream operations
     */
    public function testSplitWhenAfterMap(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4, 5])
            ->map(fn (int $n): int => $n - 3)
            ->splitWhen(fn (int $x): bool => $x === 0)
            ->toArray();

        // Then
        $this->assertSame([[-2, -1], [0, 1, 2]], $result);
    }

    /**
     * @test groupAdjacentBy delegates to Single::groupAdjacentBy
     */
    public function testGroupAdjacentBy(): void
    {
        // When
        $result = Stream::of([1, 1, 2, 2, 1])
            ->groupAdjacentBy(fn (int $x): int => $x)
            ->toArray();

        // Then
        $this->assertSame([[1, [1, 1]], [2, [2, 2]], [1, [1]]], $result);
    }

    /**
     * @test padLeft delegates to Single::padLeft
     */
    public function testPadLeft(): void
    {
        // When
        $result = Stream::of([1, 2, 3])->padLeft(5, 0)->toArray();

        // Then
        $this->assertSame([0, 0, 1, 2, 3], $result);
    }

    /**
     * @test padRight delegates to Single::padRight
     */
    public function testPadRight(): void
    {
        // When
        $result = Stream::of([1, 2, 3])->padRight(5, 0)->toArray();

        // Then
        $this->assertSame([1, 2, 3, 0, 0], $result);
    }

    /**
     * @test padLeft with Generator source
     */
    public function testPadLeftGenerator(): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator([1, 2]);

        // When
        $result = Stream::of($iterable)->padLeft(4, 'x')->toArray();

        // Then
        $this->assertSame(['x', 'x', 1, 2], $result);
    }
}
