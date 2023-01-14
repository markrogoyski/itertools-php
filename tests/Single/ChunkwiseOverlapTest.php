<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ChunkwiseOverlapTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArray
     * @param array $data
     * @param int $chunkSize
     * @param int $overlapSize
     * @param array $expected
     */
    public function testArray(array $data, int $chunkSize, int $overlapSize, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::chunkwiseOverlap($data, $chunkSize, $overlapSize) as $datum) {
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
                0,
                [],
            ],
            [
                [],
                2,
                0,
                [],
            ],
            [
                [],
                2,
                1,
                [],
            ],
            [
                [1],
                1,
                0,
                [[1]],
            ],
            [
                [1],
                1,
                0,
                [[1]],
            ],
            [
                [1],
                2,
                0,
                [[1]],
            ],
            [
                [1],
                2,
                1,
                [[1]],
            ],
            [
                [1],
                3,
                0,
                [[1]],
            ],
            [
                [1],
                3,
                1,
                [[1]],
            ],
            [
                [1],
                3,
                2,
                [[1]],
            ],
            [
                [1, 2],
                1,
                0,
                [[1], [2]],
            ],
            [
                [1, 2],
                2,
                0,
                [[1, 2]],
            ],
            [
                [1, 2],
                2,
                1,
                [[1, 2]],
            ],
            [
                [1, 2],
                3,
                0,
                [[1, 2]],
            ],
            [
                [1, 2],
                3,
                1,
                [[1, 2]],
            ],
            [
                [1, 2],
                3,
                2,
                [[1, 2]],
            ],
            [
                [1, 2, 3],
                1,
                0,
                [[1], [2], [3]],
            ],
            [
                [1, 2, 3],
                2,
                0,
                [[1, 2], [3]],
            ],
            [
                [1, 2, 3],
                2,
                1,
                [[1, 2], [2, 3]],
            ],
            [
                [1, 2, 3],
                3,
                0,
                [[1, 2, 3]],
            ],
            [
                [1, 2, 3],
                3,
                1,
                [[1, 2, 3]],
            ],
            [
                [1, 2, 3],
                3,
                2,
                [[1, 2, 3]],
            ],
            [
                [1, 2, 3],
                4,
                0,
                [[1, 2, 3]],
            ],
            [
                [1, 2, 3],
                4,
                1,
                [[1, 2, 3]],
            ],
            [
                [1, 2, 3],
                4,
                2,
                [[1, 2, 3]],
            ],
            [
                [1, 2, 3],
                4,
                3,
                [[1, 2, 3]],
            ],
            [
                [1, 2, 3, 4],
                2,
                0,
                [[1, 2], [3, 4]],
            ],
            [
                [1, 2, 3, 4],
                2,
                1,
                [[1, 2], [2, 3], [3, 4]],
            ],
            [
                [1, 2, 3, 4],
                3,
                0,
                [[1, 2, 3], [4]],
            ],
            [
                [1, 2, 3, 4],
                3,
                1,
                [[1, 2, 3], [3, 4]],
            ],
            [
                [1, 2, 3, 4],
                3,
                2,
                [[1, 2, 3], [2, 3, 4]],
            ],
            [
                [1, 2, 3, 4, 5],
                2,
                0,
                [[1, 2], [3, 4], [5]],
            ],
            [
                [1, 2, 3, 4, 5],
                2,
                1,
                [[1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                [1, 2, 3, 4, 5],
                3,
                0,
                [[1, 2, 3], [4, 5]],
            ],
            [
                [1, 2, 3, 4, 5],
                3,
                1,
                [[1, 2, 3], [3, 4, 5]],
            ],
            [
                [1, 2, 3, 4, 5],
                3,
                2,
                [[1, 2, 3], [2, 3, 4], [3, 4, 5]],
            ],
            [
                [1.1, 2.2, 3.3, 4.4, 5.5],
                2,
                0,
                [[1.1, 2.2], [3.3, 4.4], [5.5]],
            ],
            [
                [1.1, 2.2, 3.3, 4.4, 5.5],
                2,
                1,
                [[1.1, 2.2], [2.2, 3.3], [3.3, 4.4], [4.4, 5.5]],
            ],
            [
                ['1', '2', '3', '4', '5'],
                2,
                0,
                [['1', '2'], ['3', '4'], ['5']],
            ],
            [
                ['1', '2', '3', '4', '5'],
                2,
                1,
                [['1', '2'], ['2', '3'], ['3', '4'], ['4', '5']],
            ],
            [
                [[1], [2], [3], [4], [5]],
                2,
                0,
                [[[1], [2]], [[3], [4]], [[5]]],
            ],
            [
                [[1], [2], [3], [4], [5]],
                2,
                1,
                [[[1], [2]], [[2], [3]], [[3], [4]], [[4], [5]]],
            ],
            [
                [true, true, false, false],
                2,
                0,
                [[true, true], [false, false]],
            ],
            [
                [true, true, false, false],
                2,
                1,
                [[true, true], [true, false], [false, false]],
            ],
            [
                [1, 2.2, '3', [4], true, null, 'test data'],
                3,
                0,
                [[1, 2.2, '3'], [[4], true, null], ['test data']],
            ],
            [
                [1, 2.2, '3', [4], true, null, 'test data'],
                3,
                1,
                [[1, 2.2, '3'], ['3', [4], true], [true, null, 'test data']],
            ],
            [
                [1, 2.2, '3', [4], true, null, 'test data'],
                3,
                2,
                [[1, 2.2, '3'], [2.2, '3', [4]], ['3', [4], true], [[4], true, null], [true, null, 'test data']],
            ],
            [
                ['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey', 'Phoebe'],
                2,
                0,
                [['Ross', 'Rachel'], ['Chandler', 'Monica'], ['Joey', 'Phoebe']],
            ],
            [
                ['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey', 'Phoebe'],
                2,
                1,
                [['Ross', 'Rachel'], ['Rachel', 'Chandler'], ['Chandler', 'Monica'], ['Monica', 'Joey'], ['Joey', 'Phoebe']],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                3,
                1,
                [[1, 2, 3], [3, 4, 5], [5, 6, 7], [7, 8, 9], [9, 10]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator $data
     * @param int $chunkSize
     * @param int $overlapSize
     * @param array $expected
     */
    public function testGenerators(\Generator $data, int $chunkSize, int $overlapSize, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::chunkwiseOverlap($data, $chunkSize, $overlapSize) as $datum) {
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
                0,
                [],
            ],
            [
                $gen([]),
                2,
                0,
                [],
            ],
            [
                $gen([]),
                2,
                1,
                [],
            ],
            [
                $gen([1]),
                1,
                0,
                [[1]],
            ],
            [
                $gen([1]),
                1,
                0,
                [[1]],
            ],
            [
                $gen([1]),
                2,
                0,
                [[1]],
            ],
            [
                $gen([1]),
                2,
                1,
                [[1]],
            ],
            [
                $gen([1]),
                3,
                0,
                [[1]],
            ],
            [
                $gen([1]),
                3,
                1,
                [[1]],
            ],
            [
                $gen([1]),
                3,
                2,
                [[1]],
            ],
            [
                $gen([1, 2]),
                1,
                0,
                [[1], [2]],
            ],
            [
                $gen([1, 2]),
                2,
                0,
                [[1, 2]],
            ],
            [
                $gen([1, 2]),
                2,
                1,
                [[1, 2]],
            ],
            [
                $gen([1, 2]),
                3,
                0,
                [[1, 2]],
            ],
            [
                $gen([1, 2]),
                3,
                1,
                [[1, 2]],
            ],
            [
                $gen([1, 2]),
                3,
                2,
                [[1, 2]],
            ],
            [
                $gen([1, 2, 3]),
                1,
                0,
                [[1], [2], [3]],
            ],
            [
                $gen([1, 2, 3]),
                2,
                0,
                [[1, 2], [3]],
            ],
            [
                $gen([1, 2, 3]),
                2,
                1,
                [[1, 2], [2, 3]],
            ],
            [
                $gen([1, 2, 3]),
                3,
                0,
                [[1, 2, 3]],
            ],
            [
                $gen([1, 2, 3]),
                3,
                1,
                [[1, 2, 3]],
            ],
            [
                $gen([1, 2, 3]),
                3,
                2,
                [[1, 2, 3]],
            ],
            [
                $gen([1, 2, 3]),
                4,
                0,
                [[1, 2, 3]],
            ],
            [
                $gen([1, 2, 3]),
                4,
                1,
                [[1, 2, 3]],
            ],
            [
                $gen([1, 2, 3]),
                4,
                2,
                [[1, 2, 3]],
            ],
            [
                $gen([1, 2, 3]),
                4,
                3,
                [[1, 2, 3]],
            ],
            [
                $gen([1, 2, 3, 4]),
                2,
                0,
                [[1, 2], [3, 4]],
            ],
            [
                $gen([1, 2, 3, 4]),
                2,
                1,
                [[1, 2], [2, 3], [3, 4]],
            ],
            [
                $gen([1, 2, 3, 4]),
                3,
                0,
                [[1, 2, 3], [4]],
            ],
            [
                $gen([1, 2, 3, 4]),
                3,
                1,
                [[1, 2, 3], [3, 4]],
            ],
            [
                $gen([1, 2, 3, 4]),
                3,
                2,
                [[1, 2, 3], [2, 3, 4]],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                2,
                0,
                [[1, 2], [3, 4], [5]],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                2,
                1,
                [[1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                3,
                0,
                [[1, 2, 3], [4, 5]],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                3,
                1,
                [[1, 2, 3], [3, 4, 5]],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                3,
                2,
                [[1, 2, 3], [2, 3, 4], [3, 4, 5]],
            ],
            [
                $gen([1.1, 2.2, 3.3, 4.4, 5.5]),
                2,
                0,
                [[1.1, 2.2], [3.3, 4.4], [5.5]],
            ],
            [
                $gen([1.1, 2.2, 3.3, 4.4, 5.5]),
                2,
                1,
                [[1.1, 2.2], [2.2, 3.3], [3.3, 4.4], [4.4, 5.5]],
            ],
            [
                $gen(['1', '2', '3', '4', '5']),
                2,
                0,
                [['1', '2'], ['3', '4'], ['5']],
            ],
            [
                $gen(['1', '2', '3', '4', '5']),
                2,
                1,
                [['1', '2'], ['2', '3'], ['3', '4'], ['4', '5']],
            ],
            [
                $gen([[1], [2], [3], [4], [5]]),
                2,
                0,
                [[[1], [2]], [[3], [4]], [[5]]],
            ],
            [
                $gen([[1], [2], [3], [4], [5]]),
                2,
                1,
                [[[1], [2]], [[2], [3]], [[3], [4]], [[4], [5]]],
            ],
            [
                $gen([true, true, false, false]),
                2,
                0,
                [[true, true], [false, false]],
            ],
            [
                $gen([true, true, false, false]),
                2,
                1,
                [[true, true], [true, false], [false, false]],
            ],
            [
                $gen([1, 2.2, '3', [4], true, null, 'test data']),
                3,
                0,
                [[1, 2.2, '3'], [[4], true, null], ['test data']],
            ],
            [
                $gen([1, 2.2, '3', [4], true, null, 'test data']),
                3,
                1,
                [[1, 2.2, '3'], ['3', [4], true], [true, null, 'test data']],
            ],
            [
                $gen([1, 2.2, '3', [4], true, null, 'test data']),
                3,
                2,
                [[1, 2.2, '3'], [2.2, '3', [4]], ['3', [4], true], [[4], true, null], [true, null, 'test data']],
            ],
            [
                $gen(['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey', 'Phoebe']),
                2,
                0,
                [['Ross', 'Rachel'], ['Chandler', 'Monica'], ['Joey', 'Phoebe']],
            ],
            [
                $gen(['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey', 'Phoebe']),
                2,
                1,
                [['Ross', 'Rachel'], ['Rachel', 'Chandler'], ['Chandler', 'Monica'], ['Monica', 'Joey'], ['Joey', 'Phoebe']],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator $data
     * @param int $chunkSize
     * @param int $overlapSize
     * @param array $expected
     */
    public function testIterators(\Iterator $data, int $chunkSize, int $overlapSize, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::chunkwiseOverlap($data, $chunkSize, $overlapSize) as $datum) {
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
                0,
                [],
            ],
            [
                $iter([]),
                2,
                0,
                [],
            ],
            [
                $iter([]),
                2,
                1,
                [],
            ],
            [
                $iter([1]),
                1,
                0,
                [[1]],
            ],
            [
                $iter([1]),
                1,
                0,
                [[1]],
            ],
            [
                $iter([1]),
                2,
                0,
                [[1]],
            ],
            [
                $iter([1]),
                2,
                1,
                [[1]],
            ],
            [
                $iter([1]),
                3,
                0,
                [[1]],
            ],
            [
                $iter([1]),
                3,
                1,
                [[1]],
            ],
            [
                $iter([1]),
                3,
                2,
                [[1]],
            ],
            [
                $iter([1, 2]),
                1,
                0,
                [[1], [2]],
            ],
            [
                $iter([1, 2]),
                2,
                0,
                [[1, 2]],
            ],
            [
                $iter([1, 2]),
                2,
                1,
                [[1, 2]],
            ],
            [
                $iter([1, 2]),
                3,
                0,
                [[1, 2]],
            ],
            [
                $iter([1, 2]),
                3,
                1,
                [[1, 2]],
            ],
            [
                $iter([1, 2]),
                3,
                2,
                [[1, 2]],
            ],
            [
                $iter([1, 2, 3]),
                1,
                0,
                [[1], [2], [3]],
            ],
            [
                $iter([1, 2, 3]),
                2,
                0,
                [[1, 2], [3]],
            ],
            [
                $iter([1, 2, 3]),
                2,
                1,
                [[1, 2], [2, 3]],
            ],
            [
                $iter([1, 2, 3]),
                3,
                0,
                [[1, 2, 3]],
            ],
            [
                $iter([1, 2, 3]),
                3,
                1,
                [[1, 2, 3]],
            ],
            [
                $iter([1, 2, 3]),
                3,
                2,
                [[1, 2, 3]],
            ],
            [
                $iter([1, 2, 3]),
                4,
                0,
                [[1, 2, 3]],
            ],
            [
                $iter([1, 2, 3]),
                4,
                1,
                [[1, 2, 3]],
            ],
            [
                $iter([1, 2, 3]),
                4,
                2,
                [[1, 2, 3]],
            ],
            [
                $iter([1, 2, 3]),
                4,
                3,
                [[1, 2, 3]],
            ],
            [
                $iter([1, 2, 3, 4]),
                2,
                0,
                [[1, 2], [3, 4]],
            ],
            [
                $iter([1, 2, 3, 4]),
                2,
                1,
                [[1, 2], [2, 3], [3, 4]],
            ],
            [
                $iter([1, 2, 3, 4]),
                3,
                0,
                [[1, 2, 3], [4]],
            ],
            [
                $iter([1, 2, 3, 4]),
                3,
                1,
                [[1, 2, 3], [3, 4]],
            ],
            [
                $iter([1, 2, 3, 4]),
                3,
                2,
                [[1, 2, 3], [2, 3, 4]],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                2,
                0,
                [[1, 2], [3, 4], [5]],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                2,
                1,
                [[1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                3,
                0,
                [[1, 2, 3], [4, 5]],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                3,
                1,
                [[1, 2, 3], [3, 4, 5]],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                3,
                2,
                [[1, 2, 3], [2, 3, 4], [3, 4, 5]],
            ],
            [
                $iter([1.1, 2.2, 3.3, 4.4, 5.5]),
                2,
                0,
                [[1.1, 2.2], [3.3, 4.4], [5.5]],
            ],
            [
                $iter([1.1, 2.2, 3.3, 4.4, 5.5]),
                2,
                1,
                [[1.1, 2.2], [2.2, 3.3], [3.3, 4.4], [4.4, 5.5]],
            ],
            [
                $iter(['1', '2', '3', '4', '5']),
                2,
                0,
                [['1', '2'], ['3', '4'], ['5']],
            ],
            [
                $iter(['1', '2', '3', '4', '5']),
                2,
                1,
                [['1', '2'], ['2', '3'], ['3', '4'], ['4', '5']],
            ],
            [
                $iter([[1], [2], [3], [4], [5]]),
                2,
                0,
                [[[1], [2]], [[3], [4]], [[5]]],
            ],
            [
                $iter([[1], [2], [3], [4], [5]]),
                2,
                1,
                [[[1], [2]], [[2], [3]], [[3], [4]], [[4], [5]]],
            ],
            [
                $iter([true, true, false, false]),
                2,
                0,
                [[true, true], [false, false]],
            ],
            [
                $iter([true, true, false, false]),
                2,
                1,
                [[true, true], [true, false], [false, false]],
            ],
            [
                $iter([1, 2.2, '3', [4], true, null, 'test data']),
                3,
                0,
                [[1, 2.2, '3'], [[4], true, null], ['test data']],
            ],
            [
                $iter([1, 2.2, '3', [4], true, null, 'test data']),
                3,
                1,
                [[1, 2.2, '3'], ['3', [4], true], [true, null, 'test data']],
            ],
            [
                $iter([1, 2.2, '3', [4], true, null, 'test data']),
                3,
                2,
                [[1, 2.2, '3'], [2.2, '3', [4]], ['3', [4], true], [[4], true, null], [true, null, 'test data']],
            ],
            [
                $iter(['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey', 'Phoebe']),
                2,
                0,
                [['Ross', 'Rachel'], ['Chandler', 'Monica'], ['Joey', 'Phoebe']],
            ],
            [
                $iter(['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey', 'Phoebe']),
                2,
                1,
                [['Ross', 'Rachel'], ['Rachel', 'Chandler'], ['Chandler', 'Monica'], ['Monica', 'Joey'], ['Joey', 'Phoebe']],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable $data
     * @param int $chunkSize
     * @param int $overlapSize
     * @param array $expected
     */
    public function testTraversables(\Traversable $data, int $chunkSize, int $overlapSize, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::chunkwiseOverlap($data, $chunkSize, $overlapSize) as $datum) {
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
                0,
                [],
            ],
            [
                $trav([]),
                2,
                0,
                [],
            ],
            [
                $trav([]),
                2,
                1,
                [],
            ],
            [
                $trav([1]),
                1,
                0,
                [[1]],
            ],
            [
                $trav([1]),
                1,
                0,
                [[1]],
            ],
            [
                $trav([1]),
                2,
                0,
                [[1]],
            ],
            [
                $trav([1]),
                2,
                1,
                [[1]],
            ],
            [
                $trav([1]),
                3,
                0,
                [[1]],
            ],
            [
                $trav([1]),
                3,
                1,
                [[1]],
            ],
            [
                $trav([1]),
                3,
                2,
                [[1]],
            ],
            [
                $trav([1, 2]),
                1,
                0,
                [[1], [2]],
            ],
            [
                $trav([1, 2]),
                2,
                0,
                [[1, 2]],
            ],
            [
                $trav([1, 2]),
                2,
                1,
                [[1, 2]],
            ],
            [
                $trav([1, 2]),
                3,
                0,
                [[1, 2]],
            ],
            [
                $trav([1, 2]),
                3,
                1,
                [[1, 2]],
            ],
            [
                $trav([1, 2]),
                3,
                2,
                [[1, 2]],
            ],
            [
                $trav([1, 2, 3]),
                1,
                0,
                [[1], [2], [3]],
            ],
            [
                $trav([1, 2, 3]),
                2,
                0,
                [[1, 2], [3]],
            ],
            [
                $trav([1, 2, 3]),
                2,
                1,
                [[1, 2], [2, 3]],
            ],
            [
                $trav([1, 2, 3]),
                3,
                0,
                [[1, 2, 3]],
            ],
            [
                $trav([1, 2, 3]),
                3,
                1,
                [[1, 2, 3]],
            ],
            [
                $trav([1, 2, 3]),
                3,
                2,
                [[1, 2, 3]],
            ],
            [
                $trav([1, 2, 3]),
                4,
                0,
                [[1, 2, 3]],
            ],
            [
                $trav([1, 2, 3]),
                4,
                1,
                [[1, 2, 3]],
            ],
            [
                $trav([1, 2, 3]),
                4,
                2,
                [[1, 2, 3]],
            ],
            [
                $trav([1, 2, 3]),
                4,
                3,
                [[1, 2, 3]],
            ],
            [
                $trav([1, 2, 3, 4]),
                2,
                0,
                [[1, 2], [3, 4]],
            ],
            [
                $trav([1, 2, 3, 4]),
                2,
                1,
                [[1, 2], [2, 3], [3, 4]],
            ],
            [
                $trav([1, 2, 3, 4]),
                3,
                0,
                [[1, 2, 3], [4]],
            ],
            [
                $trav([1, 2, 3, 4]),
                3,
                1,
                [[1, 2, 3], [3, 4]],
            ],
            [
                $trav([1, 2, 3, 4]),
                3,
                2,
                [[1, 2, 3], [2, 3, 4]],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                2,
                0,
                [[1, 2], [3, 4], [5]],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                2,
                1,
                [[1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                3,
                0,
                [[1, 2, 3], [4, 5]],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                3,
                1,
                [[1, 2, 3], [3, 4, 5]],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                3,
                2,
                [[1, 2, 3], [2, 3, 4], [3, 4, 5]],
            ],
            [
                $trav([1.1, 2.2, 3.3, 4.4, 5.5]),
                2,
                0,
                [[1.1, 2.2], [3.3, 4.4], [5.5]],
            ],
            [
                $trav([1.1, 2.2, 3.3, 4.4, 5.5]),
                2,
                1,
                [[1.1, 2.2], [2.2, 3.3], [3.3, 4.4], [4.4, 5.5]],
            ],
            [
                $trav(['1', '2', '3', '4', '5']),
                2,
                0,
                [['1', '2'], ['3', '4'], ['5']],
            ],
            [
                $trav(['1', '2', '3', '4', '5']),
                2,
                1,
                [['1', '2'], ['2', '3'], ['3', '4'], ['4', '5']],
            ],
            [
                $trav([[1], [2], [3], [4], [5]]),
                2,
                0,
                [[[1], [2]], [[3], [4]], [[5]]],
            ],
            [
                $trav([[1], [2], [3], [4], [5]]),
                2,
                1,
                [[[1], [2]], [[2], [3]], [[3], [4]], [[4], [5]]],
            ],
            [
                $trav([true, true, false, false]),
                2,
                0,
                [[true, true], [false, false]],
            ],
            [
                $trav([true, true, false, false]),
                2,
                1,
                [[true, true], [true, false], [false, false]],
            ],
            [
                $trav([1, 2.2, '3', [4], true, null, 'test data']),
                3,
                0,
                [[1, 2.2, '3'], [[4], true, null], ['test data']],
            ],
            [
                $trav([1, 2.2, '3', [4], true, null, 'test data']),
                3,
                1,
                [[1, 2.2, '3'], ['3', [4], true], [true, null, 'test data']],
            ],
            [
                $trav([1, 2.2, '3', [4], true, null, 'test data']),
                3,
                2,
                [[1, 2.2, '3'], [2.2, '3', [4]], ['3', [4], true], [[4], true, null], [true, null, 'test data']],
            ],
            [
                $trav(['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey', 'Phoebe']),
                2,
                0,
                [['Ross', 'Rachel'], ['Chandler', 'Monica'], ['Joey', 'Phoebe']],
            ],
            [
                $trav(['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey', 'Phoebe']),
                2,
                1,
                [['Ross', 'Rachel'], ['Rachel', 'Chandler'], ['Chandler', 'Monica'], ['Monica', 'Joey'], ['Joey', 'Phoebe']],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForChunkSizeError
     * @param iterable $data
     * @param int $chunkSize
     * @param int $overlapSize
     */
    public function testChunkSizeError(iterable $data, int $chunkSize, int $overlapSize): void
    {
        // Given
        try {
            // When
            foreach (Single::chunkwiseOverlap($data, $chunkSize, $overlapSize) as $_) {
                break;
            }
            $this->fail();
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertEquals("Chunk size must be ≥ 1. Got {$chunkSize}", $e->getMessage());
        }
    }

    public function dataProviderForChunkSizeError(): array
    {
        return [
            [
                [],
                0,
                0,
            ],
            [
                [],
                -1,
                0,
            ],
            [
                [1],
                0,
                0,
            ],
            [
                [1],
                -1,
                0,
            ],
            [
                [1, 2],
                0,
                0,
            ],
            [
                [1, 2, 3],
                0,
                0,
            ],
            [
                [1, 2, 3],
                -1,
                0,
            ],
            [
                [1, 2, 3],
                -2,
                0,
            ],
            [
                [1.1, 2.2, 3.3, 4.4, 5.5],
                0,
                0,
            ],
            [
                ['1', '2', '3', '4', '5'],
                -1,
                0,
            ],
            [
                [[1], [2], [3], [4], [5]],
                -2,
                0,
            ],
            [
                [true, true, false, false],
                0,
                0,
            ],
            [
                [1, 2.2, '3', [4], true, null, 'test data'],
                -1,
                0,
            ],
            [
                ['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey', 'Phoebe'],
                -2,
                0,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForOverlapSizeError
     * @param iterable $data
     * @param int $chunkSize
     * @param int $overlapSize
     */
    public function testOverlapSizeError(iterable $data, int $chunkSize, int $overlapSize): void
    {
        // Given
        try {
            // When
            foreach (Single::chunkwiseOverlap($data, $chunkSize, $overlapSize) as $_) {
                break;
            }
            $this->fail();
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertEquals("Overlap size must be less than chunk size", $e->getMessage());
        }
    }

    public function dataProviderForOverlapSizeError(): array
    {
        return [
            [
                [],
                1,
                1,
            ],
            [
                [],
                1,
                2,
            ],
            [
                [1],
                1,
                1,
            ],
            [
                [1],
                1,
                2,
            ],
            [
                [1, 2],
                1,
                1,
            ],
            [
                [1, 2],
                1,
                2,
            ],
            [
                [1, 2, 3],
                1,
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
                2,
            ],
            [
                [1, 2, 3],
                2,
                3,
            ],
            [
                [1.1, 2.2, 3.3, 4.4, 5.5],
                1,
                1,
            ],
            [
                ['1', '2', '3', '4', '5'],
                2,
                2,
            ],
            [
                [[1], [2], [3], [4], [5]],
                1,
                1,
            ],
            [
                [true, true, false, false],
                2,
                2,
            ],
            [
                [1, 2.2, '3', [4], true, null, 'test data'],
                1,
                1,
            ],
            [
                ['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey', 'Phoebe'],
                2,
                2,
            ],
        ];
    }
}
