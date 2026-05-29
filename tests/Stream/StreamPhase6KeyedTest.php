<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture;

class StreamPhase6KeyedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test Stream::frequenciesBy fluent example usage
     */
    public function testFrequenciesByExampleUsage(): void
    {
        // Given
        $words = ['apple', 'pear', 'banana', 'kiwi'];

        // When
        $result = Stream::of($words)
            ->frequenciesBy(fn ($word) => \strlen($word))
            ->toAssociativeArray();

        // Then
        $this->assertEquals([5 => 1, 4 => 2, 6 => 1], $result);
    }

    /**
     * @test         Stream::frequenciesBy across iterable types
     * @dataProvider dataProviderForIterableTypes
     * @param        iterable $data
     */
    public function testFrequenciesBy(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->frequenciesBy(fn ($x) => $x % 2 === 0 ? 'even' : 'odd')
            ->toAssociativeArray();

        // Then
        $this->assertEquals(['odd' => 3, 'even' => 3], $result);
    }

    /**
     * @test Stream::frequenciesBy is chainable downstream
     */
    public function testFrequenciesByChainable(): void
    {
        // Given
        $words = ['apple', 'pear', 'banana', 'kiwi'];

        // When
        $result = Stream::of($words)
            ->frequenciesBy(fn ($word) => \strlen($word))
            ->toArray();

        // Then
        $this->assertEquals([1, 2, 1], $result);
    }

    /**
     * @test Stream::frequenciesBy honors strict flag
     */
    public function testFrequenciesByCoercive(): void
    {
        // Given
        $data = [1, '1', 1, '1'];

        // When
        $result = Stream::of($data)
            ->frequenciesBy(fn ($x) => $x, false)
            ->toArray();

        // Then
        $this->assertEquals([4], $result);
    }

    /**
     * @test Stream::relativeFrequenciesBy fluent example usage
     */
    public function testRelativeFrequenciesByExampleUsage(): void
    {
        // Given
        $words = ['apple', 'pear', 'kiwi', 'plum'];

        // When
        $result = Stream::of($words)
            ->relativeFrequenciesBy(fn ($word) => \strlen($word))
            ->toAssociativeArray();

        // Then
        $this->assertEqualsWithDelta([5 => 0.25, 4 => 0.75], $result, 0.0000001);
    }

    /**
     * @test         Stream::relativeFrequenciesBy across iterable types
     * @dataProvider dataProviderForIterableTypes
     * @param        iterable $data
     */
    public function testRelativeFrequenciesBy(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->relativeFrequenciesBy(fn ($x) => $x % 2 === 0 ? 'even' : 'odd')
            ->toAssociativeArray();

        // Then
        $this->assertEqualsWithDelta(['odd' => 0.5, 'even' => 0.5], $result, 0.0000001);
    }

    /**
     * @test         Stream::isSortedBy true across iterable types
     * @dataProvider dataProviderForIterableTypes
     * @param        iterable $data
     */
    public function testIsSortedByTrue(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->isSortedBy(fn ($x) => $x);

        // Then (1..6 ascending)
        $this->assertTrue($result);
    }

    /**
     * @test Stream::isSortedBy false
     */
    public function testIsSortedByFalse(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = Stream::of($data)
            ->isSortedBy(fn ($x) => -$x);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test Stream::isSortedBy example usage
     */
    public function testIsSortedByExampleUsage(): void
    {
        // Given
        $people = [
            (object)['name' => 'Alice', 'age' => 25],
            (object)['name' => 'Bob',   'age' => 30],
            (object)['name' => 'Carol', 'age' => 42],
        ];

        // When
        $result = Stream::of($people)
            ->isSortedBy(fn ($p) => $p->age);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         Stream::isReversedBy true across iterable types
     * @dataProvider dataProviderForReversedIterableTypes
     * @param        iterable $data
     */
    public function testIsReversedByTrue(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->isReversedBy(fn ($x) => $x);

        // Then (6..1 descending)
        $this->assertTrue($result);
    }

    /**
     * @test Stream::isReversedBy false
     */
    public function testIsReversedByFalse(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = Stream::of($data)
            ->isReversedBy(fn ($x) => $x);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         Stream::toCountBy across iterable types
     * @dataProvider dataProviderForIterableTypes
     * @param        iterable $data
     */
    public function testToCountBy(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->toCountBy(fn ($x) => $x % 2 === 0 ? 'even' : 'odd');

        // Then
        $this->assertEquals(['odd' => 3, 'even' => 3], $result);
    }

    /**
     * @test Stream::toCountBy example usage
     */
    public function testToCountByExampleUsage(): void
    {
        // Given
        $words = ['apple', 'pear', 'banana', 'kiwi', 'plum'];

        // When
        $result = Stream::of($words)
            ->toCountBy(fn ($word) => \strlen($word));

        // Then (apple=5, pear=4, banana=6, kiwi=4, plum=4)
        $this->assertEquals([5 => 1, 4 => 3, 6 => 1], $result);
    }

    public static function dataProviderForIterableTypes(): array
    {
        $data = [1, 2, 3, 4, 5, 6];

        return [
            'array'             => [$data],
            'generator'         => [Fixture\GeneratorFixture::getGenerator($data)],
            'iterator'          => [new Fixture\ArrayIteratorFixture($data)],
            'iteratorAggregate' => [new Fixture\IteratorAggregateFixture($data)],
        ];
    }

    public static function dataProviderForReversedIterableTypes(): array
    {
        $data = [6, 5, 4, 3, 2, 1];

        return [
            'array'             => [$data],
            'generator'         => [Fixture\GeneratorFixture::getGenerator($data)],
            'iterator'          => [new Fixture\ArrayIteratorFixture($data)],
            'iteratorAggregate' => [new Fixture\IteratorAggregateFixture($data)],
        ];
    }
}
