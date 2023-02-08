<?php

declare(strict_types=1);

namespace IterTools\Tests\Util;

use IterTools\Transform;
use IterTools\Util\Iterators\StrictMultipleIterator;

class StrictMultipleIteratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test valid - empty
     */
    public function testValidEmpty(): void
    {
        // Given
        $iterator = new StrictMultipleIterator();

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertFalse($isValid);
    }

    /**
     * @test valid - one empty array
     */
    public function testValidOneEmptyArray(): void
    {
        // Given
        $iterator = new StrictMultipleIterator();

        // And
        $iterator->attachIterator(Transform::toIterator([]));

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertFalse($isValid);
    }

    /**
     * @test valid - one non-empty array, single element
     */
    public function testValidOneArraySingleElement(): void
    {
        // Given
        $iterator = new StrictMultipleIterator();

        // And
        $iterator->attachIterator(Transform::toIterator([0]));

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertTrue($isValid);
    }

    /**
     * @test valid - one non-empty array
     */
    public function testValidOneArray(): void
    {
        // Given
        $iterator = new StrictMultipleIterator();

        // And
        $iterator->attachIterator(Transform::toIterator([1, 2, 3]));

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertTrue($isValid);
    }

    /**
     * @test valid - two non-empty arrays, single elements
     */
    public function testValidTwoEqualArraysSingleElements(): void
    {
        // Given
        $iterator = new StrictMultipleIterator();

        // And
        $iterator->attachIterator(Transform::toIterator([1]));
        $iterator->attachIterator(Transform::toIterator([0]));

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertTrue($isValid);
    }

    /**
     * @test valid - two non-empty arrays
     */
    public function testValidTwoEqualArrays(): void
    {
        // Given
        $iterator = new StrictMultipleIterator();

        // And
        $iterator->attachIterator(Transform::toIterator([1, 2, 3]));
        $iterator->attachIterator(Transform::toIterator([3, 4, 5]));

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertTrue($isValid);
    }

    /**
     * @test valid - three non-empty arrays, single elements
     */
    public function testValidThreeEqualArraysSingleElements(): void
    {
        // Given
        $iterator = new StrictMultipleIterator();

        // And
        $iterator->attachIterator(Transform::toIterator([1]));
        $iterator->attachIterator(Transform::toIterator([3]));
        $iterator->attachIterator(Transform::toIterator([5]));

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertTrue($isValid);
    }

    /**
     * @test valid - three non-empty arrays
     */
    public function testValidThreeEqualArrays(): void
    {
        // Given
        $iterator = new StrictMultipleIterator();

        // And
        $iterator->attachIterator(Transform::toIterator([1, 2, 3]));
        $iterator->attachIterator(Transform::toIterator([3, 4, 5]));
        $iterator->attachIterator(Transform::toIterator([5, 6, 7]));

        // When
        $isValid = $iterator->valid();

        // Then
        $this->assertTrue($isValid);
    }

    /**
     * @test valid - two unequal arrays - first has fewer items
     */
    public function testValidTwoUnequalArraysFirstSmaller(): void
    {
        // Given
        $iterator = new StrictMultipleIterator();

        // And
        $iterator->attachIterator(Transform::toIterator([1, 2]));
        $iterator->attachIterator(Transform::toIterator([3, 4, 5]));

        // And
        $iterator->next();
        $iterator->next();

        // Then
        $this->expectException(\LengthException::class);

        // When
        $iterator->valid();
    }

    /**
     * @test valid - two unequal arrays - second has fewer items
     */
    public function testValidTwoUnequalArraysSecondSmaller(): void
    {
        // Given
        $iterator = new StrictMultipleIterator();

        // And
        $iterator->attachIterator(Transform::toIterator([1, 2, 3]));
        $iterator->attachIterator(Transform::toIterator([3, 4]));

        // And
        $iterator->next();
        $iterator->next();

        // Then
        $this->expectException(\LengthException::class);

        // When
        $iterator->valid();
    }

    /**
     * @test valid - three unequal arrays - first has fewer items
     */
    public function testValidThreeUnequalArraysFirstSmaller(): void
    {
        // Given
        $iterator = new StrictMultipleIterator();

        // And
        $iterator->attachIterator(Transform::toIterator([0, 1, 2]));
        $iterator->attachIterator(Transform::toIterator([0, 3, 4, 5]));
        $iterator->attachIterator(Transform::toIterator([0, 5, 6, 7]));

        // And
        $iterator->next();
        $iterator->next();
        $iterator->next();

        // Then
        $this->expectException(\LengthException::class);

        // When
        $iterator->valid();
    }

    /**
     * @test valid - three unequal arrays - second has fewer items
     */
    public function testValidThreeUnequalArraysSecondSmaller(): void
    {
        // Given
        $iterator = new StrictMultipleIterator();

        // And
        $iterator->attachIterator(Transform::toIterator([0, 1, 2, 3]));
        $iterator->attachIterator(Transform::toIterator([0, 3, 4]));
        $iterator->attachIterator(Transform::toIterator([0, 5, 6, 7]));

        // And
        $iterator->next();
        $iterator->next();
        $iterator->next();

        // Then
        $this->expectException(\LengthException::class);

        // When
        $iterator->valid();
    }

    /**
     * @test valid - three unequal arrays - third has fewer items
     */
    public function testValidThreeUnequalArraysThirdSmaller(): void
    {
        // Given
        $iterator = new StrictMultipleIterator();

        // And
        $iterator->attachIterator(Transform::toIterator([0, 1, 2, 3]));
        $iterator->attachIterator(Transform::toIterator([0, 3, 4, 5]));
        $iterator->attachIterator(Transform::toIterator([0, 5, 6]));

        // And
        $iterator->next();
        $iterator->next();
        $iterator->next();

        // Then
        $this->expectException(\LengthException::class);

        // When
        $iterator->valid();
    }
}
