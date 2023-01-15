<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ChunkwiseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArray
     * @param array $data
     * @param int $chunkSize
     * @param array $expected
     */
    public function testArray(array $data, int $chunkSize, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::chunkwise($data, $chunkSize) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                1,
                [],
            ],
            [
                [],
                2,
                [],
            ],
            [
                [1],
                1,
                [[1]],
            ],
            [
                [1],
                2,
                [[1]],
            ],
            [
                [1],
                3,
                [[1]],
            ],
            [
                [1, 2],
                1,
                [[1], [2]],
            ],
            [
                [1, 2],
                2,
                [[1, 2]],
            ],
            [
                [1, 2],
                3,
                [[1, 2]],
            ],
            [
                [1, 2, 3],
                1,
                [[1], [2], [3]],
            ],
            [
                [1, 2, 3],
                2,
                [[1, 2], [3]],
            ],
            [
                [1, 2, 3],
                3,
                [[1, 2, 3]],
            ],
            [
                [1, 2, 3],
                4,
                [[1, 2, 3]],
            ],
            [
                [1, 2, 3, 4],
                2,
                [[1, 2], [3, 4]],
            ],
            [
                [1, 2, 3, 4],
                3,
                [[1, 2, 3], [4]],
            ],
            [
                [1, 2, 3, 4, 5],
                2,
                [[1, 2], [3, 4], [5]],
            ],
            [
                [1, 2, 3, 4, 5],
                3,
                [[1, 2, 3], [4, 5]],
            ],
            [
                [1.1, 2.2, 3.3, 4.4, 5.5],
                2,
                [[1.1, 2.2], [3.3, 4.4], [5.5]],
            ],
            [
                ['1', '2', '3', '4', '5'],
                2,
                [['1', '2'], ['3', '4'], ['5']],
            ],
            [
                [[1], [2], [3], [4], [5]],
                2,
                [[[1], [2]], [[3], [4]], [[5]]],
            ],
            [
                [true, true, false, false],
                2,
                [[true, true], [false, false]],
            ],
            [
                [1, 2.2, '3', [4], true, null, 'test data'],
                3,
                [[1, 2.2, '3'], [[4], true, null], ['test data']],
            ],
            [
                ['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey', 'Phoebe'],
                2,
                [['Ross', 'Rachel'], ['Chandler', 'Monica'], ['Joey', 'Phoebe']],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator $data
     * @param int $chunkSize
     * @param array $expected
     */
    public function testGenerators(\Generator $data, int $chunkSize, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::chunkwise($data, $chunkSize) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [
                $gen([]),
                1,
                [],
            ],
            [
                $gen([]),
                2,
                [],
            ],
            [
                $gen([1]),
                1,
                [[1]],
            ],
            [
                $gen([1]),
                2,
                [[1]],
            ],
            [
                $gen([1]),
                3,
                [[1]],
            ],
            [
                $gen([1, 2]),
                1,
                [[1], [2]],
            ],
            [
                $gen([1, 2]),
                2,
                [[1, 2]],
            ],
            [
                $gen([1, 2]),
                3,
                [[1, 2]],
            ],
            [
                $gen([1, 2, 3]),
                1,
                [[1], [2], [3]],
            ],
            [
                $gen([1, 2, 3]),
                2,
                [[1, 2], [3]],
            ],
            [
                $gen([1, 2, 3]),
                3,
                [[1, 2, 3]],
            ],
            [
                $gen([1, 2, 3]),
                4,
                [[1, 2, 3]],
            ],
            [
                $gen([1, 2, 3, 4]),
                2,
                [[1, 2], [3, 4]],
            ],
            [
                $gen([1, 2, 3, 4]),
                3,
                [[1, 2, 3], [4]],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                2,
                [[1, 2], [3, 4], [5]],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                3,
                [[1, 2, 3], [4, 5]],
            ],
            [
                $gen([1.1, 2.2, 3.3, 4.4, 5.5]),
                2,
                [[1.1, 2.2], [3.3, 4.4], [5.5]],
            ],
            [
                $gen(['1', '2', '3', '4', '5']),
                2,
                [['1', '2'], ['3', '4'], ['5']],
            ],
            [
                $gen([[1], [2], [3], [4], [5]]),
                2,
                [[[1], [2]], [[3], [4]], [[5]]],
            ],
            [
                $gen([true, true, false, false]),
                2,
                [[true, true], [false, false]],
            ],
            [
                $gen([1, 2.2, '3', [4], true, null, 'test data']),
                3,
                [[1, 2.2, '3'], [[4], true, null], ['test data']],
            ],
            [
                $gen(['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey', 'Phoebe']),
                2,
                [['Ross', 'Rachel'], ['Chandler', 'Monica'], ['Joey', 'Phoebe']],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator $data
     * @param int $chunkSize
     * @param array $expected
     */
    public function testIterators(\Iterator $data, int $chunkSize, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::chunkwise($data, $chunkSize) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [
                $iter([]),
                1,
                [],
            ],
            [
                $iter([]),
                2,
                [],
            ],
            [
                $iter([1]),
                1,
                [[1]],
            ],
            [
                $iter([1]),
                2,
                [[1]],
            ],
            [
                $iter([1]),
                3,
                [[1]],
            ],
            [
                $iter([1, 2]),
                1,
                [[1], [2]],
            ],
            [
                $iter([1, 2]),
                2,
                [[1, 2]],
            ],
            [
                $iter([1, 2]),
                3,
                [[1, 2]],
            ],
            [
                $iter([1, 2, 3]),
                1,
                [[1], [2], [3]],
            ],
            [
                $iter([1, 2, 3]),
                2,
                [[1, 2], [3]],
            ],
            [
                $iter([1, 2, 3]),
                3,
                [[1, 2, 3]],
            ],
            [
                $iter([1, 2, 3]),
                4,
                [[1, 2, 3]],
            ],
            [
                $iter([1, 2, 3, 4]),
                2,
                [[1, 2], [3, 4]],
            ],
            [
                $iter([1, 2, 3, 4]),
                3,
                [[1, 2, 3], [4]],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                2,
                [[1, 2], [3, 4], [5]],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                3,
                [[1, 2, 3], [4, 5]],
            ],
            [
                $iter([1.1, 2.2, 3.3, 4.4, 5.5]),
                2,
                [[1.1, 2.2], [3.3, 4.4], [5.5]],
            ],
            [
                $iter(['1', '2', '3', '4', '5']),
                2,
                [['1', '2'], ['3', '4'], ['5']],
            ],
            [
                $iter([[1], [2], [3], [4], [5]]),
                2,
                [[[1], [2]], [[3], [4]], [[5]]],
            ],
            [
                $iter([true, true, false, false]),
                2,
                [[true, true], [false, false]],
            ],
            [
                $iter([1, 2.2, '3', [4], true, null, 'test data']),
                3,
                [[1, 2.2, '3'], [[4], true, null], ['test data']],
            ],
            [
                $iter(['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey', 'Phoebe']),
                2,
                [['Ross', 'Rachel'], ['Chandler', 'Monica'], ['Joey', 'Phoebe']],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable $data
     * @param int $chunkSize
     * @param array $expected
     */
    public function testTraversables(\Traversable $data, int $chunkSize, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::chunkwise($data, $chunkSize) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                $trav([]),
                1,
                [],
            ],
            [
                $trav([]),
                2,
                [],
            ],
            [
                $trav([1]),
                1,
                [[1]],
            ],
            [
                $trav([1]),
                2,
                [[1]],
            ],
            [
                $trav([1]),
                3,
                [[1]],
            ],
            [
                $trav([1, 2]),
                1,
                [[1], [2]],
            ],
            [
                $trav([1, 2]),
                2,
                [[1, 2]],
            ],
            [
                $trav([1, 2]),
                3,
                [[1, 2]],
            ],
            [
                $trav([1, 2, 3]),
                1,
                [[1], [2], [3]],
            ],
            [
                $trav([1, 2, 3]),
                2,
                [[1, 2], [3]],
            ],
            [
                $trav([1, 2, 3]),
                3,
                [[1, 2, 3]],
            ],
            [
                $trav([1, 2, 3]),
                4,
                [[1, 2, 3]],
            ],
            [
                $trav([1, 2, 3, 4]),
                2,
                [[1, 2], [3, 4]],
            ],
            [
                $trav([1, 2, 3, 4]),
                3,
                [[1, 2, 3], [4]],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                2,
                [[1, 2], [3, 4], [5]],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                3,
                [[1, 2, 3], [4, 5]],
            ],
            [
                $trav([1.1, 2.2, 3.3, 4.4, 5.5]),
                2,
                [[1.1, 2.2], [3.3, 4.4], [5.5]],
            ],
            [
                $trav(['1', '2', '3', '4', '5']),
                2,
                [['1', '2'], ['3', '4'], ['5']],
            ],
            [
                $trav([[1], [2], [3], [4], [5]]),
                2,
                [[[1], [2]], [[3], [4]], [[5]]],
            ],
            [
                $trav([true, true, false, false]),
                2,
                [[true, true], [false, false]],
            ],
            [
                $trav([1, 2.2, '3', [4], true, null, 'test data']),
                3,
                [[1, 2.2, '3'], [[4], true, null], ['test data']],
            ],
            [
                $trav(['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey', 'Phoebe']),
                2,
                [['Ross', 'Rachel'], ['Chandler', 'Monica'], ['Joey', 'Phoebe']],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForError
     * @param iterable $data
     * @param int $chunkSize
     */
    public function testError(iterable $data, int $chunkSize): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Chunk size must be ≥ 1. Got {$chunkSize}");

        // When
        foreach (Single::chunkwise($data, $chunkSize) as $_) {
            break;
        }
    }

    public function dataProviderForError(): array
    {
        return [
            [
                [],
                0,
            ],
            [
                [],
                -1,
            ],
            [
                [1],
                0,
            ],
            [
                [1],
                -1,
            ],
            [
                [1, 2],
                0,
            ],
            [
                [1, 2, 3],
                0,
            ],
            [
                [1, 2, 3],
                -1,
            ],
            [
                [1, 2, 3],
                -2,
            ],
            [
                [1.1, 2.2, 3.3, 4.4, 5.5],
                0,
            ],
            [
                ['1', '2', '3', '4', '5'],
                -1,
            ],
            [
                [[1], [2], [3], [4], [5]],
                -2,
            ],
            [
                [true, true, false, false],
                0,
            ],
            [
                [1, 2.2, '3', [4], true, null, 'test data'],
                -1,
            ],
            [
                ['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey', 'Phoebe'],
                -2,
            ],
        ];
    }
}
