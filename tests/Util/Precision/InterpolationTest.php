<?php

declare(strict_types=1);

namespace IterTools\Tests\Util\Precision;

use IterTools\Util\Precision\Interpolation;

class InterpolationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test rounding an expansion that is not nonadjacent throws rather than looping without end
     */
    public function testNonConvergingRoundingThrows(): void
    {
        // Given nonoverlapping components whose float sum 2^-52 is far from their value 2^-54 + 2^-53
        $expansion = [2 ** -54, -(1 - 2 ** -53), 1.0];

        // When
        try {
            Interpolation::roundExpansionDividedBy($expansion, 1.0);
            $this->fail('Expected a LogicException');
        } catch (\LogicException $e) {
            // Then
            $this->assertSame('Rounding an interpolated value did not converge.', $e->getMessage());
        }
    }
}
