<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture;
use IterTools\Transform;

class StartsWithCoerciveTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /**
     * @test         startsWithCoercive - empty prefix is always true
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $prefix
     */
    public function testEmptyPrefixIsAlwaysTrue(iterable $prefix): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         startsWithCoercive - empty source with non-empty prefix is false
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptySourceNonEmptyPrefix(iterable $data): void
    {
        // Given
        $prefix = [1];

        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         startsWithCoercive - true for arrays
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        array<mixed> $prefix
     */
    public function testTrueArray(array $data, array $prefix): void
    {
        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         startsWithCoercive - true for generators
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        array<mixed> $prefix
     */
    public function testTrueGenerator(array $data, array $prefix): void
    {
        // Given
        $dataIt = Fixture\GeneratorFixture::getGenerator($data);
        $prefixIt = Fixture\GeneratorFixture::getGenerator($prefix);

        // When
        $result = Summary::startsWithCoercive($dataIt, $prefixIt);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         startsWithCoercive - true for iterators
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        array<mixed> $prefix
     */
    public function testTrueIterator(array $data, array $prefix): void
    {
        // Given
        $dataIt = new Fixture\ArrayIteratorFixture($data);
        $prefixIt = new Fixture\ArrayIteratorFixture($prefix);

        // When
        $result = Summary::startsWithCoercive($dataIt, $prefixIt);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         startsWithCoercive - true for traversables
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        array<mixed> $prefix
     */
    public function testTrueTraversable(array $data, array $prefix): void
    {
        // Given
        $dataIt = new Fixture\IteratorAggregateFixture($data);
        $prefixIt = new Fixture\IteratorAggregateFixture($prefix);

        // When
        $result = Summary::startsWithCoercive($dataIt, $prefixIt);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForTrue(): array
    {
        return [
            [[1, 2, 3], [1, 2, 3]],
            [[1, 2, 3, 4, 5], [1, 2]],
            // string-int coercion
            [[1, 2], ['1', '2']],
            [['1', '2'], [1, 2]],
            // bool/int coercion
            [[0, 1], [false, true]],
            // float/int coercion
            [[1.0], [1]],
        ];
    }

    /**
     * @test         startsWithCoercive - false for arrays
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        array<mixed> $prefix
     */
    public function testFalseArray(array $data, array $prefix): void
    {
        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         startsWithCoercive - false for generators
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        array<mixed> $prefix
     */
    public function testFalseGenerator(array $data, array $prefix): void
    {
        // Given
        $dataIt = Fixture\GeneratorFixture::getGenerator($data);
        $prefixIt = Fixture\GeneratorFixture::getGenerator($prefix);

        // When
        $result = Summary::startsWithCoercive($dataIt, $prefixIt);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         startsWithCoercive - false for iterators
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        array<mixed> $prefix
     */
    public function testFalseIterator(array $data, array $prefix): void
    {
        // Given
        $dataIt = new Fixture\ArrayIteratorFixture($data);
        $prefixIt = new Fixture\ArrayIteratorFixture($prefix);

        // When
        $result = Summary::startsWithCoercive($dataIt, $prefixIt);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         startsWithCoercive - false for traversables
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        array<mixed> $prefix
     */
    public function testFalseTraversable(array $data, array $prefix): void
    {
        // Given
        $dataIt = new Fixture\IteratorAggregateFixture($data);
        $prefixIt = new Fixture\IteratorAggregateFixture($prefix);

        // When
        $result = Summary::startsWithCoercive($dataIt, $prefixIt);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForFalse(): array
    {
        return [
            [[1, 2, 3], [2]],
            [[1, 2, 3], [1, 3]],
            // source shorter than prefix
            [[1, 2], [1, 2, 3]],
            [[], [1]],
            // distinct values
            [[1, 2], [1, 'b']],
        ];
    }

    /**
     * @test startsWithCoercive - NaN matches NaN (library-wide coercive contract).
     */
    public function testNaNMatchesNaN(): void
    {
        // Given
        $data = [\NAN, 1, 2];
        $prefix = [\NAN];

        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWithCoercive - array elements compared by serialized value (insertion order matters).
     */
    public function testArrayElementsSameInsertionOrder(): void
    {
        // Given
        $data = [['a' => 1, 'b' => 2], 'tail'];
        $prefix = [['a' => 1, 'b' => 2]];

        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWithCoercive - associative arrays with different insertion order do not match.
     */
    public function testArrayElementsDifferentInsertionOrder(): void
    {
        // Given
        $data = [['a' => 1, 'b' => 2]];
        $prefix = [['b' => 2, 'a' => 1]];

        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test startsWithCoercive - serializable objects with same state match.
     */
    public function testObjectsWithSameStateMatch(): void
    {
        // Given
        $a = new \stdClass();
        $a->id = 1;
        $b = new \stdClass();
        $b->id = 1;

        // When
        $result = Summary::startsWithCoercive([$a, 'tail'], [$b]);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWithCoercive - serializable objects with different state do not match.
     */
    public function testObjectsWithDifferentStateDoNotMatch(): void
    {
        // Given
        $a = new \stdClass();
        $a->id = 1;
        $b = new \stdClass();
        $b->id = 2;

        // When
        $result = Summary::startsWithCoercive([$a], [$b]);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test startsWithCoercive - non-serializable object reached during comparison throws.
     */
    public function testNonSerializableObjectThrows(): void
    {
        // Given
        $data = [new Fixture\NonSerializableFixture(1)];
        $prefix = [new Fixture\NonSerializableFixture(1)];

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Summary::startsWithCoercive($data, $prefix);
    }

    /**
     * @test         startsWithCoercive - never advances a generator source past the last element compared
     * @dataProvider dataProviderForNoOverRead
     * @param        array<mixed> $prefix
     * @param        bool         $expected
     * @param        array<mixed> $expectedPulled
     */
    public function testDoesNotAdvancePastLastComparedElementGenerator(
        array $prefix,
        bool $expected,
        array $expectedPulled
    ): void {
        // Given a source that records every element pulled from it
        $pulled = [];
        $data = Fixture\GeneratorFixture::getRecordingGenerator([1, 2, 3, 4, 5], $pulled);

        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertSame($expected, $result);

        // And the source was never advanced beyond the last element compared
        $this->assertSame($expectedPulled, $pulled);
    }

    /**
     * @test         startsWithCoercive - never advances an iterator source past the last element compared
     * @dataProvider dataProviderForNoOverRead
     * @param        array<mixed> $prefix
     * @param        bool         $expected
     * @param        array<mixed> $expectedPulled
     */
    public function testDoesNotAdvancePastLastComparedElementIterator(
        array $prefix,
        bool $expected,
        array $expectedPulled
    ): void {
        // Given a source that records every element pulled from it
        $pulled = [];
        $data = new Fixture\RecordingIteratorFixture([1, 2, 3, 4, 5], $pulled);

        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertSame($expected, $result);

        // And the source was never advanced beyond the last element compared
        $this->assertSame($expectedPulled, $pulled);
    }

    /**
     * @test         startsWithCoercive - never advances an IteratorAggregate source past the last element compared
     * @dataProvider dataProviderForNoOverRead
     * @param        array<mixed> $prefix
     * @param        bool         $expected
     * @param        array<mixed> $expectedPulled
     */
    public function testDoesNotAdvancePastLastComparedElementIteratorAggregate(
        array $prefix,
        bool $expected,
        array $expectedPulled
    ): void {
        // Given a source that records every element pulled from it
        $pulled = [];
        $data = new Fixture\RecordingIteratorAggregateFixture([1, 2, 3, 4, 5], $pulled);

        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertSame($expected, $result);

        // And the source was never advanced beyond the last element compared
        $this->assertSame($expectedPulled, $pulled);
    }

    public static function dataProviderForNoOverRead(): array
    {
        return [
            // full match: stops at the last prefix element
            [[1], true, [1]],
            [[1, 2], true, [1, 2]],
            [[1, 2, 3, 4], true, [1, 2, 3, 4]],
            [[1, 2, 3, 4, 5], true, [1, 2, 3, 4, 5]],
            // mismatch: stops at the mismatching element
            [[9], false, [1]],
            [[1, 9], false, [1, 2]],
            [[1, 2, 3, 9], false, [1, 2, 3, 4]],
            // source shorter than prefix
            [[1, 2, 3, 4, 5, 6], false, [1, 2, 3, 4, 5]],
        ];
    }

    /**
     * @test startsWithCoercive - a generator that throws after yielding the prefix still matches
     */
    public function testMatchesWhenSourceThrowsAfterPrefixGenerator(): void
    {
        // Given
        $data = (static function (): \Generator {
            yield 1;
            yield 2;
            throw new \LogicException('Source advanced past the prefix');
        })();

        // When
        $result = Summary::startsWithCoercive($data, [1, 2]);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         startsWithCoercive - leaves a generator source positioned on the last element compared
     * @dataProvider dataProviderForSourcePosition
     * @param        array<mixed> $prefix
     * @param        bool         $expected
     * @param        int          $expectedKey
     */
    public function testLeavesSourceOnLastComparedElementGenerator(array $prefix, bool $expected, int $expectedKey): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5]);

        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertSame($expected, $result);
        $this->assertSame($expectedKey, $data->key());
    }

    /**
     * @test         startsWithCoercive - leaves an iterator source positioned on the last element compared
     * @dataProvider dataProviderForSourcePosition
     * @param        array<mixed> $prefix
     * @param        bool         $expected
     * @param        int          $expectedKey
     */
    public function testLeavesSourceOnLastComparedElementIterator(array $prefix, bool $expected, int $expectedKey): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5]);

        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertSame($expected, $result);
        $this->assertSame($expectedKey, $data->key());
    }

    public static function dataProviderForSourcePosition(): array
    {
        return [
            // full match: on the last prefix element
            [[1], true, 0],
            [[1, 2], true, 1],
            [[1, 2, 3, 4, 5], true, 4],
            // mismatch: on the mismatching element
            [[9], false, 0],
            [[1, 2, 9], false, 2],
        ];
    }

    /**
     * @test startsWithCoercive - leaves a generator source exhausted when it is shorter than the prefix
     */
    public function testLeavesSourceExhaustedWhenShorterThanPrefixGenerator(): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator([1, 2]);

        // When
        $result = Summary::startsWithCoercive($data, [1, 2, 3]);

        // Then
        $this->assertFalse($result);
        $this->assertFalse($data->valid());
    }

    /**
     * @test startsWithCoercive - leaves an iterator source exhausted when it is shorter than the prefix
     */
    public function testLeavesSourceExhaustedWhenShorterThanPrefixIterator(): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture([1, 2]);

        // When
        $result = Summary::startsWithCoercive($data, [1, 2, 3]);

        // Then
        $this->assertFalse($result);
        $this->assertFalse($data->valid());
    }

    /**
     * @test startsWithCoercive - a generator passed as its own prefix is true
     */
    public function testOwnSourceGenerator(): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);

        // When
        $result = Summary::startsWithCoercive($data, $data);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWithCoercive - an iterator passed as its own prefix is true without being read
     */
    public function testOwnSourceIteratorIsNotRead(): void
    {
        // Given
        $pulled = [];
        $data = new Fixture\RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = Summary::startsWithCoercive($data, $data);

        // Then
        $this->assertTrue($result);
        $this->assertSame([], $pulled);
    }

    /**
     * @test startsWithCoercive - a throwing generator passed as its own prefix is true without its exception being raised
     */
    public function testOwnSourceThrowingGenerator(): void
    {
        // Given
        $data = (static function (): \Generator {
            throw new \RuntimeException('must not be pulled');
            yield; // unreachable
        })();

        // When
        $result = Summary::startsWithCoercive($data, $data);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWithCoercive - an IteratorAggregate passed as its own prefix is true
     */
    public function testOwnSourceIteratorAggregate(): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture([1, 2, 3]);

        // When
        $result = Summary::startsWithCoercive($data, $data);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWithCoercive - an IteratorAggregate passed as its own prefix is true without its iterator being obtained
     */
    public function testOwnSourceIteratorAggregateIsNotRead(): void
    {
        // Given
        $data = new Fixture\CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $result = Summary::startsWithCoercive($data, $data);

        // Then
        $this->assertTrue($result);
        $this->assertSame(0, $data->getIteratorCallCount());
    }

    /**
     * @test startsWithCoercive - an IteratorAggregate over a generator passed as its own prefix is true
     */
    public function testOwnSourceIteratorAggregateOverGenerator(): void
    {
        // Given
        $data = new Fixture\WrappingIteratorAggregateFixture(Fixture\GeneratorFixture::getGenerator([1, 2, 3]));

        // When
        $result = Summary::startsWithCoercive($data, $data);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWithCoercive - two IteratorAggregates over one generator are true
     */
    public function testAggregatesSharingOneGenerator(): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $data = new Fixture\WrappingIteratorAggregateFixture($generator);
        $prefix = new Fixture\WrappingIteratorAggregateFixture($generator);

        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWithCoercive - a generator with an IteratorAggregate over it as its prefix is true
     */
    public function testGeneratorWithAggregateOverItAsPrefix(): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $prefix = new Fixture\WrappingIteratorAggregateFixture($data);

        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWithCoercive - an IteratorAggregate over a generator with that generator as its prefix is true
     */
    public function testAggregateOverGeneratorWithItAsPrefix(): void
    {
        // Given
        $prefix = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $data = new Fixture\WrappingIteratorAggregateFixture($prefix);

        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWithCoercive - two IteratorAggregates over one partly read generator are true
     */
    public function testAggregatesSharingOneAdvancedGenerator(): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $generator->next();
        $data = new Fixture\WrappingIteratorAggregateFixture($generator);
        $prefix = new Fixture\WrappingIteratorAggregateFixture($generator);

        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWithCoercive - two IteratorAggregates over one exhausted generator are true
     */
    public function testAggregatesSharingOneExhaustedGenerator(): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        foreach ($generator as $ignored) {
        }
        $data = new Fixture\WrappingIteratorAggregateFixture($generator);
        $prefix = new Fixture\WrappingIteratorAggregateFixture($generator);

        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWithCoercive - an empty iterator prefix is true without the source's iterator being obtained
     */
    public function testEmptyIteratorPrefixDoesNotInitializeSource(): void
    {
        // Given
        $data = new Fixture\CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $result = Summary::startsWithCoercive($data, new \EmptyIterator());

        // Then
        $this->assertTrue($result);
        $this->assertSame(0, $data->getIteratorCallCount());
    }

    /**
     * @test startsWithCoercive - an empty generator prefix is true without the source's iterator being obtained
     */
    public function testEmptyGeneratorPrefixDoesNotInitializeSource(): void
    {
        // Given
        $data = new Fixture\CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $result = Summary::startsWithCoercive($data, Fixture\GeneratorFixture::getGenerator([]));

        // Then
        $this->assertTrue($result);
        $this->assertSame(0, $data->getIteratorCallCount());
    }

    /**
     * @test startsWithCoercive - two IteratorAggregates over one advanced tee copy are true
     */
    public function testAggregatesSharingOneAdvancedTeeCopy(): void
    {
        // Given
        [$copy] = Transform::tee([1, 2, 3], 1);
        $copy->next();
        $data = new Fixture\WrappingIteratorAggregateFixture($copy);
        $prefix = new Fixture\WrappingIteratorAggregateFixture($copy);

        // When
        $result = Summary::startsWithCoercive($data, $prefix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test startsWithCoercive - a prefix failing to start keeps its exception when the source also fails to start
     */
    public function testFailingPrefixExceptionIsKeptWhenSourceAlsoFails(): void
    {
        // Given
        $data = new class () implements \IteratorAggregate {
            public function getIterator(): \Traversable
            {
                throw new \LogicException('source error');
            }
        };
        $prefix = (static function (): \Generator {
            throw new \DomainException('prefix error');
            yield; // unreachable
        })();

        // Then
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('prefix error');

        // When
        Summary::startsWithCoercive($data, $prefix);
    }
}
