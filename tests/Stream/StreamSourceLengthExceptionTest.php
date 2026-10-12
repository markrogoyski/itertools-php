<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Multi;
use IterTools\Stream;
use IterTools\Tests\Fixture\GeneratorFixture;

class StreamSourceLengthExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test Stream::sameWith propagates a \LengthException thrown by a source
     */
    public function testSameWithPropagatesSourceLengthException(): void
    {
        // Given
        $source = Multi::zipEqual([1, 2], [1]);

        // Then
        $this->expectException(\LengthException::class);

        // When
        Stream::of($source)->sameWith([[1, 1]]);
    }

    /**
     * @test Stream::arePermutationsWith propagates a \LengthException thrown by a source
     */
    public function testArePermutationsWithPropagatesSourceLengthException(): void
    {
        // Given
        $source = Multi::zipEqual([1, 2], [1]);

        // Then
        $this->expectException(\LengthException::class);

        // When
        Stream::of($source)->arePermutationsWith([[1, 1]]);
    }

    /**
     * @test Stream::arePermutationsCoerciveWith propagates a \LengthException thrown by a source
     */
    public function testArePermutationsCoerciveWithPropagatesSourceLengthException(): void
    {
        // Given
        $source = Multi::zipEqual([1, 2], [1]);

        // Then
        $this->expectException(\LengthException::class);

        // When
        Stream::of($source)->arePermutationsCoerciveWith([[1, 1]]);
    }

    /**
     * @test Stream::sameWith, arePermutationsWith and arePermutationsCoerciveWith are false for different lengths
     */
    public function testDifferentLengthsAreFalse(): void
    {
        // When
        $same = Stream::of([1, 2])->sameWith(GeneratorFixture::getGenerator([1]));
        $permutations = Stream::of([1, 2])->arePermutationsWith(GeneratorFixture::getGenerator([1]));
        $permutationsCoercive = Stream::of([1, 2])->arePermutationsCoerciveWith(GeneratorFixture::getGenerator([1]));

        // Then
        $this->assertFalse($same);
        $this->assertFalse($permutations);
        $this->assertFalse($permutationsCoercive);
    }
}
