<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi;

use IterTools\Multi;

class IterZipErrorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         Zipping a non-iterable is a type error
     * @dataProvider dataProviderForNonIterables
     * @param        mixed $nonIterable
     */
    public function testNonIterableTypeError($nonIterable)
    {
        // Then
        $this->expectException(\TypeError::class);

        // When
        Multi::zip($nonIterable);
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
    public function testNothingToIterate()
    {
        // Given
        $nothing = [];
        $result  = [];

        // When
        foreach (Multi::zip($nothing) as $_) {
            $result[] = $_;
        }

        // Then
        $this->assertEmpty($result);
    }
}
