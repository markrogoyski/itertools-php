<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Stream;
use IterTools\Summary;
use IterTools\Tests\Fixture;

/**
 * startsWith, startsWithCoercive, endsWith and endsWithCoercive given one source on both sides, and the
 * exceptions a source or a prefix or suffix throws.
 */
class StartsWithEndsWithSharedSourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         a generator shared through a Stream as the source is true without being read
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testGeneratorSharedThroughStreamAsSourceIsNotRead(callable $method): void
    {
        // Given
        $started = false;
        $generator = (static function () use (&$started): \Generator {
            $started = true;
            throw new \RuntimeException('boom');
            yield 1; // unreachable
        })();

        // When
        $result = $method(Stream::of($generator), $generator);

        // Then
        $this->assertTrue($result);
        $this->assertFalse($started);
    }

    /**
     * @test         a generator shared through a Stream as the prefix or suffix is true without being read
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testGeneratorSharedThroughStreamAsPrefixOrSuffixIsNotRead(callable $method): void
    {
        // Given
        $started = false;
        $generator = (static function () use (&$started): \Generator {
            $started = true;
            throw new \RuntimeException('boom');
            yield 1; // unreachable
        })();

        // When
        $result = $method($generator, Stream::of($generator));

        // Then
        $this->assertTrue($result);
        $this->assertFalse($started);
    }

    /**
     * @test         a generator shared through a Stream on each side is true without being read
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testGeneratorSharedThroughStreamsOnBothSidesIsNotRead(callable $method): void
    {
        // Given
        $started = false;
        $generator = (static function () use (&$started): \Generator {
            $started = true;
            throw new \RuntimeException('boom');
            yield 1; // unreachable
        })();

        // When
        $result = $method(Stream::of($generator), Stream::of($generator));

        // Then
        $this->assertTrue($result);
        $this->assertFalse($started);
    }

    /**
     * @test         a generator shared through nested Streams as the source is true without being read
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testGeneratorSharedThroughNestedStreamAsSourceIsNotRead(callable $method): void
    {
        // Given
        $started = false;
        $generator = (static function () use (&$started): \Generator {
            $started = true;
            throw new \RuntimeException('boom');
            yield 1; // unreachable
        })();

        // When
        $result = $method(Stream::of(Stream::of($generator)), $generator);

        // Then
        $this->assertTrue($result);
        $this->assertFalse($started);
    }

    /**
     * @test         a generator shared through nested Streams as the prefix or suffix is true without being read
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testGeneratorSharedThroughNestedStreamAsPrefixOrSuffixIsNotRead(callable $method): void
    {
        // Given
        $started = false;
        $generator = (static function () use (&$started): \Generator {
            $started = true;
            throw new \RuntimeException('boom');
            yield 1; // unreachable
        })();

        // When
        $result = $method($generator, Stream::of(Stream::of($generator)));

        // Then
        $this->assertTrue($result);
        $this->assertFalse($started);
    }

    /**
     * @test         an iterator shared through a Stream is true without being rewound or read
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testIteratorSharedThroughStreamIsNotRead(callable $method): void
    {
        // Given
        $pulled = [];
        $iterator = new Fixture\RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = $method(Stream::of($iterator), $iterator);

        // Then
        $this->assertTrue($result);
        $this->assertSame([], $pulled);
    }

    /**
     * @test         an empty generator shared through two Streams is true and can still be traversed
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testEmptyGeneratorSharedThroughStreamsStaysTraversable(callable $method): void
    {
        // Given
        $started = false;
        $generator = (static function () use (&$started): \Generator {
            $started = true;
            return;
            yield; // unreachable
        })();

        // When
        $result = $method(Stream::of($generator), Stream::of($generator));

        // Then
        $this->assertTrue($result);
        $this->assertFalse($started);
        $this->assertSame([], \iterator_to_array($generator));
    }

    /**
     * @test         an aggregate over a generator whose body throws, compared with it, propagates the exception
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testOpaqueSharedThrowingGeneratorAsSourcePropagates(callable $method): void
    {
        // Given
        $generator = (static function (): \Generator {
            throw new \RuntimeException('boom');
            yield 1; // unreachable
        })();
        $aggregate = new Fixture\WrappingIteratorAggregateFixture($generator);

        // Then
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('boom');
        $this->expectExceptionMessageMatches('/\Aboom\z/');

        // When
        $method($aggregate, $generator);
    }

    /**
     * @test         a generator whose body throws, with an aggregate over it as the prefix or suffix, propagates
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testOpaqueSharedThrowingGeneratorAsPrefixOrSuffixPropagates(callable $method): void
    {
        // Given
        $generator = (static function (): \Generator {
            throw new \RuntimeException('boom');
            yield 1; // unreachable
        })();
        $aggregate = new Fixture\WrappingIteratorAggregateFixture($generator);

        // Then
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('boom');
        $this->expectExceptionMessageMatches('/\Aboom\z/');

        // When
        $method($generator, $aggregate);
    }

    /**
     * @test         a body throwing an exception with the message of PHP's rewind failure propagates it
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testOpaqueSharedGeneratorThrowingRewindMessagePropagates(callable $method): void
    {
        // Given
        $thrown = new \Exception('Cannot rewind a generator that was already run');
        $generator = (static function () use ($thrown): \Generator {
            throw $thrown;
            yield 1; // unreachable
        })();
        $aggregate = new Fixture\WrappingIteratorAggregateFixture($generator);

        // When
        $caught = null;
        try {
            $method($aggregate, $generator);
        } catch (\Exception $e) {
            $caught = $e;
        }

        // Then
        $this->assertSame($thrown, $caught);
        $this->assertSame(\Exception::class, \get_class($caught));
        $this->assertSame('Cannot rewind a generator that was already run', $caught->getMessage());
    }

    /**
     * @test         a body failing to rewind another, advanced, generator propagates that failure
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testOpaqueSharedGeneratorRewindingAnotherGeneratorPropagates(callable $method): void
    {
        // Given
        $other = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $other->next();
        $generator = (static function () use ($other): \Generator {
            $other->rewind();
            yield 1;
        })();
        $aggregate = new Fixture\WrappingIteratorAggregateFixture($generator);

        // When
        $caught = null;
        try {
            $method($aggregate, $generator);
        } catch (\Exception $e) {
            $caught = $e;
        }

        // Then
        $this->assertInstanceOf(\Exception::class, $caught);
        $this->assertSame(\Exception::class, \get_class($caught));
        $this->assertSame('Cannot rewind a generator that was already run', $caught->getMessage());
    }

    /**
     * @test         a body whose own startsWith fails on an advanced generator prefix propagates that failure
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testOpaqueSharedGeneratorRethrowingNestedRewindFailurePropagates(callable $method): void
    {
        // Given
        $inner = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $inner->next();
        $generator = (static function () use ($inner): \Generator {
            Summary::startsWith(new \ArrayIterator([1]), $inner);
            yield 1;
        })();
        $aggregate = new Fixture\WrappingIteratorAggregateFixture($generator);

        // When
        $caught = null;
        try {
            $method($aggregate, $generator);
        } catch (\Exception $e) {
            $caught = $e;
        }

        // Then
        $this->assertInstanceOf(\Exception::class, $caught);
        $this->assertSame(\Exception::class, \get_class($caught));
        $this->assertSame('Cannot rewind a generator that was already run', $caught->getMessage());
    }

    /**
     * @test         a body rethrowing PHP's rewind failure saved from the same method propagates it
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testOpaqueSharedGeneratorRethrowingSavedRewindFailurePropagates(callable $method): void
    {
        // Given
        $advanced = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $advanced->next();
        $saved = null;
        try {
            $method(new \ArrayIterator([2, 3]), $advanced);
        } catch (\Exception $e) {
            $saved = $e;
        }
        $this->assertInstanceOf(\Exception::class, $saved);
        $generator = (static function () use ($saved): \Generator {
            throw $saved;
            yield 1; // unreachable
        })();
        $aggregate = new Fixture\WrappingIteratorAggregateFixture($generator);

        // When
        $caught = null;
        try {
            $method($aggregate, $generator);
        } catch (\Exception $e) {
            $caught = $e;
        }

        // Then
        $this->assertSame($saved, $caught);
        $this->assertSame(\Exception::class, \get_class($caught));
        $this->assertSame('Cannot rewind a generator that was already run', $caught->getMessage());
    }

    /**
     * @test         an aggregate over an advanced generator, compared with that generator, is true
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testOpaqueSharedAdvancedGeneratorAsSourceIsTrue(callable $method): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $generator->next();
        $aggregate = new Fixture\WrappingIteratorAggregateFixture($generator);

        // When
        $result = $method($aggregate, $generator);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         an advanced generator, with an aggregate over it as the prefix or suffix, is true
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testOpaqueSharedAdvancedGeneratorAsPrefixOrSuffixIsTrue(callable $method): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $generator->next();
        $aggregate = new Fixture\WrappingIteratorAggregateFixture($generator);

        // When
        $result = $method($generator, $aggregate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         an aggregate over a generator, compared with that generator, reads it, then is true
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testOpaqueSharedGeneratorAsSourceIsReadThenTrue(callable $method): void
    {
        // Given
        $started = false;
        $generator = (static function () use (&$started): \Generator {
            $started = true;
            yield 1;
            yield 2;
            yield 3;
        })();
        $aggregate = new Fixture\WrappingIteratorAggregateFixture($generator);

        // When
        $result = $method($aggregate, $generator);

        // Then
        $this->assertTrue($result);
        $this->assertTrue($started);
    }

    /**
     * @test         a generator, with an aggregate over it as the prefix or suffix, reads it, then is true
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testOpaqueSharedGeneratorAsPrefixOrSuffixIsReadThenTrue(callable $method): void
    {
        // Given
        $started = false;
        $generator = (static function () use (&$started): \Generator {
            $started = true;
            yield 1;
            yield 2;
            yield 3;
        })();
        $aggregate = new Fixture\WrappingIteratorAggregateFixture($generator);

        // When
        $result = $method($generator, $aggregate);

        // Then
        $this->assertTrue($result);
        $this->assertTrue($started);
    }

    /**
     * @test         a throwing generator as the source, with a distinct prefix or suffix, propagates its exception
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testDistinctThrowingGeneratorAsSourcePropagates(callable $method): void
    {
        // Given
        $boom = new \RuntimeException('boom');
        $againstArray = (static function () use ($boom): \Generator {
            throw $boom;
            yield 1; // unreachable
        })();
        $againstIterator = (static function () use ($boom): \Generator {
            throw $boom;
            yield 1; // unreachable
        })();

        // When
        $caughtAgainstArray = null;
        try {
            $method($againstArray, [1]);
        } catch (\RuntimeException $e) {
            $caughtAgainstArray = $e;
        }
        $caughtAgainstIterator = null;
        try {
            $method($againstIterator, new \ArrayIterator([1]));
        } catch (\RuntimeException $e) {
            $caughtAgainstIterator = $e;
        }

        // Then
        $this->assertSame($boom, $caughtAgainstArray);
        $this->assertSame($boom, $caughtAgainstIterator);
        $this->assertSame(\RuntimeException::class, \get_class($caughtAgainstIterator));
        $this->assertSame('boom', $caughtAgainstIterator->getMessage());
    }

    /**
     * @test         a throwing generator as the prefix or suffix of a distinct source propagates its exception
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testDistinctThrowingGeneratorAsPrefixOrSuffixPropagates(callable $method): void
    {
        // Given
        $boom = new \RuntimeException('boom');
        $ofIterator = (static function () use ($boom): \Generator {
            throw $boom;
            yield 1; // unreachable
        })();
        $ofStream = (static function () use ($boom): \Generator {
            throw $boom;
            yield 1; // unreachable
        })();

        // When
        $caughtOfIterator = null;
        try {
            $method(new \ArrayIterator([1]), $ofIterator);
        } catch (\RuntimeException $e) {
            $caughtOfIterator = $e;
        }
        $caughtOfStream = null;
        try {
            $method(Stream::of([1]), $ofStream);
        } catch (\RuntimeException $e) {
            $caughtOfStream = $e;
        }

        // Then
        $this->assertSame($boom, $caughtOfIterator);
        $this->assertSame($boom, $caughtOfStream);
        $this->assertSame(\RuntimeException::class, \get_class($caughtOfStream));
        $this->assertSame('boom', $caughtOfStream->getMessage());
    }

    /**
     * @test         an advanced generator as the prefix or suffix of a distinct source propagates PHP's rewind failure
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testDistinctAdvancedGeneratorAsPrefixOrSuffixPropagatesRewindFailure(callable $method): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $generator->next();

        // When
        $caught = null;
        try {
            $method(new \ArrayIterator([2, 3]), $generator);
        } catch (\Exception $e) {
            $caught = $e;
        }

        // Then
        $this->assertInstanceOf(\Exception::class, $caught);
        $this->assertSame(\Exception::class, \get_class($caught));
        $this->assertSame('Cannot rewind a generator that was already run', $caught->getMessage());
    }

    /**
     * @test         an empty prefix or suffix is true without the aggregate source's iterator being obtained
     * @dataProvider dataProviderForMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testEmptyPrefixOrSuffixDoesNotInitializeStreamedAggregate(callable $method): void
    {
        // Given
        $data = new Fixture\CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $direct = $method($data, new \EmptyIterator());
        $streamed = $method(Stream::of($data), new \EmptyIterator());
        $streamedAgainstStream = $method(Stream::of($data), Stream::of(new \EmptyIterator()));
        $againstStreamedGenerator = $method($data, Stream::of(Fixture\GeneratorFixture::getGenerator([])));

        // Then
        $this->assertTrue($direct);
        $this->assertTrue($streamed);
        $this->assertTrue($streamedAgainstStream);
        $this->assertTrue($againstStreamedGenerator);
        $this->assertSame(0, $data->getIteratorCallCount());
    }

    /**
     * @test         an aggregate source behind a Stream has its iterator obtained once, only to be compared
     * @dataProvider dataProviderForMethodsWithMatchingPattern
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     * @param        array<int>                                       $pattern
     */
    public function testProbeDoesNotObtainStreamedAggregateIterator(callable $method, array $pattern): void
    {
        // Given
        $data = new Fixture\CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $result = $method(Stream::of($data), new \ArrayIterator($pattern));

        // Then
        $this->assertTrue($result);
        $this->assertSame(1, $data->getIteratorCallCount());
    }

    /**
     * @test         startsWith of an advanced generator and a distinct prefix compares from its current position
     * @dataProvider dataProviderForStartsWithMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testDistinctAdvancedGeneratorSourceStartsWithFromCurrentPosition(callable $method): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $generator->next();

        // When
        $result = $method($generator, [2]);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         endsWith of an advanced generator and a distinct suffix throws PHP's rewind failure
     * @dataProvider dataProviderForEndsWithMethods
     * @param        callable(iterable<mixed>, iterable<mixed>): bool $method
     */
    public function testDistinctAdvancedGeneratorSourceEndsWithThrows(callable $method): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $generator->next();

        // When
        $caught = null;
        try {
            $method($generator, [3]);
        } catch (\Exception $e) {
            $caught = $e;
        }

        // Then
        $this->assertInstanceOf(\Exception::class, $caught);
        $this->assertSame(\Exception::class, \get_class($caught));
        $this->assertSame('Cannot rewind a generator that was already run', $caught->getMessage());
    }

    public static function dataProviderForMethods(): array
    {
        return [
            'startsWith' => [
                static fn (iterable $data, iterable $other): bool => Summary::startsWith($data, $other),
            ],
            'startsWithCoercive' => [
                static fn (iterable $data, iterable $other): bool => Summary::startsWithCoercive($data, $other),
            ],
            'endsWith' => [
                static fn (iterable $data, iterable $other): bool => Summary::endsWith($data, $other),
            ],
            'endsWithCoercive' => [
                static fn (iterable $data, iterable $other): bool => Summary::endsWithCoercive($data, $other),
            ],
        ];
    }

    public static function dataProviderForMethodsWithMatchingPattern(): array
    {
        return [
            'startsWith' => [
                static fn (iterable $data, iterable $other): bool => Summary::startsWith($data, $other),
                [1],
            ],
            'startsWithCoercive' => [
                static fn (iterable $data, iterable $other): bool => Summary::startsWithCoercive($data, $other),
                [1],
            ],
            'endsWith' => [
                static fn (iterable $data, iterable $other): bool => Summary::endsWith($data, $other),
                [3],
            ],
            'endsWithCoercive' => [
                static fn (iterable $data, iterable $other): bool => Summary::endsWithCoercive($data, $other),
                [3],
            ],
        ];
    }

    public static function dataProviderForStartsWithMethods(): array
    {
        return [
            'startsWith' => [
                static fn (iterable $data, iterable $other): bool => Summary::startsWith($data, $other),
            ],
            'startsWithCoercive' => [
                static fn (iterable $data, iterable $other): bool => Summary::startsWithCoercive($data, $other),
            ],
        ];
    }

    public static function dataProviderForEndsWithMethods(): array
    {
        return [
            'endsWith' => [
                static fn (iterable $data, iterable $other): bool => Summary::endsWith($data, $other),
            ],
            'endsWithCoercive' => [
                static fn (iterable $data, iterable $other): bool => Summary::endsWithCoercive($data, $other),
            ],
        ];
    }
}
