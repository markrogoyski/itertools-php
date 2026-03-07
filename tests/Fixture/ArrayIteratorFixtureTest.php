<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

class ArrayIteratorFixtureTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test implements Iterator interface
     */
    public function testImplementsIteratorInterface(): void
    {
        // Given
        $fixture = new ArrayIteratorFixture([]);

        // Then
        $this->assertInstanceOf(\Iterator::class, $fixture);
    }

    /**
     * @test empty array
     */
    public function testEmptyArray(): void
    {
        // Given
        $fixture = new ArrayIteratorFixture([]);

        // When
        $result = \iterator_to_array($fixture);

        // Then
        $this->assertFalse($fixture->valid());
        $this->assertSame([], $result);
    }

    /**
     * @test iterates values
     */
    public function testIteratesValues(): void
    {
        // Given
        $values = [10, 20, 30];
        $fixture = new ArrayIteratorFixture($values);

        // When
        $result = \iterator_to_array($fixture);

        // Then
        $this->assertSame($values, $result);
    }

    /**
     * @test keys are sequential integers
     */
    public function testKeysAreSequentialIntegers(): void
    {
        // Given
        $fixture = new ArrayIteratorFixture(['a', 'b', 'c']);

        // When
        $keys = [];
        foreach ($fixture as $key => $value) {
            $keys[] = $key;
        }

        // Then
        $this->assertSame([0, 1, 2], $keys);
    }

    /**
     * @test rewind resets iteration
     */
    public function testRewindResetsIteration(): void
    {
        // Given
        $fixture = new ArrayIteratorFixture([1, 2, 3]);
        $first = \iterator_to_array($fixture);

        // When
        $fixture->rewind();
        $second = \iterator_to_array($fixture);

        // Then
        $this->assertSame($first, $second);
    }

    /**
     * @test current returns current value
     */
    public function testCurrentReturnsCurrentValue(): void
    {
        // Given
        $fixture = new ArrayIteratorFixture([42, 99]);

        // When
        $first = $fixture->current();
        $fixture->next();
        $second = $fixture->current();

        // Then
        $this->assertSame(42, $first);
        $this->assertSame(99, $second);
    }

    /**
     * @test key returns current index
     */
    public function testKeyReturnsCurrentIndex(): void
    {
        // Given
        $fixture = new ArrayIteratorFixture(['a', 'b']);

        // When
        $firstKey = $fixture->key();
        $fixture->next();
        $secondKey = $fixture->key();

        // Then
        $this->assertSame(0, $firstKey);
        $this->assertSame(1, $secondKey);
    }

    /**
     * @test valid returns false after end
     */
    public function testValidReturnsFalseAfterEnd(): void
    {
        // Given
        $fixture = new ArrayIteratorFixture([1]);

        // When
        $validBefore = $fixture->valid();
        $fixture->next();
        $validAfter = $fixture->valid();

        // Then
        $this->assertTrue($validBefore);
        $this->assertFalse($validAfter);
    }

    /**
     * @test mixed value types
     */
    public function testMixedValueTypes(): void
    {
        // Given
        $values = [1, 'two', 3.0, null, true, [4, 5]];
        $fixture = new ArrayIteratorFixture($values);

        // When
        $result = \iterator_to_array($fixture);

        // Then
        $this->assertSame($values, $result);
    }
}
