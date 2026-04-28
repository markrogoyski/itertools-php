<?php

declare(strict_types=1);

namespace IterTools\Tests\Util;

use IterTools\Util\SortBoundedHeap;

class SortBoundedHeapTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test SortBoundedHeap is an SplHeap subclass
     */
    public function testIsSplHeap(): void
    {
        // Given
        $heap = new SortBoundedHeap(1);

        // Then
        $this->assertInstanceOf(\SplHeap::class, $heap);
    }

    /**
     * @test Min-heap direction (-1): root is the entry with the smallest key
     */
    public function testMinHeapDirectionRootIsSmallest(): void
    {
        // Given
        $heap = new SortBoundedHeap(-1);
        $heap->insert([10, 0, 'a']);
        $heap->insert([5, 1, 'b']);
        $heap->insert([20, 2, 'c']);
        $heap->insert([1, 3, 'd']);

        // When
        $root = $heap->top();

        // Then
        $this->assertSame([1, 3, 'd'], $root);
    }

    /**
     * @test Max-heap direction (+1): root is the entry with the largest key
     */
    public function testMaxHeapDirectionRootIsLargest(): void
    {
        // Given
        $heap = new SortBoundedHeap(1);
        $heap->insert([10, 0, 'a']);
        $heap->insert([5, 1, 'b']);
        $heap->insert([20, 2, 'c']);
        $heap->insert([1, 3, 'd']);

        // When
        $root = $heap->top();

        // Then
        $this->assertSame([20, 2, 'c'], $root);
    }

    /**
     * @test Min-heap extraction yields entries in ascending key order
     */
    public function testMinHeapExtractionOrder(): void
    {
        // Given
        $heap = new SortBoundedHeap(-1);
        foreach ([[3, 0, 'c'], [1, 1, 'a'], [4, 2, 'd'], [1, 3, 'b'], [5, 4, 'e']] as $entry) {
            $heap->insert($entry);
        }

        // When
        $extracted = [];
        foreach ($heap as $item) {
            $extracted[] = $item;
        }

        // Then: key ascending; among equal keys, higher insertion index extracted first
        $this->assertSame(
            [[1, 3, 'b'], [1, 1, 'a'], [3, 0, 'c'], [4, 2, 'd'], [5, 4, 'e']],
            $extracted
        );
    }

    /**
     * @test Max-heap extraction yields entries in descending key order
     */
    public function testMaxHeapExtractionOrder(): void
    {
        // Given
        $heap = new SortBoundedHeap(1);
        foreach ([[3, 0, 'c'], [1, 1, 'a'], [4, 2, 'd'], [4, 3, 'b'], [5, 4, 'e']] as $entry) {
            $heap->insert($entry);
        }

        // When
        $extracted = [];
        foreach ($heap as $item) {
            $extracted[] = $item;
        }

        // Then: key descending; among equal keys, higher insertion index extracted first
        $this->assertSame(
            [[5, 4, 'e'], [4, 3, 'b'], [4, 2, 'd'], [3, 0, 'c'], [1, 1, 'a']],
            $extracted
        );
    }

    /**
     * @test Tie-stability: higher insertion index has higher priority (closer to root)
     */
    public function testTieStabilityHigherIndexCloserToRoot(): void
    {
        // Given: a min-heap with three equal-keyed entries
        $heap = new SortBoundedHeap(-1);
        $heap->insert([10, 0, 'a']);
        $heap->insert([10, 1, 'b']);
        $heap->insert([10, 2, 'c']);

        // When
        $root = $heap->top();

        // Then: among equal keys, the youngest (highest insertion index) is at the root
        $this->assertSame([10, 2, 'c'], $root);
    }

    /**
     * @test Tie-stability: same rule applies for the max-heap direction
     */
    public function testTieStabilityHigherIndexCloserToRootMaxHeap(): void
    {
        // Given
        $heap = new SortBoundedHeap(1);
        $heap->insert([10, 0, 'a']);
        $heap->insert([10, 1, 'b']);
        $heap->insert([10, 2, 'c']);

        // When
        $root = $heap->top();

        // Then
        $this->assertSame([10, 2, 'c'], $root);
    }

    /**
     * @test count() reflects insert and extract operations
     */
    public function testCount(): void
    {
        // Given
        $heap = new SortBoundedHeap(-1);

        // Then
        $this->assertSame(0, $heap->count());

        // When
        $heap->insert([1, 0, 'a']);
        $heap->insert([2, 1, 'b']);

        // Then
        $this->assertSame(2, $heap->count());

        // When
        $heap->extract();

        // Then
        $this->assertSame(1, $heap->count());
    }

    /**
     * @test top() does not remove the root; extract() does
     */
    public function testTopDoesNotRemoveRoot(): void
    {
        // Given
        $heap = new SortBoundedHeap(-1);
        $heap->insert([5, 0, 'x']);
        $heap->insert([3, 1, 'y']);

        // When
        $first  = $heap->top();
        $second = $heap->top();

        // Then
        $this->assertSame([3, 1, 'y'], $first);
        $this->assertSame([3, 1, 'y'], $second);
        $this->assertSame(2, $heap->count());

        // When
        $extracted = $heap->extract();

        // Then
        $this->assertSame([3, 1, 'y'], $extracted);
        $this->assertSame(1, $heap->count());
    }

    /**
     * @test Heap supports string keys via spaceship operator
     */
    public function testStringKeys(): void
    {
        // Given
        $heap = new SortBoundedHeap(-1);
        $heap->insert(['cherry', 0, 1]);
        $heap->insert(['apple', 1, 2]);
        $heap->insert(['banana', 2, 3]);

        // When
        $root = $heap->top();

        // Then
        $this->assertSame(['apple', 1, 2], $root);
    }

    /**
     * @test Empty heap reports count zero and is empty
     */
    public function testEmptyHeap(): void
    {
        // Given
        $heap = new SortBoundedHeap(1);

        // Then
        $this->assertSame(0, $heap->count());
        $this->assertTrue($heap->isEmpty());
    }
}
