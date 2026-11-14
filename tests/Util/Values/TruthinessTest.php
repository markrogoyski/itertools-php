<?php

declare(strict_types=1);

namespace IterTools\Tests\Util\Values;

use IterTools\Util\Values\Truthiness;

class TruthinessTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         of coerces a value to bool as a (bool) cast does, without raising PHP 8.5's NAN-to-bool warning
     * @dataProvider dataProviderForOf
     * @param        mixed $value
     * @param        bool  $expected
     */
    public function testOf(mixed $value, bool $expected): void
    {
        // Given
        $raised   = [];
        $previous = \set_error_handler(static function (int $errno, string $errstr) use (&$raised): bool {
            $raised[] = $errstr;
            return true;
        });

        try {
            // When
            $result = Truthiness::of($value);
        } finally {
            \set_error_handler($previous);
        }

        // Then
        $this->assertSame([], $raised);
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with:
     *   PHP's documented conversion to bool (https://www.php.net/manual/en/language.types.boolean.php):
     *   false, 0, 0.0, -0.0, '', '0', [] and null are false, every other value is true, NAN included.
     *   Cross-checked for NAN with Python: bool(float('nan')) is True.
     *
     * @return array<array{mixed, bool}>
     */
    public static function dataProviderForOf(): array
    {
        return [
            [\NAN, true],
            [-\NAN, true],
            [\INF, true],
            [-\INF, true],
            [1.5, true],
            [-1.0, true],
            [\PHP_FLOAT_MIN, true],
            [0.0, false],
            [-0.0, false],
            [1, true],
            [-1, true],
            [0, false],
            ['a', true],
            ['0.0', true],
            [' ', true],
            ['NAN', true],
            ['0', false],
            ['', false],
            [[\NAN], true],
            [[0], true],
            [[], false],
            [null, false],
            [true, true],
            [false, false],
            [new \stdClass(), true],
        ];
    }
}
