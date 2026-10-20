<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

class MisreportingCountableFixtureTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test implements IteratorAggregate and Countable interfaces
     */
    public function testImplementsInterfaces(): void
    {
        // Given
        $fixture = new MisreportingCountableFixture([], 0);

        // Then
        $this->assertInstanceOf(\IteratorAggregate::class, $fixture);
        $this->assertInstanceOf(\Countable::class, $fixture);
    }

    /**
     * @test count reports the given size, not the number of elements
     */
    public function testCountReportsGivenSize(): void
    {
        // Given
        $fixture = new MisreportingCountableFixture([1, 2, 3], 0);

        // Then
        $this->assertCount(0, $fixture);
    }

    /**
     * @test iteration yields the given values with their keys
     */
    public function testIteratesValues(): void
    {
        // Given
        $values = ['a' => 1, 'b' => 2, 'c' => 3];
        $fixture = new MisreportingCountableFixture($values, 99);

        // When
        $result = \iterator_to_array($fixture);

        // Then
        $this->assertSame($values, $result);
    }
}
