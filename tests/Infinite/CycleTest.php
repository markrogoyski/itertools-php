<?php

declare(strict_types=1);

namespace IterTools\Tests\Infinite;

use IterTools\Infinite;
use IterTools\Tests\Fixture;

class CycleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test cycle array
     */
    public function testCycleArray()
    {
        // Given
        $array    = [1, 2, 3, 4, 5];
        $result   = [];
        $expected = [1, 2, 3, 4, 5, 1, 2, 3, 4, 5];

        // And
        $count = 0;

        // When
        foreach (Infinite::cycle($array) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 10) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle empty array
     */
    public function testCycleEmptyArray()
    {
        // Given
        $array    = [];
        $result   = [];
        $expected = [];

        // When
        foreach (Infinite::cycle($array) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle generator
     */
    public function testCycleGenerator()
    {
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5]);
        $result    = [];
        $expected  = [1, 2, 3, 4, 5, 1, 2, 3, 4, 5];

        // And
        $count = 0;

        // When
        foreach (Infinite::cycle($generator) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 10) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle generator
     */
    public function testCycleEmptyGenerator()
    {
        $generator = Fixture\GeneratorFixture::getGenerator([]);
        $result    = [];
        $expected  = [];

        // When
        foreach (Infinite::cycle($generator) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle iterator
     */
    public function testCycleIterator()
    {
        $iterator  = new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5]);
        $result    = [];
        $expected  = [1, 2, 3, 4, 5, 1, 2, 3, 4, 5];

        // And
        $count = 0;

        // When
        foreach (Infinite::cycle($iterator) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 10) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle empty iterator
     */
    public function testCycleEmptyIterator()
    {
        $iterator  = new Fixture\ArrayIteratorFixture([]);
        $result    = [];
        $expected  = [];

        // When
        foreach (Infinite::cycle($iterator) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle traversable
     */
    public function testCycleTraversable()
    {
        $traversable = new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5]);
        $result      = [];
        $expected    = [1, 2, 3, 4, 5, 1, 2, 3, 4, 5];

        // And
        $count = 0;

        // When
        foreach (Infinite::cycle($traversable) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 10) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle empty traversable
     */
    public function testCycleEmptyTraversable()
    {
        $traversable = new Fixture\IteratorAggregateFixture([]);
        $result      = [];
        $expected    = [];

        // When
        foreach (Infinite::cycle($traversable) as $item) {
            $result[] = $item;
            $count++;
        }

        // Then
        $this->assertEquals($expected, $result);
    }
}
