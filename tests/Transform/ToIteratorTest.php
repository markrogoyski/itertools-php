<?php

declare(strict_types=1);

namespace IterTools\Tests\Transform;

use IterTools\Stream;
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

    /**
     * @test         toIterator gives the iterator an aggregate returns, not a wrapper around it
     * @dataProvider dataProviderForIterators
     * @param        \Iterator $inner
     */
    public function testAggregateGivesItsIterator(\Iterator $inner): void
    {
        // Given
        $aggregate = new Fixture\WrappingIteratorAggregateFixture($inner);

        // When
        $iterator = Transform::toIterator($aggregate);

        // Then
        $this->assertSame($inner, $iterator);
    }

    /**
     * @test         toIterator unwraps nested aggregates to the iterator the innermost one returns
     * @dataProvider dataProviderForIterators
     * @param        \Iterator $inner
     */
    public function testNestedAggregatesGiveInnermostIterator(\Iterator $inner): void
    {
        // Given
        $aggregate = new Fixture\WrappingIteratorAggregateFixture(
            new Fixture\WrappingIteratorAggregateFixture(
                new Fixture\WrappingIteratorAggregateFixture($inner)
            )
        );

        // When
        $iterator = Transform::toIterator($aggregate);

        // Then
        $this->assertSame($inner, $iterator);
    }

    /**
     * @test         toIterator gives the same iterator for two aggregates that return it
     * @dataProvider dataProviderForIterators
     * @param        \Iterator $inner
     */
    public function testAggregatesReturningSameIteratorGiveSameIterator(\Iterator $inner): void
    {
        // Given
        $aggregate1 = new Fixture\WrappingIteratorAggregateFixture($inner);
        $aggregate2 = new Fixture\WrappingIteratorAggregateFixture(new Fixture\WrappingIteratorAggregateFixture($inner));

        // When
        $iterator1 = Transform::toIterator($aggregate1);
        $iterator2 = Transform::toIterator($aggregate2);

        // Then
        $this->assertSame($iterator1, $iterator2);
    }

    /**
     * @return array
     */
    public static function dataProviderForIterators(): array
    {
        return [
            [Fixture\GeneratorFixture::getGenerator([1, 2, 3])],
            [new Fixture\ArrayIteratorFixture([1, 2, 3])],
            [new \ArrayIterator([1, 2, 3])],
        ];
    }

    /**
     * @test toIterator gives the generator a stream holds
     */
    public function testStreamGivesItsGenerator(): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $stream    = Stream::of($generator);

        // When
        $iterator = Transform::toIterator($stream);

        // Then
        $this->assertSame($generator, $iterator);
    }

    /**
     * @test toIterator gives the generator a stream of a stream holds
     */
    public function testStreamOfStreamGivesItsGenerator(): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $stream    = Stream::of(Stream::of($generator));

        // When
        $iterator = Transform::toIterator($stream);

        // Then
        $this->assertSame($generator, $iterator);
    }

    /**
     * @test         toIterator gives a fresh iterator on each call for an aggregate that returns one per call
     * @dataProvider dataProviderForReiterableAggregates
     * @param        \IteratorAggregate $aggregate
     * @param        array              $expected
     */
    public function testReiterableAggregateGivesFreshIterators(\IteratorAggregate $aggregate, array $expected): void
    {
        // When
        $iterator1 = Transform::toIterator($aggregate);
        $iterator2 = Transform::toIterator($aggregate);

        // Then
        $this->assertNotSame($iterator1, $iterator2);
        $this->assertSame($expected, \iterator_to_array($iterator1));
        $this->assertSame($expected, \iterator_to_array($iterator2));
    }

    /**
     * @return array
     */
    public static function dataProviderForReiterableAggregates(): array
    {
        return [
            [new \ArrayObject([1, 2, 3]), [1, 2, 3]],
            [new Fixture\IteratorAggregateFixture([1, 2, 3]), [1, 2, 3]],
            [Transform::memoize([1, 2, 3]), [1, 2, 3]],
            [Transform::memoize(Fixture\GeneratorFixture::getGenerator([1, 2, 3])), [1, 2, 3]],
            [Stream::of([1, 2, 3]), [1, 2, 3]],
        ];
    }

    /**
     * @test toIterator throws for an aggregate whose getIterator returns itself
     */
    public function testAggregateReturningItselfThrows(): void
    {
        // Given
        $aggregate = new Fixture\WrappingIteratorAggregateFixture();

        // Then
        $this->expectException(\LogicException::class);

        // When
        Transform::toIterator($aggregate);
    }

    /**
     * @test toIterator throws for a cycle of two aggregates
     */
    public function testCycleOfTwoAggregatesThrows(): void
    {
        // Given
        $aggregate1 = new Fixture\WrappingIteratorAggregateFixture();
        $aggregate2 = new Fixture\WrappingIteratorAggregateFixture($aggregate1);
        $aggregate1->wrap($aggregate2);

        // Then
        $this->expectException(\LogicException::class);

        // When
        Transform::toIterator($aggregate1);
    }

    /**
     * @test toIterator throws for an aggregate that leads into a cycle it is not part of
     */
    public function testAggregateLeadingIntoCycleThrows(): void
    {
        // Given
        $aggregate2 = new Fixture\WrappingIteratorAggregateFixture();
        $aggregate3 = new Fixture\WrappingIteratorAggregateFixture($aggregate2);
        $aggregate2->wrap($aggregate3);
        $aggregate1 = new Fixture\WrappingIteratorAggregateFixture($aggregate2);

        // Then
        $this->expectException(\LogicException::class);

        // When
        Transform::toIterator($aggregate1);
    }

    /**
     * @test toIterator throws for an aggregate whose getIterator returns a value that is not Traversable
     */
    public function testAggregateReturningNonTraversableThrows(): void
    {
        // Given
        $aggregate = new class () implements \IteratorAggregate {
            #[\ReturnTypeWillChange]
            public function getIterator(): mixed
            {
                return [1, 2, 3];
            }
        };

        // Then
        $this->expectException(\LogicException::class);

        // When
        Transform::toIterator($aggregate);
    }
}
