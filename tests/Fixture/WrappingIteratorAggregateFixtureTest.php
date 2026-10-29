<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

class WrappingIteratorAggregateFixtureTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test implements IteratorAggregate interface
     */
    public function testImplementsIteratorAggregateInterface(): void
    {
        // Given
        $fixture = new WrappingIteratorAggregateFixture(new \ArrayIterator([]));

        // Then
        $this->assertInstanceOf(\IteratorAggregate::class, $fixture);
    }

    /**
     * @test getIterator returns the wrapped object on every call
     */
    public function testGetIteratorReturnsWrappedObject(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2, 3]);
        $fixture   = new WrappingIteratorAggregateFixture($generator);

        // When
        $first  = $fixture->getIterator();
        $second = $fixture->getIterator();

        // Then
        $this->assertSame($generator, $first);
        $this->assertSame($generator, $second);
    }

    /**
     * @test wraps itself by default
     */
    public function testWrapsItselfByDefault(): void
    {
        // Given
        $fixture = new WrappingIteratorAggregateFixture();

        // When
        $result = $fixture->getIterator();

        // Then
        $this->assertSame($fixture, $result);
    }

    /**
     * @test wrap replaces the wrapped object
     */
    public function testWrapReplacesWrappedObject(): void
    {
        // Given
        $fixture  = new WrappingIteratorAggregateFixture(new \ArrayIterator([1]));
        $iterator = new \ArrayIterator([2]);

        // When
        $fixture->wrap($iterator);

        // Then
        $this->assertSame($iterator, $fixture->getIterator());
    }

    /**
     * @test iterates the wrapped values with their keys
     */
    public function testIteratesWrappedValues(): void
    {
        // Given
        $values  = ['a' => 1, 'b' => 2, 'c' => 3];
        $fixture = new WrappingIteratorAggregateFixture(new \ArrayIterator($values));

        // When
        $result = \iterator_to_array($fixture);

        // Then
        $this->assertSame($values, $result);
    }
}
