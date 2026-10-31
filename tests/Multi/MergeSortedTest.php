<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi;

use IterTools\Multi;
use IterTools\Single;
use IterTools\Stream;
use IterTools\Summary;
use IterTools\Infinite;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\CountingIteratorAggregateFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class MergeSortedTest extends \PHPUnit\Framework\TestCase
{
    public function testHandlesNoSourcesAndEmptySources(): void
    {
        // When
        $none = \iterator_to_array(Multi::mergeSorted());
        $empty = \iterator_to_array(Multi::mergeSorted([], GeneratorFixture::getGenerator([])));

        // Then
        $this->assertSame([], $none);
        $this->assertSame([], $empty);
    }

    public function testCombinesEverySupportedIterableType(): void
    {
        // Given
        $array = [-4, 4];
        $generator = GeneratorFixture::getGenerator([-3, 1.5]);
        $iterator = new ArrayIteratorFixture([-2, 2]);
        $aggregate = new IteratorAggregateFixture([-1, 3]);

        // When
        $result = \iterator_to_array(Multi::mergeSorted($array, $generator, $iterator, $aggregate));

        // Then
        $this->assertSame([-4, -3, -2, -1, 1.5, 2, 3, 4], $result);
    }

    public function testMergesStablyAndDiscardsKeys(): void
    {
        // Given
        $first = ['a' => 1, 'b' => 2, 'c' => 2];
        $second = ['d' => 2, 'e' => 3];

        // When
        $result = \iterator_to_array(Multi::mergeSorted($first, $second));

        // Then
        $this->assertSame([1, 2, 2, 2, 3], $result);
    }

    public function testIsLazyAndSupportsInfiniteInputs(): void
    {
        // Given
        $pulls = 0;
        $source = (static function () use (&$pulls): \Generator {
            ++$pulls;
            yield 0;
            ++$pulls;
            yield 2;
        })();
        $merged = Multi::mergeSorted($source, [1, 3]);

        // When
        $result = \iterator_to_array(Single::limit($merged, 1));

        // Then
        $this->assertSame([0], $result);
        $this->assertSame(1, $pulls);
    }

    public function testYieldsExactPrefixBeforeRejectingLaterNan(): void
    {
        // Given
        $merged = Multi::mergeSorted([1, \NAN], [2]);
        $yielded = [];

        // When
        try {
            foreach ($merged as $value) {
                $yielded[] = $value;
            }
            $this->fail('Expected NAN to be rejected');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('Multi::mergeSorted cannot order NAN', $e->getMessage());
        }

        // Then
        $this->assertSame([1], $yielded);
    }

    public function testYieldsNothingWhenSeededValueIsNan(): void
    {
        // Given
        $merged = Multi::mergeSorted([1], [\NAN]);
        $yielded = [];

        // When
        try {
            foreach ($merged as $value) {
                $yielded[] = $value;
            }
            $this->fail('Expected NAN to be rejected');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('Multi::mergeSorted cannot order NAN', $e->getMessage());
        }

        // Then
        $this->assertSame([], $yielded);
    }

    public function testPreservesSourceOrderWithNamedArguments(): void
    {
        // Given
        $alpha = [new \stdClass(), new \stdClass()];
        $beta = [new \stdClass(), new \stdClass()];
        $gamma = [new \stdClass()];

        // When
        $result = \iterator_to_array(Multi::mergeSorted(alpha: $alpha, beta: $beta, gamma: $gamma));

        // Then
        $this->assertSame([$alpha[0], $alpha[1], $beta[0], $beta[1], $gamma[0]], $result);
    }

    public function testDoesNotTouchIteratorAggregateUntilAdvanced(): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture([1, 2]);

        // When
        $merged = Multi::mergeSorted($aggregate, [3]);

        // Then
        $this->assertSame(0, $aggregate->getIteratorCallCount());

        // When
        $result = \iterator_to_array($merged);

        // Then
        $this->assertSame([1, 2, 3], $result);
        $this->assertSame(1, $aggregate->getIteratorCallCount());
    }

    public function testPropagatesSourceExceptionWithoutWrapping(): void
    {
        // Given
        $exception = new \RuntimeException('source failed');
        $failing = (static function () use ($exception): \Generator {
            yield 1;
            throw $exception;
        })();
        $merged = Multi::mergeSorted($failing, [2]);
        $yielded = [];

        // When
        try {
            foreach ($merged as $value) {
                $yielded[] = $value;
            }
            $this->fail('Expected source exception to propagate');
        } catch (\RuntimeException $e) {
            // Then
            $this->assertSame($exception, $e);
            $this->assertNull($e->getPrevious());
        }

        // Then
        $this->assertSame([1], $yielded);
    }

    public function testMergesInfiniteSources(): void
    {
        // When
        $result = \iterator_to_array(Single::limit(
            Multi::mergeSorted(Infinite::count(0, 2), Infinite::count(1, 2)),
            10,
        ));

        // Then
        $this->assertSame(\range(0, 9), $result);
    }

    /**
     * @test mergeSorted accepts named arguments that match its internal parameter names
     */
    public function testMergeSortedAcceptsNamedArgumentsMatchingInternalParameterNames(): void
    {
        // When
        $result = \iterator_to_array(Multi::mergeSorted(
            keyFunc: [1, 4],
            nanMessage: [2, 3],
        ), false);

        // Then
        $this->assertSame([1, 2, 3, 4], $result);
    }

    /**
     * @test         mergeSorted arrays of ints beyond 2^53 and floats, merged by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array<array<mixed>> $sources
     * @param        array<mixed>        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionArray(array $sources, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = \iterator_to_array(Multi::mergeSorted(...$sources), false);

        // Then
        $this->assertSame($expected, $result);
        $this->assertTrue(Summary::isSorted($result));
    }

    /**
     * @test         mergeSorted generators of ints beyond 2^53 and floats, merged by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array<array<mixed>> $sources
     * @param        array<mixed>        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionGenerator(array $sources, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $generators = \array_map(static fn (array $source) => GeneratorFixture::getGenerator($source), $sources);

        // When
        $result = \iterator_to_array(Multi::mergeSorted(...$generators), false);

        // Then
        $this->assertSame($expected, $result);
        $this->assertTrue(Summary::isSorted($result));
    }

    /**
     * Test data generated with Python, which compares an int and a float by exact value
     * (tools/oracles/exact_order_heaps.py):
     *   list(heapq.merge(*sources))
     * Each int compares equal as a float to a float it is not equal to, so <=> orders them wrongly.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecision(): array
    {
        return [
            'int 2^53 + 1 in the first source, above float 2.0^53 in the second' => [
                [[9007199254740993], [9007199254740992.0]],
                [9007199254740992.0, 9007199254740993],
            ],
            'negative float -2.0^53 in the first source, above int -(2^53 + 1) in the second' => [
                [[-9007199254740992.0], [-9007199254740993]],
                [-9007199254740993, -9007199254740992.0],
            ],
            'interleaved ints and floats around 2^53' => [
                [[9007199254740993, 9007199254740995], [9007199254740992.0, 9007199254740994.0]],
                [9007199254740992.0, 9007199254740993, 9007199254740994.0, 9007199254740995],
            ],
        ];
    }

    /**
     * @test mergeSorted reads an iterator passed twice as independent copies of it
     */
    public function testIteratorPassedTwice(): void
    {
        // Given
        $iterator = new ArrayIteratorFixture([1, 2, 3]);

        // When
        $result = \iterator_to_array(Multi::mergeSorted($iterator, $iterator), false);

        // Then
        $this->assertSame([1, 1, 2, 2, 3, 3], $result);
    }

    /**
     * @test mergeSorted reads a generator passed twice as independent copies of it
     */
    public function testGeneratorPassedTwice(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2, 3, 4]);

        // When
        $result = \iterator_to_array(Multi::mergeSorted($generator, $generator), false);

        // Then
        $this->assertSame([1, 1, 2, 2, 3, 3, 4, 4], $result);
    }

    /**
     * @test mergeSorted reads a generator passed three times as independent copies of it
     */
    public function testGeneratorPassedThreeTimes(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2]);

        // When
        $result = \iterator_to_array(Multi::mergeSorted($generator, $generator, $generator), false);

        // Then
        $this->assertSame([1, 1, 1, 2, 2, 2], $result);
    }

    /**
     * @test mergeSorted reads a stream passed twice as independent copies of it
     */
    public function testStreamPassedTwice(): void
    {
        // Given
        $stream = Stream::of(GeneratorFixture::getGenerator([1, 2, 3]));

        // When
        $result = \iterator_to_array(Multi::mergeSorted($stream, $stream), false);

        // Then
        $this->assertSame([1, 1, 2, 2, 3, 3], $result);
    }

    /**
     * @test mergeSorted reads a stream and the generator it holds as independent copies of the generator
     */
    public function testStreamAndItsGenerator(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2, 3]);
        $stream    = Stream::of($generator);

        // When
        $result = \iterator_to_array(Multi::mergeSorted($stream, $generator), false);

        // Then
        $this->assertSame([1, 1, 2, 2, 3, 3], $result);
    }

    /**
     * @test mergeSorted gives each copy of a repeated generator its own argument position in ties with other sources
     */
    public function testGeneratorPassedTwiceAroundOtherSources(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2, 3]);

        // When
        $result = \iterator_to_array(
            Multi::mergeSorted($generator, [0.0, 2.0, 5.0], $generator, new ArrayIteratorFixture([2.5])),
            false,
        );

        // Then
        $this->assertSame([0.0, 1, 1, 2, 2.0, 2, 2.5, 3, 3, 5.0], $result);
    }

    /**
     * @test mergeSorted propagates the exception of a generator passed twice, after the values before it
     */
    public function testGeneratorPassedTwicePropagatesSourceException(): void
    {
        // Given
        $exception = new \RuntimeException('source failed');
        $failing = (static function () use ($exception): \Generator {
            yield 1;
            yield 2;
            throw $exception;
        })();
        $merged = Multi::mergeSorted($failing, [1.5], $failing);
        $yielded = [];

        // When
        try {
            foreach ($merged as $value) {
                $yielded[] = $value;
            }
            $this->fail('Expected source exception to propagate');
        } catch (\RuntimeException $e) {
            // Then
            $this->assertSame($exception, $e);
            $this->assertNull($e->getPrevious());
        }

        // Then
        $this->assertSame([1, 1, 1.5, 2], $yielded);
    }

    /**
     * @test mergeSorted stopped early reads a generator passed twice no further than the values it needs
     */
    public function testGeneratorPassedTwiceStoppedEarly(): void
    {
        // Given
        $pulls = 0;
        $generator = (static function () use (&$pulls): \Generator {
            ++$pulls;
            yield 1;
            ++$pulls;
            yield 2;
            ++$pulls;
            yield 3;
        })();
        $merged = Multi::mergeSorted($generator, $generator);

        // When
        $result = \iterator_to_array(Single::limit($merged, 3), false);

        // Then
        $this->assertSame([1, 1, 2], $result);
        $this->assertSame(2, $pulls);
    }

    /**
     * @test mergeSorted rejects a NAN reached in a generator passed twice, after the values before it
     */
    public function testGeneratorPassedTwiceRejectsLaterNan(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, \NAN]);
        $merged = Multi::mergeSorted($generator, $generator);
        $yielded = [];

        // When
        try {
            foreach ($merged as $value) {
                $yielded[] = $value;
            }
            $this->fail('Expected NAN to be rejected');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('Multi::mergeSorted cannot order NAN', $e->getMessage());
        }

        // Then
        $this->assertSame([1], $yielded);
    }

    /**
     * @test mergeSorted rejects a NAN seeded from a generator passed twice before yielding anything
     */
    public function testGeneratorPassedTwiceRejectsSeededNan(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([\NAN]);
        $merged = Multi::mergeSorted([1], $generator, $generator);
        $yielded = [];

        // When
        try {
            foreach ($merged as $value) {
                $yielded[] = $value;
            }
            $this->fail('Expected NAN to be rejected');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertSame('Multi::mergeSorted cannot order NAN', $e->getMessage());
        }

        // Then
        $this->assertSame([], $yielded);
    }

    /**
     * @test mergeSorted reads only the values of an iterator passed twice, never its keys
     */
    public function testIteratorPassedTwiceKeysNotRead(): void
    {
        // Given
        $iterator = new class implements \Iterator {
            private int $i = 0;
            private const VALUES = [1, 2];

            public function rewind(): void
            {
                $this->i = 0;
            }

            public function valid(): bool
            {
                return $this->i < \count(self::VALUES);
            }

            public function key(): never
            {
                throw new \LogicException('key() must not be called');
            }

            public function current(): int
            {
                return self::VALUES[$this->i];
            }

            public function next(): void
            {
                ++$this->i;
            }
        };

        // When
        $result = \iterator_to_array(Multi::mergeSorted($iterator, [1.5], $iterator), false);

        // Then
        $this->assertSame([1, 1, 1.5, 2, 2], $result);
    }
}
