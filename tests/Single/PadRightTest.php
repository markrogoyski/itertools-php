<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class PadRightTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test padRight example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = [];
        foreach (Single::padRight($data, 5, 0) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame([1, 2, 3, 0, 0], $result);
    }

    /**
     * @test         padRight (array)
     * @dataProvider dataProviderForPadRight
     * @param        array<mixed> $data
     * @param        int          $length
     * @param        mixed        $fill
     * @param        array<mixed> $expected
     */
    public function testPadRightArray(array $data, int $length, mixed $fill, array $expected): void
    {
        // When
        $result = [];
        foreach (Single::padRight($data, $length, $fill) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         padRight (Generator)
     * @dataProvider dataProviderForPadRight
     * @param        array<mixed> $data
     * @param        int          $length
     * @param        mixed        $fill
     * @param        array<mixed> $expected
     */
    public function testPadRightGenerator(array $data, int $length, mixed $fill, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = [];
        foreach (Single::padRight($iterable, $length, $fill) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         padRight (Iterator)
     * @dataProvider dataProviderForPadRight
     * @param        array<mixed> $data
     * @param        int          $length
     * @param        mixed        $fill
     * @param        array<mixed> $expected
     */
    public function testPadRightIterator(array $data, int $length, mixed $fill, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = [];
        foreach (Single::padRight($iterable, $length, $fill) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         padRight (IteratorAggregate)
     * @dataProvider dataProviderForPadRight
     * @param        array<mixed> $data
     * @param        int          $length
     * @param        mixed        $fill
     * @param        array<mixed> $expected
     */
    public function testPadRightIteratorAggregate(array $data, int $length, mixed $fill, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = [];
        foreach (Single::padRight($iterable, $length, $fill) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForPadRight(): array
    {
        return [
            // shorter source — pads
            [[1, 2, 3], 5, 0, [1, 2, 3, 0, 0]],
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
            [['a', 'b'], 4, 'x', ['a', 'b', 'x', 'x']],
            // null fill
            [[1], 3, null, [1, null, null]],
        ];
    }

    /**
     * @test padRight throws on negative length
     */
    public function testNegativeLengthThrows(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        \iterator_to_array(Single::padRight([1, 2], -1, 0));
    }

    /**
     * @test padRight output keys are sequential 0-indexed
     */
    public function testKeysSequential(): void
    {
        // Given
        $data = ['x' => 1, 'y' => 2];

        // When
        $keys = [];
        foreach (Single::padRight($data, 4, 0) as $key => $value) {
            $keys[] = $key;
        }

        // Then
        $this->assertSame([0, 1, 2, 3], $keys);
    }
}
