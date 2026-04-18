<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;
use IterTools\Tests\Fixture\NonSerializableFixture;

class StreamContainsCoerciveTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         Stream::containsCoercive - true
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testTrueArray(array $data, mixed $needle): void
    {
        // When
        $result = Stream::of($data)->containsCoercive($needle);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         Stream::containsCoercive - true
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testTrueGenerator(array $data, mixed $needle): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($iterable)->containsCoercive($needle);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         Stream::containsCoercive - true
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testTrueIterator(array $data, mixed $needle): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iterable)->containsCoercive($needle);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         Stream::containsCoercive - true
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testTrueIteratorAggregate(array $data, mixed $needle): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Stream::of($iterable)->containsCoercive($needle);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForTrue(): array
    {
        return [
            [[1], 1],
            [[1, 2, 3], 2],
            [[1, 2, 3], '1'],
            [['1', '2', '3'], 1],
            [[0, 1, 2], false],
            [[0, 1, 2], '0'],
            [[100, 200, 300], '1e2'],
        ];
    }

    /**
     * @test         Stream::containsCoercive - false
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testFalseArray(array $data, mixed $needle): void
    {
        // When
        $result = Stream::of($data)->containsCoercive($needle);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         Stream::containsCoercive - false
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testFalseGenerator(array $data, mixed $needle): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($iterable)->containsCoercive($needle);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         Stream::containsCoercive - false
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testFalseIterator(array $data, mixed $needle): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iterable)->containsCoercive($needle);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         Stream::containsCoercive - false
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        mixed        $needle
     */
    public function testFalseIteratorAggregate(array $data, mixed $needle): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Stream::of($iterable)->containsCoercive($needle);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForFalse(): array
    {
        return [
            [[], 1],
            [[1, 2, 3], 4],
            [['a', 'b', 'c'], 'd'],
            [[1, 2, 3], 99],
        ];
    }

    /**
     * @test Stream::containsCoercive composes with upstream operations
     */
    public function testComposesWithUpstreamOperations(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5];

        // When
        $found = Stream::of($data)
            ->map(fn ($x) => $x * 2)
            ->containsCoercive('6');

        $notFound = Stream::of($data)
            ->map(fn ($x) => $x * 2)
            ->containsCoercive(5);

        // Then
        $this->assertTrue($found);
        $this->assertFalse($notFound);
    }

    /**
     * @test Stream::containsCoercive throws on non-serializable needle
     */
    public function testNonSerializableNeedleThrows(): void
    {
        // Given
        $needle = new NonSerializableFixture(1);

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::of([1, 2, 3])->containsCoercive($needle);
    }
}
