<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;
use IterTools\Tests\Fixture\RecordingIteratorAggregateFixture;
use IterTools\Tests\Fixture\RecordingIteratorFixture;

class StreamSummaryTier3Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @test atLeastN delegates to Summary::atLeastN (array)
     */
    public function testAtLeastNArray(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4, 5])->atLeastN(3);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test atLeastN delegates to Summary::atLeastN (Generator)
     */
    public function testAtLeastNGenerator(): void
    {
        // Given
        $data = GeneratorFixture::getGenerator([1, 2, 3, 4, 5]);

        // When
        $result = Stream::of($data)->atLeastN(3);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test atLeastN delegates to Summary::atLeastN (Iterator)
     */
    public function testAtLeastNIterator(): void
    {
        // Given
        $data = new ArrayIteratorFixture([1, 2, 3, 4, 5]);

        // When
        $result = Stream::of($data)->atLeastN(3);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test atLeastN delegates to Summary::atLeastN (IteratorAggregate)
     */
    public function testAtLeastNIteratorAggregate(): void
    {
        // Given
        $data = new IteratorAggregateFixture([1, 2, 3, 4, 5]);

        // When
        $result = Stream::of($data)->atLeastN(3);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test atLeastN with predicate returns false when count is below n
     */
    public function testAtLeastNWithPredicateFalse(): void
    {
        // When
        $result = Stream::of([1, 2, 3])->atLeastN(2, fn (int $x): bool => $x > 5);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test atMostN delegates to Summary::atMostN (array)
     */
    public function testAtMostNArray(): void
    {
        // When
        $result = Stream::of([1, 2, 3])->atMostN(3);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test atMostN delegates to Summary::atMostN (Generator)
     */
    public function testAtMostNGenerator(): void
    {
        // Given
        $data = GeneratorFixture::getGenerator([1, 2, 3]);

        // When
        $result = Stream::of($data)->atMostN(3);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test atMostN delegates to Summary::atMostN (Iterator)
     */
    public function testAtMostNIterator(): void
    {
        // Given
        $data = new ArrayIteratorFixture([1, 2, 3]);

        // When
        $result = Stream::of($data)->atMostN(3);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test atMostN delegates to Summary::atMostN (IteratorAggregate)
     */
    public function testAtMostNIteratorAggregate(): void
    {
        // Given
        $data = new IteratorAggregateFixture([1, 2, 3]);

        // When
        $result = Stream::of($data)->atMostN(3);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test atMostN with predicate returns false when count exceeds n
     */
    public function testAtMostNWithPredicateFalse(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4, 5])->atMostN(2, fn (int $x): bool => $x > 0);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test startsWith delegates to Summary::startsWith (array)
     */
    public function testStartsWithArray(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4, 5])->startsWith([1, 2]);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWith delegates to Summary::startsWith (Generator)
     */
    public function testStartsWithGenerator(): void
    {
        // Given
        $data = GeneratorFixture::getGenerator([1, 2, 3, 4, 5]);

        // When
        $result = Stream::of($data)->startsWith([1, 2]);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWith delegates to Summary::startsWith (Iterator)
     */
    public function testStartsWithIterator(): void
    {
        // Given
        $data = new ArrayIteratorFixture([1, 2, 3, 4, 5]);

        // When
        $result = Stream::of($data)->startsWith([1, 2]);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWith delegates to Summary::startsWith (IteratorAggregate)
     */
    public function testStartsWithIteratorAggregate(): void
    {
        // Given
        $data = new IteratorAggregateFixture([1, 2, 3, 4, 5]);

        // When
        $result = Stream::of($data)->startsWith([1, 2]);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWith returns false when prefix does not match
     */
    public function testStartsWithMismatch(): void
    {
        // When
        $result = Stream::of([1, 2, 3])->startsWith([2, 3]);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test startsWithCoercive matches via type coercion
     */
    public function testStartsWithCoercive(): void
    {
        // When
        $result = Stream::of([1, 2, 3])->startsWithCoercive(['1', '2']);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWith strict differs from coercive
     */
    public function testStartsWithStrictDiffersFromCoercive(): void
    {
        // When
        $strict = Stream::of([1, 2, 3])->startsWith(['1', '2']);
        $coercive = Stream::of([1, 2, 3])->startsWithCoercive(['1', '2']);

        // Then
        $this->assertFalse($strict);
        $this->assertTrue($coercive);
    }

    /**
     * @test endsWith delegates to Summary::endsWith (array)
     */
    public function testEndsWithArray(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4, 5])->endsWith([4, 5]);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWith delegates to Summary::endsWith (Generator)
     */
    public function testEndsWithGenerator(): void
    {
        // Given
        $data = GeneratorFixture::getGenerator([1, 2, 3, 4, 5]);

        // When
        $result = Stream::of($data)->endsWith([4, 5]);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWith delegates to Summary::endsWith (Iterator)
     */
    public function testEndsWithIterator(): void
    {
        // Given
        $data = new ArrayIteratorFixture([1, 2, 3, 4, 5]);

        // When
        $result = Stream::of($data)->endsWith([4, 5]);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWith delegates to Summary::endsWith (IteratorAggregate)
     */
    public function testEndsWithIteratorAggregate(): void
    {
        // Given
        $data = new IteratorAggregateFixture([1, 2, 3, 4, 5]);

        // When
        $result = Stream::of($data)->endsWith([4, 5]);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWith returns false when suffix does not match
     */
    public function testEndsWithMismatch(): void
    {
        // When
        $result = Stream::of([1, 2, 3])->endsWith([2]);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test endsWithCoercive matches via type coercion
     */
    public function testEndsWithCoercive(): void
    {
        // When
        $result = Stream::of([1, 2, 3])->endsWithCoercive(['2', '3']);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWith strict differs from coercive
     */
    public function testEndsWithStrictDiffersFromCoercive(): void
    {
        // When
        $strict = Stream::of([1, 2, 3])->endsWith(['2', '3']);
        $coercive = Stream::of([1, 2, 3])->endsWithCoercive(['2', '3']);

        // Then
        $this->assertFalse($strict);
        $this->assertTrue($coercive);
    }

    /**
     * @test startsWith - does not advance a generator stream past the matched prefix
     */
    public function testStartsWithDoesNotAdvancePastPrefixGenerator(): void
    {
        // Given
        $data = (static function (): \Generator {
            yield 1;
            yield 2;
            throw new \LogicException('Stream advanced past the prefix');
        })();

        // When
        $result = Stream::of($data)->startsWith([1, 2]);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWith - does not advance an iterator stream past the matched prefix
     */
    public function testStartsWithDoesNotAdvancePastPrefixIterator(): void
    {
        // Given
        $pulled = [];
        $data = new RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = Stream::of($data)->startsWith([1, 2]);

        // Then
        $this->assertTrue($result);
        $this->assertSame([1, 2], $pulled);
    }

    /**
     * @test startsWith - does not advance an IteratorAggregate stream past the matched prefix
     */
    public function testStartsWithDoesNotAdvancePastPrefixIteratorAggregate(): void
    {
        // Given
        $pulled = [];
        $data = new RecordingIteratorAggregateFixture([1, 2, 3], $pulled);

        // When
        $result = Stream::of($data)->startsWith([1, 2]);

        // Then
        $this->assertTrue($result);
        $this->assertSame([1, 2], $pulled);
    }

    /**
     * @test startsWithCoercive - does not advance a generator stream past the matched prefix
     */
    public function testStartsWithCoerciveDoesNotAdvancePastPrefixGenerator(): void
    {
        // Given
        $data = (static function (): \Generator {
            yield 1;
            yield 2;
            throw new \LogicException('Stream advanced past the prefix');
        })();

        // When
        $result = Stream::of($data)->startsWithCoercive(['1', '2']);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWithCoercive - does not advance an iterator stream past the matched prefix
     */
    public function testStartsWithCoerciveDoesNotAdvancePastPrefixIterator(): void
    {
        // Given
        $pulled = [];
        $data = new RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = Stream::of($data)->startsWithCoercive(['1', '2']);

        // Then
        $this->assertTrue($result);
        $this->assertSame([1, 2], $pulled);
    }

    /**
     * @test startsWithCoercive - does not advance an IteratorAggregate stream past the matched prefix
     */
    public function testStartsWithCoerciveDoesNotAdvancePastPrefixIteratorAggregate(): void
    {
        // Given
        $pulled = [];
        $data = new RecordingIteratorAggregateFixture([1, 2, 3], $pulled);

        // When
        $result = Stream::of($data)->startsWithCoercive(['1', '2']);

        // Then
        $this->assertTrue($result);
        $this->assertSame([1, 2], $pulled);
    }

    /**
     * @test sameWith - does not read an iterator stream compared with no collections
     */
    public function testSameWithNoCollectionsDoesNotReadIterator(): void
    {
        // Given
        $pulled = [];
        $data = new RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = Stream::of($data)->sameWith();

        // Then
        $this->assertTrue($result);
        $this->assertSame([], $pulled);
    }

    /**
     * @test sameWith - does not read an IteratorAggregate stream compared with no collections
     */
    public function testSameWithNoCollectionsDoesNotReadIteratorAggregate(): void
    {
        // Given
        $pulled = [];
        $data = new RecordingIteratorAggregateFixture([1, 2, 3], $pulled);

        // When
        $result = Stream::of($data)->sameWith();

        // Then
        $this->assertTrue($result);
        $this->assertSame([], $pulled);
    }

    /**
     * @test sameWith - does not read an iterator stream compared with its own source
     */
    public function testSameWithOwnSourceDoesNotReadIterator(): void
    {
        // Given
        $pulled = [];
        $data = new RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = Stream::of($data)->sameWith($data);

        // Then
        $this->assertTrue($result);
        $this->assertSame([], $pulled);
    }

    /**
     * @test startsWith - does not read an iterator stream compared with its own source
     */
    public function testStartsWithOwnSourceDoesNotReadIterator(): void
    {
        // Given
        $pulled = [];
        $data = new RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = Stream::of($data)->startsWith($data);

        // Then
        $this->assertTrue($result);
        $this->assertSame([], $pulled);
    }

    /**
     * @test startsWith - a generator stream compared with its own source is true
     */
    public function testStartsWithOwnSourceGenerator(): void
    {
        // Given
        $data = GeneratorFixture::getGenerator([1, 2, 3]);

        // When
        $result = Stream::of($data)->startsWith($data);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWithCoercive - does not read an iterator stream compared with its own source
     */
    public function testStartsWithCoerciveOwnSourceDoesNotReadIterator(): void
    {
        // Given
        $pulled = [];
        $data = new RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = Stream::of($data)->startsWithCoercive($data);

        // Then
        $this->assertTrue($result);
        $this->assertSame([], $pulled);
    }

    /**
     * @test startsWithCoercive - a generator stream compared with its own source is true
     */
    public function testStartsWithCoerciveOwnSourceGenerator(): void
    {
        // Given
        $data = GeneratorFixture::getGenerator([1, 2, 3]);

        // When
        $result = Stream::of($data)->startsWithCoercive($data);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWith - does not read an iterator stream compared with its own source
     */
    public function testEndsWithOwnSourceDoesNotReadIterator(): void
    {
        // Given
        $pulled = [];
        $data = new RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = Stream::of($data)->endsWith($data);

        // Then
        $this->assertTrue($result);
        $this->assertSame([], $pulled);
    }

    /**
     * @test endsWith - a generator stream compared with its own source is true
     */
    public function testEndsWithOwnSourceGenerator(): void
    {
        // Given
        $data = GeneratorFixture::getGenerator([1, 2, 3]);

        // When
        $result = Stream::of($data)->endsWith($data);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - does not read an iterator stream compared with its own source
     */
    public function testEndsWithCoerciveOwnSourceDoesNotReadIterator(): void
    {
        // Given
        $pulled = [];
        $data = new RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = Stream::of($data)->endsWithCoercive($data);

        // Then
        $this->assertTrue($result);
        $this->assertSame([], $pulled);
    }

    /**
     * @test endsWithCoercive - a generator stream compared with its own source is true
     */
    public function testEndsWithCoerciveOwnSourceGenerator(): void
    {
        // Given
        $data = GeneratorFixture::getGenerator([1, 2, 3]);

        // When
        $result = Stream::of($data)->endsWithCoercive($data);

        // Then
        $this->assertTrue($result);
    }
}
