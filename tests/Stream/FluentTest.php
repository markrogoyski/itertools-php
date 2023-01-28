<?php

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class FluentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function testOfFluent()
    {
        // Given
        $input = [1, 2, 3, 4, 5, 6, 7, 7, 8, 9, 10];

        // And
        $expected = [[4, 'four'], [20, 'twenty']];

        // When
        $result = Stream::of($input)
            ->chainWith([11, 12])                                // 1, 2, 3, 4, 5, 6, 7, 7, 8, 9, 10, 11, 12
            ->compress([0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0])  // 2, 3, 4, 5, 6, 7, 7, 8, 9, 10
            ->distinct()                                         // 2, 3, 4, 5, 6, 7, 8, 9, 10
            ->filterTrue(fn ($x) => $x > 2)                      // 3, 4, 5, 6, 7, 8, 9, 10
            ->filterFalse(fn ($x) => $x > 9)                     // 3, 4, 5, 6, 7, 8, 9
            ->intersectionWith([3, 4, 5, 6, 7, 8])               // 3, 4, 5, 6, 7, 8
            ->limit(5)                                      // 3, 4, 5, 6, 7
            ->map(fn ($x) => $x + 1)                             // 4, 5, 6, 7, 8
            ->runningMax()                                       // 4, 5, 6, 7, 8
            ->runningMin()                                       // 4, 4, 4, 4, 4
            ->runningProduct()                                   // 4, 16, 64, 256, 1024
            ->runningTotal()                                     // 4, 20, 84, 340, 1364
            ->symmetricDifferenceWith([340, 1364])               // 4, 20, 84
            ->takeWhile(fn ($x) => $x < 50)                      // 4, 20
            ->zipWith(['four', 'twenty'])                        // [4, 'four'], [20, 'twenty']
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }
}
