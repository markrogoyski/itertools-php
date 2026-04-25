<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamIntersperseTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test Stream::intersperse example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = Stream::of($data)
            ->intersperse(0)
            ->toArray();

        // Then
        $this->assertSame([1, 0, 2, 0, 3], $result);
    }

    /**
     * @test         Stream::intersperse (array)
     * @dataProvider dataProviderForIntersperse
     * @param        array<mixed> $data
     * @param        mixed        $separator
     * @param        array<mixed> $expected
     */
    public function testIntersperseArray(array $data, mixed $separator, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->intersperse($separator)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::intersperse (Generator)
     * @dataProvider dataProviderForIntersperse
     * @param        array<mixed> $data
     * @param        mixed        $separator
     * @param        array<mixed> $expected
     */
    public function testIntersperseGenerator(array $data, mixed $separator, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($iterable)
            ->intersperse($separator)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::intersperse (Iterator)
     * @dataProvider dataProviderForIntersperse
     * @param        array<mixed> $data
     * @param        mixed        $separator
     * @param        array<mixed> $expected
     */
    public function testIntersperseIterator(array $data, mixed $separator, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iterable)
            ->intersperse($separator)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::intersperse (IteratorAggregate)
     * @dataProvider dataProviderForIntersperse
     * @param        array<mixed> $data
     * @param        mixed        $separator
     * @param        array<mixed> $expected
     */
    public function testIntersperseIteratorAggregate(array $data, mixed $separator, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Stream::of($iterable)
            ->intersperse($separator)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForIntersperse(): array
    {
        return [
            [[], 0, []],
            [[1], 0, [1]],
            [[1, 2], 0, [1, 0, 2]],
            [[1, 2, 3], 0, [1, 0, 2, 0, 3]],
            [[1, 2, 3, 4, 5], 0, [1, 0, 2, 0, 3, 0, 4, 0, 5]],
            [['a', 'b', 'c'], '-', ['a', '-', 'b', '-', 'c']],
        ];
    }

    /**
     * @test         Stream::intersperse on empty input
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmpty(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->intersperse('x')
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test Stream::intersperse is chainable with other stream operations
     */
    public function testChainableWithOtherOperations(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5];

        // When
        $result = Stream::of($data)
            ->filter(fn (int $n): bool => $n % 2 === 1)
            ->intersperse(0)
            ->toArray();

        // Then
        $this->assertSame([1, 0, 3, 0, 5], $result);
    }
}
