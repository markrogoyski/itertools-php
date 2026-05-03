<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamReduceTier3Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @test toLastMatch delegates to Reduce::toLastMatch (array)
     */
    public function testToLastMatchArray(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5];

        // When
        $result = Stream::of($data)->toLastMatch(fn (int $n): bool => $n % 2 === 0);

        // Then
        $this->assertSame(4, $result);
    }

    /**
     * @test toLastMatch delegates to Reduce::toLastMatch (Generator)
     */
    public function testToLastMatchGenerator(): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator([1, 2, 3, 4, 5]);

        // When
        $result = Stream::of($iterable)->toLastMatch(fn (int $n): bool => $n % 2 === 0);

        // Then
        $this->assertSame(4, $result);
    }

    /**
     * @test toLastMatch delegates to Reduce::toLastMatch (Iterator)
     */
    public function testToLastMatchIterator(): void
    {
        // Given
        $iterable = new ArrayIteratorFixture([1, 2, 3, 4, 5]);

        // When
        $result = Stream::of($iterable)->toLastMatch(fn (int $n): bool => $n % 2 === 0);

        // Then
        $this->assertSame(4, $result);
    }

    /**
     * @test toLastMatch delegates to Reduce::toLastMatch (IteratorAggregate)
     */
    public function testToLastMatchIteratorAggregate(): void
    {
        // Given
        $iterable = new IteratorAggregateFixture([1, 2, 3, 4, 5]);

        // When
        $result = Stream::of($iterable)->toLastMatch(fn (int $n): bool => $n % 2 === 0);

        // Then
        $this->assertSame(4, $result);
    }

    /**
     * @test toLastMatch returns custom default when no match
     */
    public function testToLastMatchDefault(): void
    {
        // When
        $result = Stream::of([1, 3, 5])->toLastMatch(fn (int $n): bool => $n % 2 === 0, -1);

        // Then
        $this->assertSame(-1, $result);
    }

    /**
     * @test toLastMatch composes with upstream operations
     */
    public function testToLastMatchAfterMap(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4, 5])
            ->map(fn (int $n): int => $n * 10)
            ->toLastMatch(fn (int $n): bool => $n > 20);

        // Then
        $this->assertSame(50, $result);
    }

    /**
     * @test toLastMatchIndex delegates to Reduce::toLastMatchIndex (array)
     */
    public function testToLastMatchIndexArray(): void
    {
        // When
        $result = Stream::of([10, 20, 30, 40])->toLastMatchIndex(fn (int $n): bool => $n > 15);

        // Then
        $this->assertSame(3, $result);
    }

    /**
     * @test toLastMatchIndex delegates to Reduce::toLastMatchIndex (Generator)
     */
    public function testToLastMatchIndexGenerator(): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator([10, 20, 30, 40]);

        // When
        $result = Stream::of($iterable)->toLastMatchIndex(fn (int $n): bool => $n > 15);

        // Then
        $this->assertSame(3, $result);
    }

    /**
     * @test toLastMatchIndex delegates to Reduce::toLastMatchIndex (Iterator)
     */
    public function testToLastMatchIndexIterator(): void
    {
        // Given
        $iterable = new ArrayIteratorFixture([10, 20, 30, 40]);

        // When
        $result = Stream::of($iterable)->toLastMatchIndex(fn (int $n): bool => $n > 15);

        // Then
        $this->assertSame(3, $result);
    }

    /**
     * @test toLastMatchIndex delegates to Reduce::toLastMatchIndex (IteratorAggregate)
     */
    public function testToLastMatchIndexIteratorAggregate(): void
    {
        // Given
        $iterable = new IteratorAggregateFixture([10, 20, 30, 40]);

        // When
        $result = Stream::of($iterable)->toLastMatchIndex(fn (int $n): bool => $n > 15);

        // Then
        $this->assertSame(3, $result);
    }

    /**
     * @test toLastMatchIndex returns default when no match
     */
    public function testToLastMatchIndexDefault(): void
    {
        // When
        $result = Stream::of([1, 3, 5])->toLastMatchIndex(fn (int $n): bool => $n % 2 === 0, -1);

        // Then
        $this->assertSame(-1, $result);
    }

    /**
     * @test toLastMatchKey delegates to Reduce::toLastMatchKey (array)
     */
    public function testToLastMatchKeyArray(): void
    {
        // When
        $result = Stream::of(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 0])
            ->toLastMatchKey(fn (int $n): bool => $n > 0);

        // Then
        $this->assertSame('c', $result);
    }

    /**
     * @test toLastMatchKey delegates to Reduce::toLastMatchKey (Generator)
     */
    public function testToLastMatchKeyGenerator(): void
    {
        // Given
        $iterable = GeneratorFixture::getKeyValueGenerator(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 0]);

        // When
        $result = Stream::of($iterable)->toLastMatchKey(fn (int $n): bool => $n > 0);

        // Then
        $this->assertSame('c', $result);
    }

    /**
     * @test toLastMatchKey delegates to Reduce::toLastMatchKey (IteratorAggregate)
     */
    public function testToLastMatchKeyIteratorAggregate(): void
    {
        // Given
        $iterable = new IteratorAggregateFixture(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 0]);

        // When
        $result = Stream::of($iterable)->toLastMatchKey(fn (int $n): bool => $n > 0);

        // Then
        $this->assertSame('c', $result);
    }

    /**
     * @test toLastMatchKey returns default when no match
     */
    public function testToLastMatchKeyDefault(): void
    {
        // When
        $result = Stream::of(['a' => 1, 'b' => 2])
            ->toLastMatchKey(fn (int $n): bool => $n > 100, 'missing');

        // Then
        $this->assertSame('missing', $result);
    }

    /**
     * @test toOnly delegates to Reduce::toOnly (array)
     */
    public function testToOnlyArray(): void
    {
        // When
        $result = Stream::of([42])->toOnly();

        // Then
        $this->assertSame(42, $result);
    }

    /**
     * @test toOnly delegates to Reduce::toOnly (Generator)
     */
    public function testToOnlyGenerator(): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator(['only']);

        // When
        $result = Stream::of($iterable)->toOnly();

        // Then
        $this->assertSame('only', $result);
    }

    /**
     * @test toOnly delegates to Reduce::toOnly (Iterator)
     */
    public function testToOnlyIterator(): void
    {
        // Given
        $iterable = new ArrayIteratorFixture(['only']);

        // When
        $result = Stream::of($iterable)->toOnly();

        // Then
        $this->assertSame('only', $result);
    }

    /**
     * @test toOnly delegates to Reduce::toOnly (IteratorAggregate)
     */
    public function testToOnlyIteratorAggregate(): void
    {
        // Given
        $iterable = new IteratorAggregateFixture(['only']);

        // When
        $result = Stream::of($iterable)->toOnly();

        // Then
        $this->assertSame('only', $result);
    }

    /**
     * @test toOnly throws on empty stream
     */
    public function testToOnlyEmpty(): void
    {
        // Then
        $this->expectException(\LengthException::class);

        // When
        Stream::of([])->toOnly();
    }

    /**
     * @test toOnly throws on multi-element stream
     */
    public function testToOnlyMultiple(): void
    {
        // Then
        $this->expectException(\LengthException::class);

        // When
        Stream::of([1, 2, 3])->toOnly();
    }

    /**
     * @test toOnly composes with filter to find a unique element
     */
    public function testToOnlyAfterFilter(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4, 5])
            ->filter(fn (int $n): bool => $n === 3)
            ->toOnly();

        // Then
        $this->assertSame(3, $result);
    }

    /**
     * @test consume drains a stream (array)
     */
    public function testConsumeArray(): void
    {
        // When
        Stream::of([1, 2, 3])->consume();

        // Then
        $this->addToAssertionCount(1);
    }

    /**
     * @test consume drains a stream (Generator)
     */
    public function testConsumeGenerator(): void
    {
        // Given
        $observed  = [];
        $generator = (function () use (&$observed): \Generator {
            foreach ([1, 2, 3] as $n) {
                $observed[] = $n;
                yield $n;
            }
        })();

        // When
        Stream::of($generator)->consume();

        // Then
        $this->assertSame([1, 2, 3], $observed);
    }

    /**
     * @test consume drains a stream (Iterator)
     */
    public function testConsumeIterator(): void
    {
        // Given
        $iterable = new ArrayIteratorFixture([1, 2, 3]);

        // When
        Stream::of($iterable)->consume();

        // Then
        $this->addToAssertionCount(1);
    }

    /**
     * @test consume drains a stream (IteratorAggregate)
     */
    public function testConsumeIteratorAggregate(): void
    {
        // Given
        $iterable = new IteratorAggregateFixture([1, 2, 3]);

        // When
        Stream::of($iterable)->consume();

        // Then
        $this->addToAssertionCount(1);
    }

    /**
     * @test consume forces evaluation of an upstream lazy map
     */
    public function testConsumeForcesLazyMapEvaluation(): void
    {
        // Given
        $observed = [];
        $pipeline = Stream::of([1, 2, 3])
            ->map(function (int $n) use (&$observed): int {
                $observed[] = $n;
                return $n * 2;
            });

        // Then map is lazy — nothing has run yet
        $this->assertSame([], $observed);

        // When
        $pipeline->consume();

        // Then
        $this->assertSame([1, 2, 3], $observed);
    }
}
