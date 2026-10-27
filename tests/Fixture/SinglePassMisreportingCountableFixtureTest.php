<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

class SinglePassMisreportingCountableFixtureTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test implements IteratorAggregate and Countable interfaces
     */
    public function testImplementsInterfaces(): void
    {
        // Given
        $fixture = new SinglePassMisreportingCountableFixture([], 0);

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
        $fixture = new SinglePassMisreportingCountableFixture([1, 2, 3], 10);

        // Then
        $this->assertCount(10, $fixture);
    }

    /**
     * @test iteration yields the given values with their keys
     */
    public function testIteratesValues(): void
    {
        // Given
        $values = ['a' => 1, 'b' => 2, 'c' => 3];
        $fixture = new SinglePassMisreportingCountableFixture($values, 99);

        // When
        $result = \iterator_to_array($fixture);

        // Then
        $this->assertSame($values, $result);
    }

    /**
     * @test a second traversal throws
     */
    public function testSecondTraversalThrows(): void
    {
        // Given
        $fixture = new SinglePassMisreportingCountableFixture([1, 2, 3], 3);
        \iterator_to_array($fixture);

        // Then
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot traverse an already closed generator');

        // When
        \iterator_to_array($fixture);
    }
}
