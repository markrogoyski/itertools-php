<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\ZipFilled;

use IterTools\Multi;
use IterTools\Tests\Fixture;

class GeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         zipFilled with two generators of the same size
     * @dataProvider dataProviderForZipFilledTwoGeneratorsSameSize
     * @param        mixed $filler
     * @param        \Generator $generator1
     * @param        \Generator $generator2
     * @param        array      $expected
     */
    public function testZipFilledTwoGeneratorsSameSize($filler, \Generator $generator1, \Generator $generator2, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::zipFilled($filler, $generator1, $generator2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipFilledTwoGeneratorsSameSize(): array
    {
        return [
            [
                'filler',
                Fixture\GeneratorFixture::getGenerator([]),
                Fixture\GeneratorFixture::getGenerator([]),
                [],
            ],
            [
                'filler',
                Fixture\GeneratorFixture::getGenerator([1]),
                Fixture\GeneratorFixture::getGenerator([2]),
                [[1, 2]],
            ],
            [
                'filler',
                Fixture\GeneratorFixture::getGenerator([1, 2]),
                Fixture\GeneratorFixture::getGenerator([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                'filler',
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                Fixture\GeneratorFixture::getGenerator([4, 5, 6]),
                [[1, 4], [2, 5], [3, 6]],
            ],
            [
                'filler',
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                Fixture\GeneratorFixture::getGenerator([4, 5, 6, 7, 8, 9, 1, 2, 3]),
                [[1, 4], [2, 5], [3, 6], [4, 7], [5, 8], [6, 9], [7, 1], [8, 2], [9, 3]],
            ],
            [
                'filler',
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                Fixture\GeneratorFixture::getGenerator([4, 5, 6, 7, 8, 9, 1, 2]),
                [[1, 4], [2, 5], [3, 6], [4, 7], [5, 8], [6, 9], [7, 1], [8, 2], [9, 'filler']],
            ],
        ];
    }
}
