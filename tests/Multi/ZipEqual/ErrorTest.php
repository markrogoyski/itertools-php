<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\ZipEqual;

use IterTools\Multi;

class ErrorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         Zipping a non-iterable is a type error
     * @dataProvider dataProviderForNonIterables
     * @param        mixed $nonIterable
     */
    public function testNonIterableTypeError($nonIterable): void
    {
        // Then
        $this->expectException(\TypeError::class);

        // When
        Multi::zipEqual($nonIterable);
    }

    /**
     * @return array
     */
    public function dataProviderForNonIterables(): array
    {
        return [
            'int'    => [5],
            'float'  => [5.5],
            'string' => ['abc def'],
            'bool'   => [true],
            'object' => [new \stdClass()],
        ];
    }

    /**
     * @test Nothing to iterate does nothing
     */
    public function testNothingToIterate(): void
    {
        // Given
        $nothing = [];
        $result  = [];

        // When
        foreach (Multi::zipEqual($nothing) as $_) {
            $result[] = $_;
        }

        // Then
        $this->assertEmpty($result);
    }
}
