<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Stream;
use IterTools\Summary;
use IterTools\Tests\Fixture\CountingIteratorAggregateFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\NonSerializableFixture;
use IterTools\Tests\Fixture\WrappingIteratorAggregateFixture;

/**
 * same, arePermutations and arePermutationsCoercive given one object more than once.
 */
class SelfComparisonTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         a stream over an array holding NAN, passed twice, is the same as itself
     * @dataProvider dataProviderForComparisons
     * @param        string $comparison
     */
    public function testNanStreamPassedTwice(string $comparison): void
    {
        // Given
        $stream = Stream::of([1, \NAN, 3]);

        // When
        $result = Summary::$comparison($stream, $stream);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         a stream of objects that cannot be serialized, passed twice, is the same as itself
     * @dataProvider dataProviderForComparisons
     * @param        string $comparison
     */
    public function testNonSerializableStreamPassedTwice(string $comparison): void
    {
        // Given
        $stream = Stream::of([new NonSerializableFixture(1), new NonSerializableFixture(2)]);

        // When
        $result = Summary::$comparison($stream, $stream);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         an advanced generator passed twice is the same as itself and is not read
     * @dataProvider dataProviderForComparisons
     * @param        string $comparison
     */
    public function testAdvancedGeneratorPassedTwice(string $comparison): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2, 3]);
        $generator->next();

        // When
        $result = Summary::$comparison($generator, $generator);

        // Then
        $this->assertTrue($result);
        $this->assertSame(2, $generator->current());
    }

    /**
     * @test         an IteratorAggregate passed twice is the same as itself without a getIterator() call
     * @dataProvider dataProviderForComparisons
     * @param        string $comparison
     */
    public function testAggregatePassedTwiceIsNotResolved(string $comparison): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $result = Summary::$comparison($aggregate, $aggregate, $aggregate);

        // Then
        $this->assertTrue($result);
        $this->assertSame(0, $aggregate->getIteratorCallCount());
    }

    /**
     * @test         an IteratorAggregate whose getIterator() throws, passed twice, is the same as itself
     * @dataProvider dataProviderForComparisons
     * @param        string $comparison
     */
    public function testThrowingAggregatePassedTwice(string $comparison): void
    {
        // Given
        $aggregate = new class () implements \IteratorAggregate {
            public function getIterator(): \Iterator
            {
                throw new \RuntimeException('getIterator must not be called');
            }
        };

        // When
        $result = Summary::$comparison($aggregate, $aggregate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         a generator whose body throws, passed twice, is the same as itself and does not run
     * @dataProvider dataProviderForComparisons
     * @param        string $comparison
     */
    public function testThrowingGeneratorPassedTwiceDoesNotRun(string $comparison): void
    {
        // Given
        $ran       = false;
        $generator = (static function () use (&$ran): \Generator {
            $ran = true;
            throw new \RuntimeException('boom');
            yield 1; // unreachable
        })();

        // When
        $result = Summary::$comparison($generator, $generator);

        // Then
        $this->assertTrue($result);
        $this->assertFalse($ran);
    }

    /**
     * @test         distinct aggregates returning one generator compare it with itself without running it
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
        $result = Summary::$comparison($first, $second);

        // Then
        $this->assertTrue($result);
        $this->assertFalse($ran);
    }

    /**
     * @test an array passed twice to same is still compared by value: NAN is not the same as NAN
     */
    public function testArrayPassedTwiceIsComparedByValue(): void
    {
        // Given
        $array = [1, \NAN];

        // When
        $result = Summary::same($array, $array);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForComparisons(): array
    {
        return [
            'same'                    => ['same'],
            'arePermutations'         => ['arePermutations'],
            'arePermutationsCoercive' => ['arePermutationsCoercive'],
        ];
    }
}
