<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\CountableIteratorAggregateFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToNthTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArray
     * @param array $data
     * @param int $position
     * @param int|float $expected
     */
    public function testArray(array $data, int $position, $expected): void
    {
        // When
        $result = Reduce::toNth($data, $position);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [1],
                0,
                1,
            ],
            [
                [1, 2],
                0,
                1,
            ],
            [
                [1, 2],
                1,
                2,
            ],
            [
                [1, 2, 3],
                0,
                1,
            ],
            [
                [1, 2, 3],
                1,
                2,
            ],
            [
                [1, 2, 3],
                2,
                3,
            ],
            [
                ['1', '2', '3'],
                0,
                '1',
            ],
            [
                ['1', '2', '3'],
                1,
                '2',
            ],
            [
                ['1', '2', '3'],
                2,
                '3',
            ],
            [
                ['a' => '1', 'b' => '2', 'c' => '3'],
                0,
                '1',
            ],
            [
                ['a' => '1', 'b' => '2', 'c' => '3'],
                1,
                '2',
            ],
            [
                ['a' => '1', 'b' => '2', 'c' => '3'],
                2,
                '3',
            ],
            [
                [1, 2.2, '3', 'four', [5], (object)[6], true, false, null],
                0,
                1,
            ],
            [
                [1, 2.2, '3', 'four', [5], (object)[6], true, false, null],
                1,
                2.2,
            ],
            [
                [1, 2.2, '3', 'four', [5], (object)[6], true, false, null],
                2,
                '3',
            ],
            [
                [1, 2.2, '3', 'four', [5], (object)[6], true, false, null],
                3,
                'four',
            ],
            [
                [1, 2.2, '3', 'four', [5], (object)[6], true, false, null],
                4,
                [5],
            ],
            [
                [1, 2.2, '3', 'four', [5], $o = (object)[6], true, false, null],
                5,
                $o,
            ],
            [
                [1, 2.2, '3', 'four', [5], (object)[6], true, false, null],
                6,
                true,
            ],
            [
                [1, 2.2, '3', 'four', [5], (object)[6], true, false, null],
                7,
                false,
            ],
            [
                [1, 2.2, '3', 'four', [5], (object)[6], true, false, null],
                8,
                null,
            ],
            [
                [1, 2.2, '3', 'four', [5], (object)[6], true, false, null],
                4,
                [5],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator $data
     * @param int $position
     * @param mixed $expected
     */
    public function testGenerators(\Generator $data, int $position, $expected): void
    {
        // When
        $result = Reduce::toNth($data, $position);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [
                $gen([1]),
                0,
                1,
            ],
            [
                $gen([1, 2]),
                0,
                1,
            ],
            [
                $gen([1, 2]),
                1,
                2,
            ],
            [
                $gen([1, 2, 3]),
                0,
                1,
            ],
            [
                $gen([1, 2, 3]),
                1,
                2,
            ],
            [
                $gen([1, 2, 3]),
                2,
                3,
            ],
            [
                $gen(['1', '2', '3']),
                0,
                '1',
            ],
            [
                $gen(['1', '2', '3']),
                1,
                '2',
            ],
            [
                $gen(['1', '2', '3']),
                2,
                '3',
            ],
            [
                $gen(['a' => '1', 'b' => '2', 'c' => '3']),
                0,
                '1',
            ],
            [
                $gen(['a' => '1', 'b' => '2', 'c' => '3']),
                1,
                '2',
            ],
            [
                $gen(['a' => '1', 'b' => '2', 'c' => '3']),
                2,
                '3',
            ],
            [
                $gen([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                0,
                1,
            ],
            [
                $gen([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                1,
                2.2,
            ],
            [
                $gen([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                2,
                '3',
            ],
            [
                $gen([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                3,
                'four',
            ],
            [
                $gen([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                4,
                [5],
            ],
            [
                $gen([1, 2.2, '3', 'four', [5], $o = (object)[6], true, false, null]),
                5,
                $o,
            ],
            [
                $gen([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                6,
                true,
            ],
            [
                $gen([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                7,
                false,
            ],
            [
                $gen([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                8,
                null,
            ],
            [
                $gen([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                4,
                [5],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator $data
     * @param int $position
     * @param mixed $expected
     */
    public function testIterators(\Iterator $data, int $position, $expected): void
    {
        // When
        $result = Reduce::toNth($data, $position);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = static function (array $data) {
            return new \ArrayIterator($data);
        };

        return [
            [
                $iter([1]),
                0,
                1,
            ],
            [
                $iter([1, 2]),
                0,
                1,
            ],
            [
                $iter([1, 2]),
                1,
                2,
            ],
            [
                $iter([1, 2, 3]),
                0,
                1,
            ],
            [
                $iter([1, 2, 3]),
                1,
                2,
            ],
            [
                $iter([1, 2, 3]),
                2,
                3,
            ],
            [
                $iter(['1', '2', '3']),
                0,
                '1',
            ],
            [
                $iter(['1', '2', '3']),
                1,
                '2',
            ],
            [
                $iter(['1', '2', '3']),
                2,
                '3',
            ],
            [
                $iter(['a' => '1', 'b' => '2', 'c' => '3']),
                0,
                '1',
            ],
            [
                $iter(['a' => '1', 'b' => '2', 'c' => '3']),
                1,
                '2',
            ],
            [
                $iter(['a' => '1', 'b' => '2', 'c' => '3']),
                2,
                '3',
            ],
            [
                $iter([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                0,
                1,
            ],
            [
                $iter([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                1,
                2.2,
            ],
            [
                $iter([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                2,
                '3',
            ],
            [
                $iter([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                3,
                'four',
            ],
            [
                $iter([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                4,
                [5],
            ],
            [
                $iter([1, 2.2, '3', 'four', [5], $o = (object)[6], true, false, null]),
                5,
                $o,
            ],
            [
                $iter([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                6,
                true,
            ],
            [
                $iter([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                7,
                false,
            ],
            [
                $iter([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                8,
                null,
            ],
            [
                $iter([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                4,
                [5],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable $data
     * @param int $position
     * @param mixed $expected
     */
    public function testTraversables(\Traversable $data, int $position, $expected): void
    {
        // When
        $result = Reduce::toNth($data, $position);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                $trav([1]),
                0,
                1,
            ],
            [
                $trav([1, 2]),
                0,
                1,
            ],
            [
                $trav([1, 2]),
                1,
                2,
            ],
            [
                $trav([1, 2, 3]),
                0,
                1,
            ],
            [
                $trav([1, 2, 3]),
                1,
                2,
            ],
            [
                $trav([1, 2, 3]),
                2,
                3,
            ],
            [
                $trav(['1', '2', '3']),
                0,
                '1',
            ],
            [
                $trav(['1', '2', '3']),
                1,
                '2',
            ],
            [
                $trav(['1', '2', '3']),
                2,
                '3',
            ],
            [
                $trav(['a' => '1', 'b' => '2', 'c' => '3']),
                0,
                '1',
            ],
            [
                $trav(['a' => '1', 'b' => '2', 'c' => '3']),
                1,
                '2',
            ],
            [
                $trav(['a' => '1', 'b' => '2', 'c' => '3']),
                2,
                '3',
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                0,
                1,
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                1,
                2.2,
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                2,
                '3',
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                3,
                'four',
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                4,
                [5],
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], $o = (object)[6], true, false, null]),
                5,
                $o,
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                6,
                true,
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                7,
                false,
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                8,
                null,
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                4,
                [5],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForCountables
     * @param CountableIteratorAggregateFixture $data
     * @param int $position
     * @param int|float $expected
     */
    public function testCountables(CountableIteratorAggregateFixture $data, int $position, $expected): void
    {
        // When
        $result = Reduce::toNth($data, $position);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForCountables(): array
    {
        $trav = static function (array $data) {
            return new CountableIteratorAggregateFixture($data);
        };

        return [
            [
                $trav([1]),
                0,
                1,
            ],
            [
                $trav([1, 2]),
                0,
                1,
            ],
            [
                $trav([1, 2]),
                1,
                2,
            ],
            [
                $trav([1, 2, 3]),
                0,
                1,
            ],
            [
                $trav([1, 2, 3]),
                1,
                2,
            ],
            [
                $trav([1, 2, 3]),
                2,
                3,
            ],
            [
                $trav(['1', '2', '3']),
                0,
                '1',
            ],
            [
                $trav(['1', '2', '3']),
                1,
                '2',
            ],
            [
                $trav(['1', '2', '3']),
                2,
                '3',
            ],
            [
                $trav(['a' => '1', 'b' => '2', 'c' => '3']),
                0,
                '1',
            ],
            [
                $trav(['a' => '1', 'b' => '2', 'c' => '3']),
                1,
                '2',
            ],
            [
                $trav(['a' => '1', 'b' => '2', 'c' => '3']),
                2,
                '3',
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                0,
                1,
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                1,
                2.2,
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                2,
                '3',
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                3,
                'four',
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                4,
                [5],
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], $o = (object)[6], true, false, null]),
                5,
                $o,
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                6,
                true,
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                7,
                false,
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                8,
                null,
            ],
            [
                $trav([1, 2.2, '3', 'four', [5], (object)[6], true, false, null]),
                4,
                [5],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForError
     * @param iterable $data
     * @param int $position
     * @return void
     */
    public function testError(iterable $data, int $position): void
    {
        // Then
        $this->expectException(\LengthException::class);
        $this->expectExceptionMessage("Given iterable does not contain item with position {$position}");

        // When
        Reduce::toNth($data, $position);
    }

    public function dataProviderForError(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };
        $cnt = static function (array $data) {
            return new CountableIteratorAggregateFixture($data);
        };

        return [
            [
                [],
                0,
            ],
            [
                $gen([]),
                0,
            ],
            [
                $iter([]),
                0,
            ],
            [
                $trav([]),
                0,
            ],
            [
                $cnt([]),
                0,
            ],
            [
                [],
                1,
            ],
            [
                $gen([]),
                1,
            ],
            [
                $iter([]),
                1,
            ],
            [
                $trav([]),
                1,
            ],
            [
                $cnt([]),
                1,
            ],
            [
                [1, 2, 3],
                3,
            ],
            [
                $gen([1, 2, 3]),
                3,
            ],
            [
                $iter([1, 2, 3]),
                3,
            ],
            [
                $trav([1, 2, 3]),
                3,
            ],
            [
                $cnt([1, 2, 3]),
                3,
            ],
            [
                [1, 2, 3],
                4,
            ],
            [
                $gen([1, 2, 3]),
                4,
            ],
            [
                $iter([1, 2, 3]),
                4,
            ],
            [
                $trav([1, 2, 3]),
                4,
            ],
            [
                $cnt([1, 2, 3]),
                4,
            ],
        ];
    }
}
