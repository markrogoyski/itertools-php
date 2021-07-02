<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Util;
use IterTools\Tests\Fixture;

class MakeIteratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test makeIterator array to iterator
     */
    public function testMakeArrayAnIterator(): void
    {
        // Given
        $array = [1, 2, 3, 4, 5];

        // When
        $iterator = Util::makeIterator($array);

        // Then
        $this->assertIsIterable($iterator);
        $this->assertInstanceOf(\Iterator::class, $iterator);
    }

    /**
     * @test makeIterator iterator to iterator
     */
    public function testMakeIteratorAnIterator(): void
    {
        // Given
        $arrayIterator = new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5]);

        // When
        $iterator = Util::makeIterator($arrayIterator);

        // Then
        $this->assertIsIterable($iterator);
        $this->assertInstanceOf(\Iterator::class, $iterator);
    }

    /**
     * @test makeIterator traversable to iterator
     */
    public function testMakeTraversableAnIterator(): void
    {
        // Given
        $iteratorAggregate = new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5]);

        // When
        $iterator = Util::makeIterator($iteratorAggregate);

        // Then
        $this->assertIsIterable($iterator);
        $this->assertInstanceOf(\Iterator::class, $iterator);
    }

    /**
     * @test makeIterator generator to iterator
     */
    public function testMakeGeneratorAnIterator(): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5]);

        // When
        $iterator = Util::makeIterator($generator);

        // Then
        $this->assertIsIterable($iterator);
        $this->assertInstanceOf(\Iterator::class, $iterator);
    }
}
