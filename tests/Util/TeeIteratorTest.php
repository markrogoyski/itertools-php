<?php

declare(strict_types=1);

namespace IterTools\Tests\Util;

use IterTools\Transform;
use IterTools\Util\Iterators\TeeIterator;

class TeeIteratorTest extends \PHPUnit\Framework\TestCase
{
    public function testErrorNextOnNull(): void
    {
        // Given
        $sourceIterator = Transform::toIterator([1, 2, 3]);

        // When
        $iterator = new TeeIterator($sourceIterator, 1);

        // Then
        $this->expectException(\LogicException::class);
        $iterator->next();
    }

    public function testErrorCurrentOnNull(): void
    {
        // Given
        $sourceIterator = Transform::toIterator([1, 2, 3]);

        // When
        $iterator = new TeeIterator($sourceIterator, 1);

        // Then
        $this->expectException(\LogicException::class);
        $iterator->current();
    }

    public function testErrorKeyOnNull(): void
    {
        // Given
        $sourceIterator = Transform::toIterator([1, 2, 3]);

        // When
        $iterator = new TeeIterator($sourceIterator, 1);

        // Then
        $this->expectException(\LogicException::class);
        $iterator->key();
    }

    public function testErrorValidOnNull(): void
    {
        // Given
        $sourceIterator = Transform::toIterator([1, 2, 3]);

        // When
        $iterator = new TeeIterator($sourceIterator, 1);

        // Then
        $this->expectException(\LogicException::class);
        $iterator->valid();
    }

    public function testRelatedNotValid(): void
    {
        // Given
        $sourceIterator = Transform::toIterator([]);

        // When
        $teeIterator = new TeeIterator($sourceIterator, 1);
        $relatedIterator = $teeIterator->getRelatedIterators()[0];

        // Then
        $this->assertFalse($teeIterator->valid($relatedIterator));
        $this->assertFalse($relatedIterator->valid());

        // When
        $relatedIterator->next();

        // Then
        $this->assertFalse($teeIterator->valid($relatedIterator));
        $this->assertFalse($relatedIterator->valid());
    }

    public function testRelatedCanBeRewindedRepeatedlyBeforeAdvancing(): void
    {
        // Given
        $teeIterator = new TeeIterator(Transform::toIterator([1, 2, 3]), 1);
        $relatedIterator = $teeIterator->getRelatedIterators()[0];

        // When
        $relatedIterator->rewind();
        $relatedIterator->rewind();
        $relatedIterator->rewind();

        // Then
        $this->assertSame([1, 2, 3], \iterator_to_array($relatedIterator));
    }

    public function testRelatedCannotBeRewindedAfterAdvancing(): void
    {
        // Given
        $teeIterator = new TeeIterator(Transform::toIterator([1, 2, 3]), 1);
        $relatedIterator = $teeIterator->getRelatedIterators()[0];
        $relatedIterator->next();

        // Then
        $this->expectException(\LogicException::class);

        // When
        $relatedIterator->rewind();
    }

    public function testDroppingAClonedRelatedLeavesTheOriginalUsable(): void
    {
        // Given
        $teeIterator = new TeeIterator(Transform::toIterator([1, 2, 3]), 1);
        $relatedIterator = $teeIterator->getRelatedIterators()[0];
        $clone = clone $relatedIterator;

        // When
        unset($clone);

        // Then
        $this->assertSame([1, 2, 3], \iterator_to_array($relatedIterator));
    }

    public function testClonedRelatedIsAnIndependentCursorFromTheSamePosition(): void
    {
        // Given
        $teeIterator = new TeeIterator(Transform::toIterator([1, 2, 3]), 1);
        $relatedIterator = $teeIterator->getRelatedIterators()[0];
        $relatedIterator->next();

        // When
        $clone = clone $relatedIterator;
        $clone->next();

        // Then
        $this->assertSame(2, $relatedIterator->current());
        $this->assertSame(3, $clone->current());

        // And when each cursor is walked to the end
        $original = [];
        while ($relatedIterator->valid()) {
            $original[$relatedIterator->key()] = $relatedIterator->current();
            $relatedIterator->next();
        }
        $cloned = [];
        while ($clone->valid()) {
            $cloned[$clone->key()] = $clone->current();
            $clone->next();
        }

        // Then
        $this->assertSame([1 => 2, 2 => 3], $original);
        $this->assertSame([2 => 3], $cloned);
    }

    public function testRelatedIteratorsAreFreedWithoutCycleCollection(): void
    {
        // Given
        $teeIterator = new TeeIterator(Transform::toIterator([1, 2, 3]), 2);
        $weakTee = \WeakReference::create($teeIterator);
        $related = $teeIterator->getRelatedIterators();
        $related[0]->valid();
        unset($teeIterator);

        // When
        unset($related);

        // Then
        $this->assertNull($weakTee->get());
    }

    public function testDroppedCopyDoesNotMakeTheOthersBufferEverything(): void
    {
        // Given
        $refs = [];
        $source = (static function () use (&$refs) {
            for ($i = 0; $i < 6; ++$i) {
                $item = new \stdClass();
                $refs[$i] = \WeakReference::create($item);
                yield $i => $item;
            }
        })();
        $teeIterator = new TeeIterator($source, 2);
        [$kept, $dropped] = $teeIterator->getRelatedIterators();
        unset($teeIterator);

        // And a copy that reads only the first element and is then dropped
        $dropped->valid();
        $dropped->current();
        unset($dropped);

        // When the remaining copy reads to the fifth element
        $kept->rewind();
        for ($i = 0; $i < 4; ++$i) {
            $kept->next();
        }
        $this->assertSame(4, $kept->key());

        // Then the elements it has moved past are no longer retained
        $this->assertNull($refs[0]->get());
        $this->assertNull($refs[1]->get());
        $this->assertNull($refs[2]->get());
    }
}
