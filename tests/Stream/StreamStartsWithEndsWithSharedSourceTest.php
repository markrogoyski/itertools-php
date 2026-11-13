<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture;

/**
 * Stream startsWith, startsWithCoercive, endsWith and endsWithCoercive given one source on both sides, and the
 * exceptions a source or a prefix or suffix throws.
 */
class StreamStartsWithEndsWithSharedSourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         a stream and a stream over the same generator are true without it being read
     * @dataProvider dataProviderForMethods
     * @param        callable(Stream, iterable<mixed>): bool $method
     */
    public function testGeneratorSharedWithStreamIsNotRead(callable $method): void
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
     * @test         a stream over a stream over a generator, compared with the generator, is true without reading it
     * @dataProvider dataProviderForMethods
     * @param        callable(Stream, iterable<mixed>): bool $method
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
     * @test         a stream over a stream over a generator, compared with a stream over it, is true without reading it
     * @dataProvider dataProviderForMethods
     * @param        callable(Stream, iterable<mixed>): bool $method
     */
    public function testGeneratorSharedThroughNestedStreamsIsNotRead(callable $method): void
    {
        // Given
        $started = false;
        $generator = (static function () use (&$started): \Generator {
            $started = true;
            throw new \RuntimeException('boom');
            yield 1; // unreachable
        })();

        // When
        $result = $method(Stream::of(Stream::of($generator)), Stream::of($generator));

        // Then
        $this->assertTrue($result);
        $this->assertFalse($started);
    }

    /**
     * @test         a stream and a stream over the same iterator are true without it being rewound or read
     * @dataProvider dataProviderForMethods
     * @param        callable(Stream, iterable<mixed>): bool $method
     */
    public function testIteratorSharedWithStreamIsNotRead(callable $method): void
    {
        // Given
        $pulled = [];
        $iterator = new Fixture\RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = $method(Stream::of($iterator), Stream::of($iterator));

        // Then
        $this->assertTrue($result);
        $this->assertSame([], $pulled);
    }

    /**
     * @test         a stream and a stream over the same empty generator are true and it can still be traversed
     * @dataProvider dataProviderForMethods
     * @param        callable(Stream, iterable<mixed>): bool $method
     */
    public function testEmptyGeneratorSharedWithStreamStaysTraversable(callable $method): void
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
     * @test         a stream over an aggregate over a throwing generator, compared with it, propagates the exception
     * @dataProvider dataProviderForMethods
     * @param        callable(Stream, iterable<mixed>): bool $method
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
        $method(Stream::of($aggregate), $generator);
    }

    /**
     * @test         a stream over a throwing generator, with an aggregate over it as the prefix or suffix, propagates
     * @dataProvider dataProviderForMethods
     * @param        callable(Stream, iterable<mixed>): bool $method
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
        $method(Stream::of($generator), $aggregate);
    }

    /**
     * @test         a stream over an aggregate over an advanced generator, compared with that generator, is true
     * @dataProvider dataProviderForMethods
     * @param        callable(Stream, iterable<mixed>): bool $method
     */
    public function testOpaqueSharedAdvancedGeneratorIsTrue(callable $method): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $generator->next();
        $aggregate = new Fixture\WrappingIteratorAggregateFixture($generator);

        // When
        $result = $method(Stream::of($aggregate), $generator);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         a stream over an aggregate over a generator, compared with that generator, reads it, then is true
     * @dataProvider dataProviderForMethods
     * @param        callable(Stream, iterable<mixed>): bool $method
     */
    public function testOpaqueSharedGeneratorIsReadThenTrue(callable $method): void
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
        $result = $method(Stream::of($aggregate), $generator);

        // Then
        $this->assertTrue($result);
        $this->assertTrue($started);
    }

    /**
     * @test         a throwing generator as the stream's source or as a distinct prefix or suffix propagates
     * @dataProvider dataProviderForMethods
     * @param        callable(Stream, iterable<mixed>): bool $method
     */
    public function testDistinctThrowingGeneratorPropagates(callable $method): void
    {
        // Given
        $boom = new \RuntimeException('boom');
        $asSource = (static function () use ($boom): \Generator {
            throw $boom;
            yield 1; // unreachable
        })();
        $asPrefixOrSuffix = (static function () use ($boom): \Generator {
            throw $boom;
            yield 1; // unreachable
        })();

        // When
        $caughtAsSource = null;
        try {
            $method(Stream::of($asSource), [1]);
        } catch (\RuntimeException $e) {
            $caughtAsSource = $e;
        }
        $caughtAsPrefixOrSuffix = null;
        try {
            $method(Stream::of([1]), $asPrefixOrSuffix);
        } catch (\RuntimeException $e) {
            $caughtAsPrefixOrSuffix = $e;
        }

        // Then
        $this->assertSame($boom, $caughtAsSource);
        $this->assertSame($boom, $caughtAsPrefixOrSuffix);
        $this->assertSame(\RuntimeException::class, \get_class($caughtAsPrefixOrSuffix));
        $this->assertSame('boom', $caughtAsPrefixOrSuffix->getMessage());
    }

    /**
     * @test         a stream over an aggregate, given an empty prefix or suffix, is true without obtaining its iterator
     * @dataProvider dataProviderForMethods
     * @param        callable(Stream, iterable<mixed>): bool $method
     */
    public function testEmptyPrefixOrSuffixDoesNotInitializeStreamedAggregate(callable $method): void
    {
        // Given
        $data = new Fixture\CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $againstIterator = $method(Stream::of($data), new \EmptyIterator());
        $againstStream = $method(Stream::of($data), Stream::of(new \EmptyIterator()));

        // Then
        $this->assertTrue($againstIterator);
        $this->assertTrue($againstStream);
        $this->assertSame(0, $data->getIteratorCallCount());
    }

    public static function dataProviderForMethods(): array
    {
        return [
            'startsWith' => [
                static fn (Stream $stream, iterable $other): bool => $stream->startsWith($other),
            ],
            'startsWithCoercive' => [
                static fn (Stream $stream, iterable $other): bool => $stream->startsWithCoercive($other),
            ],
            'endsWith' => [
                static fn (Stream $stream, iterable $other): bool => $stream->endsWith($other),
            ],
            'endsWithCoercive' => [
                static fn (Stream $stream, iterable $other): bool => $stream->endsWithCoercive($other),
            ],
        ];
    }
}
