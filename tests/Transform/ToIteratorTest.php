<?php

declare(strict_types=1);

namespace IterTools\Tests\Transform;

use IterTools\Tests\Fixture;
use IterTools\Transform;

class ToIteratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test makeIterator array to iterator
     */
    public function testArray(): void
    {
        // Given
        $array = [1, 2, 3, 4, 5];

        // When
        $iterator = Transform::toIterator($array);

        // Then
        $this->assertIsIterable($iterator);
        $this->assertInstanceOf(\Iterator::class, $iterator);
    }

    /**
     * @test makeIterator iterator to iterator
     */
    public function testIterator(): void
    {
        // Given
        $arrayIterator = new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5]);

        // When
        $iterator = Transform::toIterator($arrayIterator);

        // Then
        $this->assertIsIterable($iterator);
        $this->assertInstanceOf(\Iterator::class, $iterator);
    }

    /**
     * @test makeIterator traversable to iterator
     */
    public function testTraversable(): void
    {
        // Given
        $iteratorAggregate = new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5]);

        // When
        $iterator = Transform::toIterator($iteratorAggregate);

        // Then
        $this->assertIsIterable($iterator);
        $this->assertInstanceOf(\Iterator::class, $iterator);
    }

    /**
     * @test makeIterator generator to iterator
     */
    public function testGenerator(): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5]);

        // When
        $iterator = Transform::toIterator($generator);

        // Then
        $this->assertIsIterable($iterator);
        $this->assertInstanceOf(\Iterator::class, $iterator);
    }
}
