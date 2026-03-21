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
    public function testCycleArray(): void
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
    public function testCycleEmptyArray(): void
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
    public function testCycleGenerator(): void
    {
        // Given
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
    public function testCycleEmptyGenerator(): void
    {
        // Given
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
    public function testCycleIterator(): void
    {
        // Given
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
    public function testCycleEmptyIterator(): void
    {
        // Given
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
    public function testCycleTraversable(): void
    {
        // Given
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
    public function testCycleEmptyTraversable(): void
    {
        // Given
        $traversable = new Fixture\IteratorAggregateFixture([]);
        $result      = [];
        $expected    = [];

        // When
        foreach (Infinite::cycle($traversable) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle IteratorAggregate returning Generator
     */
    public function testCycleIteratorAggregateReturningGenerator(): void
    {
        // Given
        $agg = new class implements \IteratorAggregate {
            public function getIterator(): \Traversable
            {
                yield 1;
                yield 2;
                yield 3;
            }
        };

        // And
        $result    = [];
        $expected  = [1, 2, 3, 1, 2, 3];
        $count = 0;

        // When
        foreach (Infinite::cycle($agg) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 6) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle IteratorAggregate with duplicate keys
     */
    public function testCycleIteratorAggregateWithDuplicateKeys(): void
    {
        // Given
        $agg = new class implements \IteratorAggregate {
            public function getIterator(): \Traversable
            {
                yield 'a' => 1;
                yield 'a' => 2;
                yield 'b' => 3;
            }
        };

        // And
        $result    = [];
        $expected  = [1, 2, 3, 1, 2, 3];
        $count = 0;

        // When
        foreach (Infinite::cycle($agg) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 6) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle Generator with duplicate keys
     */
    public function testCycleGeneratorWithDuplicateKeys(): void
    {
        // Given
        $gen = function () {
            yield 'x' => 10;
            yield 'x' => 20;
        };

        // And
        $result    = [];
        $expected  = [10, 20, 10, 20];
        $count = 0;

        // When
        foreach (Infinite::cycle($gen()) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 4) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }
}
