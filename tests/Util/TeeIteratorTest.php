<?php

declare(strict_types=1);

namespace IterTools\Tests\Util;

use IterTools\Util\IteratorFactory;
use IterTools\Util\Iterators\TeeIterator;

class TeeIteratorTest extends \PHPUnit\Framework\TestCase
{
    public function testErrorNextOnNull(): void
    {
        // Given
        $sourceIterator = IteratorFactory::makeIterator([1, 2, 3]);

        // When
        $iterator = new TeeIterator($sourceIterator, 1);

        // Then
        $this->expectException(\LogicException::class);
        $iterator->next();
    }

    public function testErrorCurrentOnNull(): void
    {
        // Given
        $sourceIterator = IteratorFactory::makeIterator([1, 2, 3]);

        // When
        $iterator = new TeeIterator($sourceIterator, 1);

        // Then
        $this->expectException(\LogicException::class);
        $iterator->current();
    }

    public function testErrorKeyOnNull(): void
    {
        // Given
        $sourceIterator = IteratorFactory::makeIterator([1, 2, 3]);

        // When
        $iterator = new TeeIterator($sourceIterator, 1);

        // Then
        $this->expectException(\LogicException::class);
        $iterator->key();
    }

    public function testErrorValidOnNull(): void
    {
        // Given
        $sourceIterator = IteratorFactory::makeIterator([1, 2, 3]);

        // When
        $iterator = new TeeIterator($sourceIterator, 1);

        // Then
        $this->expectException(\LogicException::class);
        $iterator->valid();
    }

    public function testRelatedNotValid(): void
    {
        // Given
        $sourceIterator = IteratorFactory::makeIterator([]);

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

    public function testRelatedCannotBeRewindedRepeatedly(): void
    {
        // Given
        $sourceIterator = IteratorFactory::makeIterator([]);

        // When
        $teeIterator = new TeeIterator($sourceIterator, 1);
        $relatedIterator = $teeIterator->getRelatedIterators()[0];
        $relatedIterator->rewind();

        // Then
        $this->expectException(\LogicException::class);
        $relatedIterator->rewind();
    }
}
