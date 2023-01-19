<?php

declare(strict_types=1);

namespace IterTools\Tests\Set;

use IterTools\Util\UniqueExtractor;
use IterTools\Tests\Fixture;

class UniqueExtractorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForStrict
     * @param mixed  $var
     * @param string $expectedPrefix
     */
    public function testStrict($var, string $expectedPrefix): void
    {
        // When
        $string = UniqueExtractor::getString($var, true);

        // Then
        $this->assertTrue(str_starts_with($string, $expectedPrefix), "got: $string");
    }

    public function dataProviderForStrict(): array
    {
        return [
            [
                [1, 2, 3],
                'array_'
            ],
            [
                \fopen('php://input', 'r'),
                'resource_'
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                'generator_'
            ],
            [
                fn ($x) => $x + 1,
                'closure_'
            ],
            [
                new \stdClass(),
                'object_'
            ],
            [
                true,
                'boolean_1'
            ],
            [
                false,
                'boolean_0'
            ],
            [
                1,
                'integer_1'
            ],
            [
                54,
                'integer_54'
            ],
            [
                0,
                'integer_0'
            ],
            [
                -4,
                'integer_-4'
            ],
            [
                1.0,
                'double_1'
            ],
            [
                1.1,
                'double_1.1'
            ],
            [
                'word',
                'string_'
            ],
            [
                '1',
                'string_1'
            ],
            [
                '1.0',
                'string_1.0'
            ],
            [
                \NAN,
                'double_'
            ],
            [
                null,
                'NULL_'
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForNotStrict
     * @param mixed  $var
     * @param string $expectedPrefix
     */
    public function testNotStrict($var, string $expectedPrefix): void
    {
        // When
        $string = UniqueExtractor::getString($var, false);

        // Then
        $this->assertTrue(str_starts_with($string, $expectedPrefix), "got: $string");
    }

    public function dataProviderForNotStrict(): array
    {
        return [
            [
                [1, 2, 3],
                'array_'
            ],
            [
                \fopen('php://input', 'r'),
                'resource_'
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                'generator_'
            ],
            [
                fn ($x) => $x + 1,
                'closure_'
            ],
            [
                new \stdClass(),
                'object_'
            ],
            [
                true,
                'boolean_1'
            ],
            [
                false,
                'boolean_0'
            ],
            [
                1,
                'boolean_1'
            ],
            [
                54,
                'numeric_54'
            ],
            [
                0,
                'boolean_0'
            ],
            [
                -4,
                'numeric_-4'
            ],
            [
                1.0,
                'boolean_1'
            ],
            [
                1.1,
                'numeric_1.1'
            ],
            [
                'word',
                'scalar_'
            ],
            [
                '1',
                'boolean_1'
            ],
            [
                '1.0',
                'numeric_1'
            ],
            [
                \NAN,
                'numeric_'
            ],
            [
                null,
                'boolean_0'
            ],
        ];
    }
}
