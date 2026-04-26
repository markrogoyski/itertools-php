<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class MapSpreadTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         mapSpread array
     * @dataProvider dataProviderForArray
     * @param        array    $data
     * @param        callable $func
     * @param        array    $expected
     */
    public function testArray(array $data, callable $func, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::mapSpread($data, $func) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [
                [],
                fn ($a, $b) => $a + $b,
                [],
            ],
            [
                [[1, 2], [3, 4]],
                fn ($a, $b) => $a + $b,
                [3, 7],
            ],
            [
                [[1, 2], [3, 4], [5, 6]],
                fn ($a, $b) => $a * $b,
                [2, 12, 30],
            ],
            [
                [[1, 2, 3], [4, 5, 6]],
                fn ($a, $b, $c) => $a + $b + $c,
                [6, 15],
            ],
            [
                [['hello', 'world']],
                fn ($a, $b) => "$a $b",
                ['hello world'],
            ],
            [
                [[1, 2], [3, 4]],
                fn ($a, $b) => [$a, $b],
                [[1, 2], [3, 4]],
            ],
        ];
    }

    /**
     * @test         mapSpread generator
     * @dataProvider dataProviderForGenerator
     * @param        \Generator $data
     * @param        callable   $func
     * @param        array      $expected
     */
    public function testGenerator(\Generator $data, callable $func, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::mapSpread($data, $func) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForGenerator(): array
    {
        $gen = static fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                fn ($a, $b) => $a + $b,
                [],
            ],
            [
                $gen([[1, 2], [3, 4]]),
                fn ($a, $b) => $a + $b,
                [3, 7],
            ],
            [
                $gen([[1, 2, 3], [4, 5, 6]]),
                fn ($a, $b, $c) => $a + $b + $c,
                [6, 15],
            ],
        ];
    }

    /**
     * @test         mapSpread iterator
     * @dataProvider dataProviderForIterator
     * @param        \Iterator $data
     * @param        callable  $func
     * @param        array     $expected
     */
    public function testIterator(\Iterator $data, callable $func, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::mapSpread($data, $func) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForIterator(): array
    {
        $iter = static fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                fn ($a, $b) => $a + $b,
                [],
            ],
            [
                $iter([[1, 2], [3, 4]]),
                fn ($a, $b) => $a + $b,
                [3, 7],
            ],
            [
                $iter([[1, 2, 3], [4, 5, 6]]),
                fn ($a, $b, $c) => $a + $b + $c,
                [6, 15],
            ],
        ];
    }

    /**
     * @test         mapSpread IteratorAggregate
     * @dataProvider dataProviderForIteratorAggregate
     * @param        \Traversable $data
     * @param        callable     $func
     * @param        array        $expected
     */
    public function testIteratorAggregate(\Traversable $data, callable $func, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::mapSpread($data, $func) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForIteratorAggregate(): array
    {
        $agg = static fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $agg([]),
                fn ($a, $b) => $a + $b,
                [],
            ],
            [
                $agg([[1, 2], [3, 4]]),
                fn ($a, $b) => $a + $b,
                [3, 7],
            ],
            [
                $agg([[1, 2, 3], [4, 5, 6]]),
                fn ($a, $b, $c) => $a + $b + $c,
                [6, 15],
            ],
        ];
    }

    /**
     * @test mapSpread preserves outer keys for list-shape input
     */
    public function testOuterListKeysArePreserved(): void
    {
        // Given
        $data = [[1, 2], [3, 4], [5, 6]];

        // When
        $result = \iterator_to_array(Single::mapSpread($data, fn ($a, $b) => $a + $b));

        // Then
        $this->assertSame([0 => 3, 1 => 7, 2 => 11], $result);
    }

    /**
     * @test mapSpread inner element is a Generator
     */
    public function testInnerGenerator(): void
    {
        // Given
        $data = [
            GeneratorFixture::getGenerator([1, 2]),
            GeneratorFixture::getGenerator([3, 4]),
        ];

        // When
        $result = \iterator_to_array(Single::mapSpread($data, fn ($a, $b) => $a + $b), false);

        // Then
        $this->assertSame([3, 7], $result);
    }

    /**
     * @test mapSpread inner element is an Iterator (ArrayIteratorFixture)
     */
    public function testInnerIterator(): void
    {
        // Given
        $data = [
            new ArrayIteratorFixture([1, 2]),
            new ArrayIteratorFixture([3, 4]),
        ];

        // When
        $result = \iterator_to_array(Single::mapSpread($data, fn ($a, $b) => $a + $b), false);

        // Then
        $this->assertSame([3, 7], $result);
    }

    /**
     * @test mapSpread inner element is an IteratorAggregate
     */
    public function testInnerIteratorAggregate(): void
    {
        // Given
        $data = [
            new IteratorAggregateFixture([1, 2]),
            new IteratorAggregateFixture([3, 4]),
        ];

        // When
        $result = \iterator_to_array(Single::mapSpread($data, fn ($a, $b) => $a + $b), false);

        // Then
        $this->assertSame([3, 7], $result);
    }

    /**
     * @test mapSpread inner associative array with string keys does not bind as named arguments
     *
     * Regression: PHP 8 splats associative arrays as named arguments by default.
     * mapSpread must normalize each inner item to a list before splatting so that
     * values flow positionally even when inner keys are strings.
     */
    public function testInnerAssociativeArrayDoesNotBindAsNamedArgs(): void
    {
        // Given
        $data = [
            ['x' => 1, 'y' => 2],
            ['x' => 3, 'y' => 4],
        ];

        // When
        $result = \iterator_to_array(Single::mapSpread($data, fn ($a, $b) => $a + $b), false);

        // Then
        $this->assertSame([3, 7], $result);
    }

    /**
     * @test mapSpread preserves outer associative keys (native array)
     */
    public function testOuterAssociativeKeysPreservedArray(): void
    {
        // Given
        $data = ['x' => [1, 2], 'y' => [3, 4]];

        // When
        $result = \iterator_to_array(Single::mapSpread($data, fn ($a, $b) => $a + $b));

        // Then
        $this->assertSame(['x' => 3, 'y' => 7], $result);
    }

    /**
     * @test mapSpread preserves outer associative keys (key-value Generator)
     */
    public function testOuterAssociativeKeysPreservedKeyValueGenerator(): void
    {
        // Given
        $data = GeneratorFixture::getKeyValueGenerator(['x' => [1, 2], 'y' => [3, 4]]);

        // When
        $result = \iterator_to_array(Single::mapSpread($data, fn ($a, $b) => $a + $b));

        // Then
        $this->assertSame(['x' => 3, 'y' => 7], $result);
    }

    /**
     * @test mapSpread preserves outer associative keys (IteratorAggregate)
     */
    public function testOuterAssociativeKeysPreservedIteratorAggregate(): void
    {
        // Given
        $data = new IteratorAggregateFixture(['x' => [1, 2], 'y' => [3, 4]]);

        // When
        $result = \iterator_to_array(Single::mapSpread($data, fn ($a, $b) => $a + $b));

        // Then
        $this->assertSame(['x' => 3, 'y' => 7], $result);
    }

    /**
     * @test mapSpread throws InvalidArgumentException when inner element is not iterable
     */
    public function testNonIterableInnerThrows(): void
    {
        // Given
        $data = [[1, 2], 'not-iterable'];

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/1/');

        // When
        \iterator_to_array(Single::mapSpread($data, fn ($a, $b) => $a + $b), false);
    }

    /**
     * @test mapSpread throws when inner element is null
     */
    public function testNullInnerThrows(): void
    {
        // Given
        $data = [[1, 2], null];

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        \iterator_to_array(Single::mapSpread($data, fn ($a, $b) => $a + $b), false);
    }
}
