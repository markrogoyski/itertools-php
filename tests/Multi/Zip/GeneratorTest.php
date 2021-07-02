<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\Zip;

use IterTools\Multi;
use IterTools\Tests\Fixture;

class GeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         zip with two generators of the same size
     * @dataProvider dataProviderForZipTwoGeneratorsSameSize
     * @param        \Generator $generator1
     * @param        \Generator $generator2
     * @param        array      $expected
     */
    public function testZipTwoGeneratorsSameSize(\Generator $generator1, \Generator $generator2, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::zip($generator1, $generator2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipTwoGeneratorsSameSize(): array
    {
        return [
            [
                Fixture\GeneratorFixture::getGenerator([]),
                Fixture\GeneratorFixture::getGenerator([]),
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1]),
                Fixture\GeneratorFixture::getGenerator([2]),
                [[1, 2]],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2]),
                Fixture\GeneratorFixture::getGenerator([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                Fixture\GeneratorFixture::getGenerator([4, 5, 6]),
                [[1, 4], [2, 5], [3, 6]],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                Fixture\GeneratorFixture::getGenerator([4, 5, 6, 7, 8, 9, 1, 2, 3]),
                [[1, 4], [2, 5], [3, 6], [4, 7], [5, 8], [6, 9], [7, 1], [8, 2], [9, 3]],
            ],
        ];
    }
}
