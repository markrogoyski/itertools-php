<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\GeneratorFixture;

class StreamPrependAppendTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test prepend, append happy path
     */
    public function testPrependAndAppend(): void
    {
        // When
        $result = Stream::of([2, 3])->prepend(1)->append(4, 5)->toArray();

        // Then
        $this->assertSame([1, 2, 3, 4, 5], $result);
    }

    /**
     * @test prepend with no values is unchanged
     */
    public function testPrependEmpty(): void
    {
        // When
        $result = Stream::of([1, 2])->prepend()->toArray();

        // Then
        $this->assertSame([1, 2], $result);
    }

    /**
     * @test append with no values is unchanged
     */
    public function testAppendEmpty(): void
    {
        // When
        $result = Stream::of([1, 2])->append()->toArray();

        // Then
        $this->assertSame([1, 2], $result);
    }

    /**
     * @test prepend multiple values keeps order
     */
    public function testPrependMultiple(): void
    {
        // When
        $result = Stream::of([3, 4])->prepend(1, 2)->toArray();

        // Then
        $this->assertSame([1, 2, 3, 4], $result);
    }

    /**
     * @test prepend on empty stream
     */
    public function testPrependOnEmpty(): void
    {
        // When
        $result = Stream::of([])->prepend(1, 2)->toArray();

        // Then
        $this->assertSame([1, 2], $result);
    }

    /**
     * @test append on empty stream
     */
    public function testAppendOnEmpty(): void
    {
        // When
        $result = Stream::of([])->append(1, 2)->toArray();

        // Then
        $this->assertSame([1, 2], $result);
    }

    /**
     * @test prepend / append work with Generator sources
     */
    public function testWithGenerator(): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator([2, 3]);

        // When
        $result = Stream::of($iterable)->prepend(1)->append(4)->toArray();

        // Then
        $this->assertSame([1, 2, 3, 4], $result);
    }

    /**
     * @test prepend / append support null and other variadic types
     */
    public function testNullValues(): void
    {
        // When
        $result = Stream::of([1])->prepend(null)->append(null)->toArray();

        // Then
        $this->assertSame([null, 1, null], $result);
    }
}
