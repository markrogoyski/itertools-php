<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToModeTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test toMode example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $votes = ['red', 'blue', 'red', 'green', 'blue', 'red'];

        // When
        $modes = Reduce::toMode($votes);

        // Then
        $this->assertSame(['red'], $modes);
    }

    /**
     * @test         toMode array
     * @dataProvider dataProviderForMode
     * @param        array $data
     * @param        array $expected
     */
    public function testArray(array $data, array $expected): void
    {
        // When
        $result = Reduce::toMode($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toMode generator
     * @dataProvider dataProviderForMode
     * @param        array $data
     * @param        array $expected
     */
    public function testGenerator(array $data, array $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toMode($generator);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toMode iterator
     * @dataProvider dataProviderForMode
     * @param        array $data
     * @param        array $expected
     */
    public function testIterator(array $data, array $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toMode($iterator);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toMode traversable
     * @dataProvider dataProviderForMode
     * @param        array $data
     * @param        array $expected
     */
    public function testTraversable(array $data, array $expected): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toMode($traversable);

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForMode(): array
    {
        return [
            // Single element
            [[5], [5]],
            // Single mode
            [[1, 2, 2, 3], [2]],
            [[4, 4, 4, 1, 2], [4]],
            // Bimodal — both returned in first-seen order
            [[1, 1, 2, 2, 3], [1, 2]],
            [[3, 3, 1, 1, 2], [3, 1]],
            // Multi-modal
            [[1, 1, 2, 2, 3, 3], [1, 2, 3]],
            // All-unique — every value is a mode (first-seen order)
            [[1, 2, 3], [1, 2, 3]],
            [[3, 1, 2], [3, 1, 2]],
            // Negative numbers
            [[-1, -1, -2], [-1]],
            // Floats
            [[1.5, 1.5, 2.5], [1.5]],
            // Strings
            [['a', 'b', 'a', 'c'], ['a']],
        ];
    }

    /**
     * @test         toMode returns empty array on empty
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $data
     */
    public function testEmptyReturnsEmptyArray(iterable $data): void
    {
        // When
        $result = Reduce::toMode($data);

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test toMode preserves first-seen order of modes
     */
    public function testFirstSeenOrder(): void
    {
        // Given (10 appears first, 20 appears first among the tied modes)
        $data = [20, 10, 10, 20, 30];

        // When
        $modes = Reduce::toMode($data);

        // Then (20 and 10 both occur twice; 20 was seen first)
        $this->assertSame([20, 10], $modes);
    }

    /**
     * @test toMode is invariant to non-mode element order
     */
    public function testOrderInvarianceForSingleMode(): void
    {
        // Given
        $a = [1, 2, 2, 3, 4];
        $b = [4, 3, 2, 1, 2];

        // When
        $modeA = Reduce::toMode($a);
        $modeB = Reduce::toMode($b);

        // Then
        $this->assertSame([2], $modeA);
        $this->assertSame([2], $modeB);
    }

    /**
     * @test toMode distinguishes values by type (strict comparison)
     */
    public function testStrictTypeDistinctness(): void
    {
        // Given int 1, float 1.0, and string '1' are distinct under strict comparison
        $data = [1, 1.0, '1'];

        // When
        $modes = Reduce::toMode($data);

        // Then each occurs once, so all three are modes, in first-seen order
        $this->assertSame([1, 1.0, '1'], $modes);
    }

    /**
     * @test toMode finds the single mode in a very large collection
     */
    public function testVeryLargeN(): void
    {
        // Given 1..10000 each once, plus extra copies making 7777 the unique mode
        $data = \array_merge(\range(1, 10000), [7777, 7777]);

        // When
        $modes = Reduce::toMode($data);

        // Then
        $this->assertSame([7777], $modes);
    }
}
