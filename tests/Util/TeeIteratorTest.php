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

    public function testCopyDroppedByFreeingABufferedKeyLeavesTheNextValue(): void
    {
        // Given a key object that is the only holder of a copy which has moved past it
        $source = (static function (): \Generator {
            yield new \stdClass() => 'first';
            yield 'k2' => 'second';
        })();
        [$kept, $dropped] = Transform::tee($source, 2);
        $dropped->key()->copy = $dropped;
        $dropped->next();
        $dropped->valid();
        unset($dropped);

        // When the remaining copy moves past the key, which frees it and the copy it holds
        $kept->next();

        // Then
        $this->assertSame('k2', $kept->key());
        $this->assertSame('second', $kept->current());
    }

    public function testCopyDroppedByFreeingABufferedValueLeavesNothingRetained(): void
    {
        // Given a value object that is the only holder of a copy which has moved past it
        $refs = [];
        $source = (static function () use (&$refs): \Generator {
            for ($i = 0; $i < 4; ++$i) {
                $item = new \stdClass();
                $refs[$i] = \WeakReference::create($item);
                yield $i => $item;
            }
        })();
        [$kept, $dropped] = Transform::tee($source, 2);
        $dropped->next();
        $dropped->valid();
        $kept->current()->copy = $dropped;
        unset($dropped);

        // When the remaining copy moves past the value, which frees it and the copy it holds, then past the next
        $kept->next();
        $kept->next();

        // Then
        $this->assertSame(2, $kept->key());
        $this->assertNull($refs[0]->get());
        $this->assertNull($refs[1]->get());
    }

    public function testBufferOfALaggingCopyIsCompact(): void
    {
        // Given
        $n = 200_000;
        $source = (static function () use ($n): \Generator {
            for ($i = 0; $i < $n; ++$i) {
                yield $i;
            }
        })();
        [$drained, $lagging] = Transform::tee($source, 2);

        // When
        $before = \memory_get_usage();
        foreach ($drained as $_) {
        }
        $retained = \memory_get_usage() - $before;

        // Then
        $this->assertLessThan(64 * $n, $retained);
        $this->assertSame(0, $lagging->current());
    }

    public function testPeekStreamOfALargeGeneratorFitsInA128MLimit(): void
    {
        // Given
        $autoload = \dirname(__DIR__, 2) . '/vendor/autoload.php';
        $code = 'require ' . \var_export($autoload, true) . ';'
            . '$gen = (static function () { for ($i = 1; $i <= 600000; ++$i) { yield $i; } })();'
            . 'echo IterTools\\Stream::of($gen)->peekStream(fn ($s) => $s->toCount())->toSum();';
        $command = \escapeshellarg(\PHP_BINARY) . ' -d memory_limit=128M -r ' . \escapeshellarg($code) . ' 2>&1';

        // When
        $output = \shell_exec($command);

        // Then
        $this->assertSame('180000300000', $output);
    }

    /**
     * @test a clone of an advanced related iterator yields the rest of the source through iterator_to_array
     */
    public function testCloneBranchOfAdvancedRelatedYieldsTheRest(): void
    {
        // Given
        $teeIterator = new TeeIterator(Transform::toIterator([10, 20, 30]), 1);
        $relatedIterator = $teeIterator->getRelatedIterators()[0];
        $relatedIterator->rewind();
        $relatedIterator->next();

        // When
        $clone = clone $relatedIterator;

        // Then
        $this->assertSame([1 => 20, 2 => 30], \iterator_to_array($clone));
    }

    /**
     * @test a clone of an advanced related iterator can be rewound repeatedly before it advances
     */
    public function testCloneBranchCanBeRewoundBeforeAdvancing(): void
    {
        // Given
        $teeIterator = new TeeIterator(Transform::toIterator([10, 20, 30]), 1);
        $relatedIterator = $teeIterator->getRelatedIterators()[0];
        $relatedIterator->next();
        $clone = clone $relatedIterator;

        // When
        $clone->rewind();
        $clone->rewind();

        // Then
        $this->assertSame(20, $clone->current());
        $this->assertSame([1 => 20, 2 => 30], \iterator_to_array($clone));
    }

    /**
     * @test a clone of an advanced related iterator cannot be rewound once it advances itself
     */
    public function testCloneBranchCannotBeRewoundAfterAdvancing(): void
    {
        // Given
        $teeIterator = new TeeIterator(Transform::toIterator([10, 20, 30]), 1);
        $relatedIterator = $teeIterator->getRelatedIterators()[0];
        $relatedIterator->next();
        $clone = clone $relatedIterator;
        $clone->next();

        // When
        try {
            $clone->rewind();
            $this->fail('Expected a LogicException');
        } catch (\LogicException $e) {
            // Then
            $this->assertSame('RelatedIterator cannot be rewinded after it has advanced', $e->getMessage());
        }
    }

    /**
     * @test a clone and the related iterator it was cloned from are independent
     */
    public function testCloneBranchAndOriginalAreIndependent(): void
    {
        // Given
        $teeIterator = new TeeIterator(Transform::toIterator([10, 20, 30]), 1);
        $relatedIterator = $teeIterator->getRelatedIterators()[0];
        $relatedIterator->rewind();
        $relatedIterator->next();
        $clone = clone $relatedIterator;

        // When
        $cloned = [];
        foreach ($clone as $key => $value) {
            $cloned[$key] = $value;
        }

        // Then
        $this->assertSame([1 => 20, 2 => 30], $cloned);
        $this->assertSame(20, $relatedIterator->current());

        // And when the original is walked to the end
        $original = [];
        while ($relatedIterator->valid()) {
            $original[$relatedIterator->key()] = $relatedIterator->current();
            $relatedIterator->next();
        }

        // Then
        $this->assertSame([1 => 20, 2 => 30], $original);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('RelatedIterator cannot be rewinded after it has advanced');
        $relatedIterator->rewind();
    }

    /**
     * @test a clone of a clone starts at the position of the clone it was made from
     */
    public function testCloneBranchOfACloneStartsAtItsParentsPosition(): void
    {
        // Given
        $teeIterator = new TeeIterator(Transform::toIterator([10, 20, 30, 40]), 1);
        $relatedIterator = $teeIterator->getRelatedIterators()[0];
        $relatedIterator->next();
        $clone = clone $relatedIterator;
        $clone->next();

        // When
        $cloneOfClone = clone $clone;

        // Then
        $this->assertSame([2 => 30, 3 => 40], \iterator_to_array($cloneOfClone));
        $this->assertSame(30, $clone->current());
        $this->assertSame(20, $relatedIterator->current());
    }
}
