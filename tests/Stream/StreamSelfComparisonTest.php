<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\CountingIteratorAggregateFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\NonSerializableFixture;
use IterTools\Tests\Fixture\WrappingIteratorAggregateFixture;

/**
 * sameWith, arePermutationsWith and arePermutationsCoerciveWith given the stream itself, or its source object.
 */
class StreamSelfComparisonTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         a stream over an array holding NAN is the same as itself
     * @dataProvider dataProviderForComparisons
     * @param        string $comparison
     */
    public function testNanStreamWithItself(string $comparison): void
    {
        // Given
        $stream = Stream::of([1, \NAN, 3]);

        // When
        $result = $stream->$comparison($stream);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         a stream of objects that cannot be serialized is the same as itself
     * @dataProvider dataProviderForComparisons
     * @param        string $comparison
     */
    public function testNonSerializableStreamWithItself(string $comparison): void
    {
        // Given
        $stream = Stream::of([new NonSerializableFixture(1), new NonSerializableFixture(2)]);

        // When
        $result = $stream->$comparison($stream, $stream);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         a stream over an advanced generator is the same as itself and does not read it
     * @dataProvider dataProviderForComparisons
     * @param        string $comparison
     */
    public function testAdvancedGeneratorStreamWithItself(string $comparison): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2, 3]);
        $generator->next();
        $stream = Stream::of($generator);

        // When
        $result = $stream->$comparison($stream);

        // Then
        $this->assertTrue($result);
        $this->assertSame(2, $generator->current());
    }

    /**
     * @test         a stream over an advanced generator is the same as that generator and does not read it
     * @dataProvider dataProviderForComparisons
     * @param        string $comparison
     */
    public function testAdvancedGeneratorStreamWithItsSource(string $comparison): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2, 3]);
        $generator->next();

        // When
        $result = Stream::of($generator)->$comparison($generator);

        // Then
        $this->assertTrue($result);
        $this->assertSame(2, $generator->current());
    }

    /**
     * @test         a stream over an IteratorAggregate is the same as it without a getIterator() call
     * @dataProvider dataProviderForComparisons
     * @param        string $comparison
     */
    public function testAggregateStreamWithItsSourceIsNotResolved(string $comparison): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $result = Stream::of($aggregate)->$comparison($aggregate);

        // Then
        $this->assertTrue($result);
        $this->assertSame(0, $aggregate->getIteratorCallCount());
    }

    /**
     * @test         a stream over an IteratorAggregate is the same as itself without a getIterator() call
     * @dataProvider dataProviderForComparisons
     * @param        string $comparison
     */
    public function testAggregateStreamWithItselfIsNotResolved(string $comparison): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture([1, 2, 3]);
        $stream    = Stream::of($aggregate);

        // When
        $result = $stream->$comparison($stream, $aggregate);

        // Then
        $this->assertTrue($result);
        $this->assertSame(0, $aggregate->getIteratorCallCount());
    }

    /**
     * @test         a stream over an IteratorAggregate whose getIterator() throws is the same as it
     * @dataProvider dataProviderForComparisons
     * @param        string $comparison
     */
    public function testThrowingAggregateStreamWithItsSource(string $comparison): void
    {
        // Given
        $aggregate = new class () implements \IteratorAggregate {
            public function getIterator(): \Iterator
            {
                throw new \RuntimeException('getIterator must not be called');
            }
        };

        // When
        $result = Stream::of($aggregate)->$comparison($aggregate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         a stream over a generator whose body throws is the same as it, and the body does not run
     * @dataProvider dataProviderForComparisons
     * @param        string $comparison
     */
    public function testThrowingGeneratorStreamWithItsSourceDoesNotRun(string $comparison): void
    {
        // Given
        $ran       = false;
        $generator = (static function () use (&$ran): \Generator {
            $ran = true;
            throw new \RuntimeException('boom');
            yield 1; // unreachable
        })();

        // When
        $result = Stream::of($generator)->$comparison($generator);

        // Then
        $this->assertTrue($result);
        $this->assertFalse($ran);
    }

    /**
     * @test         a stream over one aggregate is the same as a distinct aggregate returning the same generator
     * @dataProvider dataProviderForComparisons
     * @param        string $comparison
     */
    public function testDistinctWrappersOfOneIteratorDoNotRunIt(string $comparison): void
    {
        // Given
        $ran       = false;
        $generator = (static function () use (&$ran): \Generator {
            $ran = true;
            throw new \RuntimeException('boom');
            yield 1; // unreachable
        })();
        $first  = new WrappingIteratorAggregateFixture($generator);
        $second = new WrappingIteratorAggregateFixture($generator);

        // When
        $result = Stream::of($first)->$comparison($second);

        // Then
        $this->assertTrue($result);
        $this->assertFalse($ran);
    }

    public static function dataProviderForComparisons(): array
    {
        return [
            'sameWith'                    => ['sameWith'],
            'arePermutationsWith'         => ['arePermutationsWith'],
            'arePermutationsCoerciveWith' => ['arePermutationsCoerciveWith'],
        ];
    }
}
