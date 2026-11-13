<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamAccumulateNamedArgumentsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test Stream::accumulate with a named data argument throws InvalidArgumentException (array)
     */
    public function testNamedDataThrowsArray(): void
    {
        // Given
        $stream = Stream::of([1, 2, 3])
            ->accumulate(fn ($a, $b) => $a + $b, data: 100);

        // When
        try {
            $stream->toArray();
            $this->fail('Expected an InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('accumulate has no parameter named $data', $e->getMessage());
        }
    }

    /**
     * @test Stream::accumulate with a named data argument throws InvalidArgumentException (Generator)
     */
    public function testNamedDataThrowsGenerator(): void
    {
        // Given
        $stream = Stream::of(GeneratorFixture::getGenerator([1, 2, 3]))
            ->accumulate(fn ($a, $b) => $a + $b, data: 100);

        // When
        try {
            $stream->toArray();
            $this->fail('Expected an InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('accumulate has no parameter named $data', $e->getMessage());
        }
    }

    /**
     * @test Stream::accumulate with a named data argument throws InvalidArgumentException (Iterator)
     */
    public function testNamedDataThrowsIterator(): void
    {
        // Given
        $stream = Stream::of(new ArrayIteratorFixture([1, 2, 3]))
            ->accumulate(fn ($a, $b) => $a + $b, data: 100);

        // When
        try {
            $stream->toArray();
            $this->fail('Expected an InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('accumulate has no parameter named $data', $e->getMessage());
        }
    }

    /**
     * @test Stream::accumulate with a named data argument throws InvalidArgumentException (IteratorAggregate)
     */
    public function testNamedDataThrowsIteratorAggregate(): void
    {
        // Given
        $stream = Stream::of(new IteratorAggregateFixture([1, 2, 3]))
            ->accumulate(fn ($a, $b) => $a + $b, data: 100);

        // When
        try {
            $stream->toArray();
            $this->fail('Expected an InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('accumulate has no parameter named $data', $e->getMessage());
        }
    }

    /**
     * @test Stream::accumulate with an unknown named argument throws InvalidArgumentException (array)
     */
    public function testUnknownNameThrowsArray(): void
    {
        // Given
        $stream = Stream::of([1, 2, 3])
            ->accumulate(fn ($a, $b) => $a + $b, foo: 100);

        // When
        try {
            $stream->toArray();
            $this->fail('Expected an InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('accumulate has no parameter named $foo', $e->getMessage());
        }
    }

    /**
     * @test Stream::accumulate with an unknown named argument throws InvalidArgumentException (Generator)
     */
    public function testUnknownNameThrowsGenerator(): void
    {
        // Given
        $stream = Stream::of(GeneratorFixture::getGenerator([1, 2, 3]))
            ->accumulate(fn ($a, $b) => $a + $b, foo: 100);

        // When
        try {
            $stream->toArray();
            $this->fail('Expected an InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('accumulate has no parameter named $foo', $e->getMessage());
        }
    }

    /**
     * @test Stream::accumulate with an unknown named argument throws InvalidArgumentException (Iterator)
     */
    public function testUnknownNameThrowsIterator(): void
    {
        // Given
        $stream = Stream::of(new ArrayIteratorFixture([1, 2, 3]))
            ->accumulate(fn ($a, $b) => $a + $b, foo: 100);

        // When
        try {
            $stream->toArray();
            $this->fail('Expected an InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('accumulate has no parameter named $foo', $e->getMessage());
        }
    }

    /**
     * @test Stream::accumulate with an unknown named argument throws InvalidArgumentException (IteratorAggregate)
     */
    public function testUnknownNameThrowsIteratorAggregate(): void
    {
        // Given
        $stream = Stream::of(new IteratorAggregateFixture([1, 2, 3]))
            ->accumulate(fn ($a, $b) => $a + $b, foo: 100);

        // When
        try {
            $stream->toArray();
            $this->fail('Expected an InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('accumulate has no parameter named $foo', $e->getMessage());
        }
    }

    /**
     * More than one initial value, one of them named other than initial: Single::accumulate() reports the count
     * first, and so does Stream::accumulate().
     *
     * @return array<string, array{array<mixed>}>
     */
    public static function dataProviderForTooManyInitialWithUnknownName(): array
    {
        return [
            'named initial and unknown name' => [['initial' => 10, 'foo' => 20]],
            'positional and unknown name'    => [[10, 'foo' => 20]],
            'named data and named initial'   => [['data' => 10, 'initial' => 20]],
        ];
    }

    /**
     * @test         Stream::accumulate with more than one initial value, one misnamed, reports the count (array)
     * @dataProvider dataProviderForTooManyInitialWithUnknownName
     * @param        array<mixed> $initial
     */
    public function testTooManyInitialWithUnknownNameThrowsArray(array $initial): void
    {
        // Given
        $stream = Stream::of([1, 2, 3])
            ->accumulate(fn ($a, $b) => $a + $b, ...$initial);

        // When
        try {
            $stream->toArray();
            $this->fail('Expected an InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('accumulate expects at most one initial value, got 2', $e->getMessage());
        }
    }

    /**
     * @test         Stream::accumulate with more than one initial value, one misnamed, reports the count (Generator)
     * @dataProvider dataProviderForTooManyInitialWithUnknownName
     * @param        array<mixed> $initial
     */
    public function testTooManyInitialWithUnknownNameThrowsGenerator(array $initial): void
    {
        // Given
        $stream = Stream::of(GeneratorFixture::getGenerator([1, 2, 3]))
            ->accumulate(fn ($a, $b) => $a + $b, ...$initial);

        // When
        try {
            $stream->toArray();
            $this->fail('Expected an InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('accumulate expects at most one initial value, got 2', $e->getMessage());
        }
    }

    /**
     * @test         Stream::accumulate with more than one initial value, one misnamed, reports the count (Iterator)
     * @dataProvider dataProviderForTooManyInitialWithUnknownName
     * @param        array<mixed> $initial
     */
    public function testTooManyInitialWithUnknownNameThrowsIterator(array $initial): void
    {
        // Given
        $stream = Stream::of(new ArrayIteratorFixture([1, 2, 3]))
            ->accumulate(fn ($a, $b) => $a + $b, ...$initial);

        // When
        try {
            $stream->toArray();
            $this->fail('Expected an InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('accumulate expects at most one initial value, got 2', $e->getMessage());
        }
    }

    /**
     * @test         Stream::accumulate with more than one initial value, one misnamed, reports the count
     *               (IteratorAggregate)
     * @dataProvider dataProviderForTooManyInitialWithUnknownName
     * @param        array<mixed> $initial
     */
    public function testTooManyInitialWithUnknownNameThrowsIteratorAggregate(array $initial): void
    {
        // Given
        $stream = Stream::of(new IteratorAggregateFixture([1, 2, 3]))
            ->accumulate(fn ($a, $b) => $a + $b, ...$initial);

        // When
        try {
            $stream->toArray();
            $this->fail('Expected an InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('accumulate expects at most one initial value, got 2', $e->getMessage());
        }
    }

    /**
     * @test Stream::accumulate with a named initial value starts from it (array)
     */
    public function testNamedInitialArray(): void
    {
        // When
        $result = Stream::of([1, 2])
            ->accumulate(fn ($a, $b) => $a + $b, initial: 10)
            ->toArray();

        // Then
        $this->assertSame([10, 11, 13], $result);
    }

    /**
     * @test Stream::accumulate with a named initial value starts from it (Generator)
     */
    public function testNamedInitialGenerator(): void
    {
        // When
        $result = Stream::of(GeneratorFixture::getGenerator([1, 2]))
            ->accumulate(fn ($a, $b) => $a + $b, initial: 10)
            ->toArray();

        // Then
        $this->assertSame([10, 11, 13], $result);
    }

    /**
     * @test Stream::accumulate with a named initial value starts from it (Iterator)
     */
    public function testNamedInitialIterator(): void
    {
        // When
        $result = Stream::of(new ArrayIteratorFixture([1, 2]))
            ->accumulate(fn ($a, $b) => $a + $b, initial: 10)
            ->toArray();

        // Then
        $this->assertSame([10, 11, 13], $result);
    }

    /**
     * @test Stream::accumulate with a named initial value starts from it (IteratorAggregate)
     */
    public function testNamedInitialIteratorAggregate(): void
    {
        // When
        $result = Stream::of(new IteratorAggregateFixture([1, 2]))
            ->accumulate(fn ($a, $b) => $a + $b, initial: 10)
            ->toArray();

        // Then
        $this->assertSame([10, 11, 13], $result);
    }

    /**
     * @test Stream::accumulate with a positional initial value starts from it (array)
     */
    public function testPositionalInitialArray(): void
    {
        // When
        $result = Stream::of([1, 2])
            ->accumulate(fn ($a, $b) => $a + $b, 10)
            ->toArray();

        // Then
        $this->assertSame([10, 11, 13], $result);
    }

    /**
     * @test Stream::accumulate with a positional initial value starts from it (Generator)
     */
    public function testPositionalInitialGenerator(): void
    {
        // When
        $result = Stream::of(GeneratorFixture::getGenerator([1, 2]))
            ->accumulate(fn ($a, $b) => $a + $b, 10)
            ->toArray();

        // Then
        $this->assertSame([10, 11, 13], $result);
    }

    /**
     * @test Stream::accumulate with a positional initial value starts from it (Iterator)
     */
    public function testPositionalInitialIterator(): void
    {
        // When
        $result = Stream::of(new ArrayIteratorFixture([1, 2]))
            ->accumulate(fn ($a, $b) => $a + $b, 10)
            ->toArray();

        // Then
        $this->assertSame([10, 11, 13], $result);
    }

    /**
     * @test Stream::accumulate with a positional initial value starts from it (IteratorAggregate)
     */
    public function testPositionalInitialIteratorAggregate(): void
    {
        // When
        $result = Stream::of(new IteratorAggregateFixture([1, 2]))
            ->accumulate(fn ($a, $b) => $a + $b, 10)
            ->toArray();

        // Then
        $this->assertSame([10, 11, 13], $result);
    }
}
