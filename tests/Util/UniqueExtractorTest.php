<?php

declare(strict_types=1);

namespace IterTools\Tests\Util;

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
        $this->assertTrue($this->startsWith($string, $expectedPrefix), "got: $string");
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
        $this->assertTrue($this->startsWith($string, $expectedPrefix), "got: $string");
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

    /**
     * @todo Replace usage with str_starts_with when library updated to PHP 8.0 as a minimum requirement.
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    protected function startsWith(string $haystack, string $needle): bool
    {
        return (\substr($haystack, 0, \strlen($needle)) === $needle);
    }

    /**
     * @test startsWith fixture method - true
     * @dataProvider dataProviderForStartsWithTrue
     * @param string $haystack
     * @param string $needle
     */
    public function testFixtureStartsWithTrue(string $haystack, string $needle): void
    {
        // When
        $startsWith = $this->startsWith($haystack, $needle);

        // Then
        $this->assertTrue($startsWith);
    }

    public function dataProviderForStartsWithTrue(): array
    {
        return [
            [
                '',
                '',
            ],
            [
                'a',
                'a'
            ],
            [
                'abc',
                'a'
            ],
            [
                'abc',
                'abc'
            ],
            [
                'The quick brown fox jumps over the lazy dog.',
                'T'
            ],
            [
                'The quick brown fox jumps over the lazy dog.',
                'The'
            ],
            [
                'The quick brown fox jumps over the lazy dog.',
                'The '
            ],
            [
                'The quick brown fox jumps over the lazy dog.',
                'The quick brown fox'
            ],
            [
                'The quick brown fox jumps over the lazy dog.',
                'The quick brown fox jumps over the lazy dog.'
            ],
        ];
    }

    /**
     * @test startsWith fixture method - false
     * @dataProvider dataProviderForStartsWithFalse
     * @param string $haystack
     * @param string $needle
     */
    public function testFixtureStartsWithFalse(string $haystack, string $needle): void
    {
        // When
        $startsWith = $this->startsWith($haystack, $needle);

        // Then
        $this->assertFalse($startsWith);
    }

    public function dataProviderForStartsWithFalse(): array
    {
        return [
            [
                '',
                'a',
            ],
            [
                'a',
                'b'
            ],
            [
                'abc',
                'bc'
            ],
            [
                'abc',
                'abb'
            ],
            [
                'The quick brown fox jumps over the lazy dog.',
                'he'
            ],
            [
                'The quick brown fox jumps over the lazy dog.',
                'quick'
            ],
            [
                'The quick brown fox jumps over the lazy dog.',
                ' The'
            ],
            [
                'The quick brown fox jumps over the lazy dog.',
                'Le quick brown fox'
            ],
            [
                'The quick brown fox jumps over the lazy dog.',
                ' over the lazy dog. The quick brown fox jumps'
            ],
        ];
    }
}
