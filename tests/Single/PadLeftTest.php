<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class PadLeftTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test padLeft example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = [];
        foreach (Single::padLeft($data, 5, 0) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame([0, 0, 1, 2, 3], $result);
    }

    /**
     * @test         padLeft (array)
     * @dataProvider dataProviderForPadLeft
     * @param        array<mixed> $data
     * @param        int          $length
     * @param        mixed        $fill
     * @param        array<mixed> $expected
     */
    public function testPadLeftArray(array $data, int $length, mixed $fill, array $expected): void
    {
        // When
        $result = [];
        foreach (Single::padLeft($data, $length, $fill) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         padLeft (Generator)
     * @dataProvider dataProviderForPadLeft
     * @param        array<mixed> $data
     * @param        int          $length
     * @param        mixed        $fill
     * @param        array<mixed> $expected
     */
    public function testPadLeftGenerator(array $data, int $length, mixed $fill, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = [];
        foreach (Single::padLeft($iterable, $length, $fill) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         padLeft (Iterator)
     * @dataProvider dataProviderForPadLeft
     * @param        array<mixed> $data
     * @param        int          $length
     * @param        mixed        $fill
     * @param        array<mixed> $expected
     */
    public function testPadLeftIterator(array $data, int $length, mixed $fill, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = [];
        foreach (Single::padLeft($iterable, $length, $fill) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         padLeft (IteratorAggregate)
     * @dataProvider dataProviderForPadLeft
     * @param        array<mixed> $data
     * @param        int          $length
     * @param        mixed        $fill
     * @param        array<mixed> $expected
     */
    public function testPadLeftIteratorAggregate(array $data, int $length, mixed $fill, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = [];
        foreach (Single::padLeft($iterable, $length, $fill) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForPadLeft(): array
    {
        return [
            // shorter source — pads
            [[1, 2, 3], 5, 0, [0, 0, 1, 2, 3]],
            // equal length — unchanged
            [[1, 2, 3], 3, 0, [1, 2, 3]],
            // longer source — unchanged (no truncation)
            [[1, 2, 3, 4, 5], 3, 0, [1, 2, 3, 4, 5]],
            // length 0 with non-empty source
            [[1, 2], 0, 'x', [1, 2]],
            // empty source + length 3
            [[], 3, 0, [0, 0, 0]],
            // empty source + length 0
            [[], 0, 0, []],
            // string fill
            [['a', 'b'], 4, 'x', ['x', 'x', 'a', 'b']],
            // null fill
            [[1], 3, null, [null, null, 1]],
        ];
    }

    /**
     * @test padLeft throws on negative length
     */
    public function testNegativeLengthThrows(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        \iterator_to_array(Single::padLeft([1, 2], -1, 0));
    }

    /**
     * @test padLeft output keys are sequential 0-indexed
     */
    public function testKeysSequential(): void
    {
        // Given
        $data = ['x' => 1, 'y' => 2];

        // When
        $keys = [];
        foreach (Single::padLeft($data, 4, 0) as $key => $value) {
            $keys[] = $key;
        }

        // Then
        $this->assertSame([0, 1, 2, 3], $keys);
    }
}
