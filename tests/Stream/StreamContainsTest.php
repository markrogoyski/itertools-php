<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamContainsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         Stream::contains - true
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testTrueArray(array $data, mixed $needle): void
    {
        // When
        $result = Stream::of($data)->contains($needle);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         Stream::contains - true
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testTrueGenerator(array $data, mixed $needle): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($iterable)->contains($needle);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         Stream::contains - true
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testTrueIterator(array $data, mixed $needle): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iterable)->contains($needle);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         Stream::contains - true
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testTrueIteratorAggregate(array $data, mixed $needle): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Stream::of($iterable)->contains($needle);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForTrue(): array
    {
        return [
            [[1], 1],
            [[1, 2, 3], 2],
            [['a', 'b', 'c'], 'c'],
            [[null, 1, 2], null],
            [[1.1, 2.2, 3.3], 2.2],
        ];
    }

    /**
     * @test         Stream::contains - false
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testFalseArray(array $data, mixed $needle): void
    {
        // When
        $result = Stream::of($data)->contains($needle);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         Stream::contains - false
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testFalseGenerator(array $data, mixed $needle): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($iterable)->contains($needle);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         Stream::contains - false
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testFalseIterator(array $data, mixed $needle): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iterable)->contains($needle);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         Stream::contains - false
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testFalseIteratorAggregate(array $data, mixed $needle): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Stream::of($iterable)->contains($needle);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForFalse(): array
    {
        return [
            [[], 1],
            [[1, 2, 3], 4],
            [[1, 2, 3], '1'],
            [[0, 1, 2], false],
            [[0, 1, 2], '0'],
            [[null], 0],
        ];
    }

    /**
     * @test Stream::contains composes with upstream operations
     */
    public function testComposesWithUpstreamOperations(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5];

        // When
        $found = Stream::of($data)
            ->map(fn ($x) => $x * 2)
            ->contains(6);

        $notFound = Stream::of($data)
            ->map(fn ($x) => $x * 2)
            ->contains(5);

        // Then
        $this->assertTrue($found);
        $this->assertFalse($notFound);
    }
}
