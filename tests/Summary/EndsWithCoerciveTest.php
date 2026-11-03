<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture;
use IterTools\Transform;

class EndsWithCoerciveTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /**
     * @test         endsWithCoercive - empty suffix is always true
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $suffix
     */
    public function testEmptySuffixIsAlwaysTrue(iterable $suffix): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         endsWithCoercive - empty source with non-empty suffix is false
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptySourceNonEmptySuffix(iterable $data): void
    {
        // Given
        $suffix = [1];

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         endsWithCoercive - true for arrays
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        array<mixed> $suffix
     */
    public function testTrueArray(array $data, array $suffix): void
    {
        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         endsWithCoercive - true for generators
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        array<mixed> $suffix
     */
    public function testTrueGenerator(array $data, array $suffix): void
    {
        // Given
        $dataIt = Fixture\GeneratorFixture::getGenerator($data);
        $suffixIt = Fixture\GeneratorFixture::getGenerator($suffix);

        // When
        $result = Summary::endsWithCoercive($dataIt, $suffixIt);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         endsWithCoercive - true for iterators
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        array<mixed> $suffix
     */
    public function testTrueIterator(array $data, array $suffix): void
    {
        // Given
        $dataIt = new Fixture\ArrayIteratorFixture($data);
        $suffixIt = new Fixture\ArrayIteratorFixture($suffix);

        // When
        $result = Summary::endsWithCoercive($dataIt, $suffixIt);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         endsWithCoercive - true for traversables
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        array<mixed> $suffix
     */
    public function testTrueTraversable(array $data, array $suffix): void
    {
        // Given
        $dataIt = new Fixture\IteratorAggregateFixture($data);
        $suffixIt = new Fixture\IteratorAggregateFixture($suffix);

        // When
        $result = Summary::endsWithCoercive($dataIt, $suffixIt);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForTrue(): array
    {
        return [
            [[1, 2, 3], [1, 2, 3]],
            [[1, 2, 3, 4, 5], [4, 5]],
            // string-int coercion
            [[1, 2], ['1', '2']],
            [['1', '2'], [1, 2]],
            // bool/int coercion
            [[0, 1], [false, true]],
            // float/int coercion
            [[1.0], [1]],
            // source one longer than suffix
            [[1, 2, 3, 4], ['2', '3', '4']],
            // source length not a multiple of suffix length
            [[1, 2, 3, 4, 5, 6, 7], ['5', 6, '7']],
            [[1, 2, 3, 4, 5, 6, 7, 8], [6, '7', 8.0]],
            // source length a multiple of suffix length
            [[1, 2, 3, 4, 5, 6, 7, 8, 9], ['7', '8', '9']],
        ];
    }

    /**
     * @test         endsWithCoercive - false for arrays
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        array<mixed> $suffix
     */
    public function testFalseArray(array $data, array $suffix): void
    {
        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         endsWithCoercive - false for generators
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        array<mixed> $suffix
     */
    public function testFalseGenerator(array $data, array $suffix): void
    {
        // Given
        $dataIt = Fixture\GeneratorFixture::getGenerator($data);
        $suffixIt = Fixture\GeneratorFixture::getGenerator($suffix);

        // When
        $result = Summary::endsWithCoercive($dataIt, $suffixIt);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         endsWithCoercive - false for iterators
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        array<mixed> $suffix
     */
    public function testFalseIterator(array $data, array $suffix): void
    {
        // Given
        $dataIt = new Fixture\ArrayIteratorFixture($data);
        $suffixIt = new Fixture\ArrayIteratorFixture($suffix);

        // When
        $result = Summary::endsWithCoercive($dataIt, $suffixIt);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         endsWithCoercive - false for traversables
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        array<mixed> $suffix
     */
    public function testFalseTraversable(array $data, array $suffix): void
    {
        // Given
        $dataIt = new Fixture\IteratorAggregateFixture($data);
        $suffixIt = new Fixture\IteratorAggregateFixture($suffix);

        // When
        $result = Summary::endsWithCoercive($dataIt, $suffixIt);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForFalse(): array
    {
        return [
            [[1, 2, 3], [2]],
            [[1, 2, 3], [2, 4]],
            // source shorter than suffix
            [[1, 2], [1, 2, 3]],
            // distinct values
            [[1, 2], ['a', 2]],
            // source one longer than suffix
            [[1, 2, 3, 4], ['1', '2', '3']],
            // mismatch at the oldest window element, source length not a multiple of suffix length
            [[1, 2, 3, 4, 5, 6, 7], ['4', 6, 7]],
            [[1, 2, 3, 4, 5, 6, 7, 8], ['5', 7, 8]],
            // mismatch at the newest window element
            [[1, 2, 3, 4, 5, 6, 7], [5, 6, '8']],
            // suffix matches a window rotated from the end
            [[1, 2, 3, 4, 5, 6, 7], ['7', 5, 6]],
            [[1, 2, 3, 4, 5, 6, 7], [6, '7', 5]],
        ];
    }

    /**
     * @test endsWithCoercive - NaN matches NaN (library-wide coercive contract).
     */
    public function testNaNMatchesNaN(): void
    {
        // Given
        $data = [1, 2, \NAN];
        $suffix = [\NAN];

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - array elements compared by serialized value (insertion order matters).
     */
    public function testArrayElementsSameInsertionOrder(): void
    {
        // Given
        $data = ['head', ['a' => 1, 'b' => 2]];
        $suffix = [['a' => 1, 'b' => 2]];

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - associative arrays with different insertion order do not match.
     */
    public function testArrayElementsDifferentInsertionOrder(): void
    {
        // Given
        $data = [['a' => 1, 'b' => 2]];
        $suffix = [['b' => 2, 'a' => 1]];

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test endsWithCoercive - serializable objects with same state match.
     */
    public function testObjectsWithSameStateMatch(): void
    {
        // Given
        $a = new \stdClass();
        $a->id = 1;
        $b = new \stdClass();
        $b->id = 1;

        // When
        $result = Summary::endsWithCoercive(['head', $a], [$b]);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - non-serializable object reached during comparison throws.
     */
    public function testNonSerializableObjectThrows(): void
    {
        // Given
        $data = [new Fixture\NonSerializableFixture(1)];
        $suffix = [new Fixture\NonSerializableFixture(1)];

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Summary::endsWithCoercive($data, $suffix);
    }

    /**
     * @test endsWithCoercive - empty suffix does not consume source generator.
     */
    public function testEmptySuffixDoesNotConsumeSource(): void
    {
        // Given
        $consumed = 0;
        $source = (function () use (&$consumed) {
            while (true) {
                $consumed++;
                yield 1;
            }
        })();

        // When
        $result = Summary::endsWithCoercive($source, []);

        // Then
        $this->assertTrue($result);
        $this->assertSame(0, $consumed);
    }

    /**
     * Regression: a source that yields one object, mutated between reads, is compared as the
     * object was when each element was read, not as it is after later reads.
     *
     * @test endsWithCoercive - mutated-object source ending with the suffix
     */
    public function testMutatedObjectSourceTrue(): void
    {
        // Given
        $data = self::mutatedObject(1, 2);
        $suffix = [self::object(1), self::object(2)];

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - mutated-object source not ending with the suffix
     */
    public function testMutatedObjectSourceFalse(): void
    {
        // Given
        $data = self::mutatedObject(1, 2);
        $suffix = [self::object(2), self::object(2)];

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test endsWithCoercive - mutated-object suffix is compared as read
     */
    public function testMutatedObjectSuffix(): void
    {
        // Given
        $data = [self::object(1), self::object(2)];
        $suffix = self::mutatedObject(1, 2);

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - reference-slot source rows are compared as read
     */
    public function testReferenceSlotSource(): void
    {
        // Given
        $data = self::referenceSlotRows(1, 2);
        $suffix = [['id' => '1'], ['id' => 2.0]];

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - a non-serializable object before the compared elements does not throw
     */
    public function testNonSerializableObjectBeforeSuffixDoesNotThrow(): void
    {
        // Given
        $data = [new Fixture\NonSerializableFixture(1), 1, 2];
        $suffix = [1, 2];

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - a non-serializable object after the first mismatch does not throw
     */
    public function testNonSerializableObjectAfterMismatchDoesNotThrow(): void
    {
        // Given
        $data = [1, new Fixture\NonSerializableFixture(1)];
        $suffix = [2, new Fixture\NonSerializableFixture(1)];

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test endsWithCoercive - a non-serializable object after the first mismatch does not throw when the
     *       window has wrapped past its oldest element
     */
    public function testNonSerializableObjectAfterMismatchInWrappedWindowDoesNotThrow(): void
    {
        // Given
        $data = [0, 1, new Fixture\NonSerializableFixture(1)];
        $suffix = [2, new Fixture\NonSerializableFixture(1)];

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test endsWithCoercive - a non-serializable object in a wrapped window is reached during comparison
     *       and throws
     */
    public function testNonSerializableObjectInWrappedWindowThrows(): void
    {
        // Given
        $data = [0, 1, new Fixture\NonSerializableFixture(1), 3];
        $suffix = [1, 2, 3];

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Summary::endsWithCoercive($data, $suffix);
    }

    /**
     * @test endsWithCoercive - a non-serializable suffix object is reached during comparison and throws
     */
    public function testNonSerializableSuffixObjectThrows(): void
    {
        // Given
        $data = [1, 2];
        $suffix = [1, new Fixture\NonSerializableFixture(1)];

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Summary::endsWithCoercive($data, $suffix);
    }

    private static function object(int $value): \stdClass
    {
        $object = new \stdClass();
        $object->value = $value;

        return $object;
    }

    /**
     * Yields one object, setting its value before each yield.
     *
     * @return \Generator<int, \stdClass>
     */
    private static function mutatedObject(int ...$values): \Generator
    {
        $object = new \stdClass();

        foreach ($values as $value) {
            $object->value = $value;

            yield $object;
        }
    }

    /**
     * Yields rows that all share one reference slot, as PDOStatement::bindColumn() fills them:
     * each yielded ['id' => &$id] follows the next assignment to $id.
     *
     * @return \Generator<int, array{id: int}>
     */
    private static function referenceSlotRows(int ...$ids): \Generator
    {
        $id = null;
        $row = ['id' => &$id];

        foreach ($ids as $value) {
            $id = $value;

            yield $row;
        }
    }

    /**
     * @test endsWithCoercive - a generator passed as its own suffix is true
     */
    public function testOwnSourceGenerator(): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);

        // When
        $result = Summary::endsWithCoercive($data, $data);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - an iterator passed as its own suffix is true without being read
     */
    public function testOwnSourceIteratorIsNotRead(): void
    {
        // Given
        $pulled = [];
        $data = new Fixture\RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = Summary::endsWithCoercive($data, $data);

        // Then
        $this->assertTrue($result);
        $this->assertSame([], $pulled);
    }

    /**
     * @test endsWithCoercive - a throwing generator passed as its own suffix is true without its exception being raised
     */
    public function testOwnSourceThrowingGenerator(): void
    {
        // Given
        $data = (static function (): \Generator {
            throw new \RuntimeException('must not be pulled');
            yield; // unreachable
        })();

        // When
        $result = Summary::endsWithCoercive($data, $data);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - an IteratorAggregate passed as its own suffix is true
     */
    public function testOwnSourceIteratorAggregate(): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture([1, 2, 3]);

        // When
        $result = Summary::endsWithCoercive($data, $data);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - an IteratorAggregate passed as its own suffix is true without its iterator being obtained
     */
    public function testOwnSourceIteratorAggregateIsNotRead(): void
    {
        // Given
        $data = new Fixture\CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $result = Summary::endsWithCoercive($data, $data);

        // Then
        $this->assertTrue($result);
        $this->assertSame(0, $data->getIteratorCallCount());
    }

    /**
     * @test endsWithCoercive - an IteratorAggregate over a generator passed as its own suffix is true
     */
    public function testOwnSourceIteratorAggregateOverGenerator(): void
    {
        // Given
        $data = new Fixture\WrappingIteratorAggregateFixture(Fixture\GeneratorFixture::getGenerator([1, 2, 3]));

        // When
        $result = Summary::endsWithCoercive($data, $data);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - two IteratorAggregates over one generator are true
     */
    public function testAggregatesSharingOneGenerator(): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $data = new Fixture\WrappingIteratorAggregateFixture($generator);
        $suffix = new Fixture\WrappingIteratorAggregateFixture($generator);

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - a generator with an IteratorAggregate over it as its suffix is true
     */
    public function testGeneratorWithAggregateOverItAsSuffix(): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $suffix = new Fixture\WrappingIteratorAggregateFixture($data);

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - an IteratorAggregate over a generator with that generator as its suffix is true
     */
    public function testAggregateOverGeneratorWithItAsSuffix(): void
    {
        // Given
        $suffix = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $data = new Fixture\WrappingIteratorAggregateFixture($suffix);

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - two IteratorAggregates over one partly read generator are true
     */
    public function testAggregatesSharingOneAdvancedGenerator(): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $generator->next();
        $data = new Fixture\WrappingIteratorAggregateFixture($generator);
        $suffix = new Fixture\WrappingIteratorAggregateFixture($generator);

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - two IteratorAggregates over one exhausted generator are true
     */
    public function testAggregatesSharingOneExhaustedGenerator(): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        foreach ($generator as $ignored) {
        }
        $data = new Fixture\WrappingIteratorAggregateFixture($generator);
        $suffix = new Fixture\WrappingIteratorAggregateFixture($generator);

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - an empty iterator suffix is true without the source's iterator being obtained
     */
    public function testEmptyIteratorSuffixDoesNotInitializeSource(): void
    {
        // Given
        $data = new Fixture\CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $result = Summary::endsWithCoercive($data, new \EmptyIterator());

        // Then
        $this->assertTrue($result);
        $this->assertSame(0, $data->getIteratorCallCount());
    }

    /**
     * @test endsWithCoercive - an empty generator suffix is true without the source's iterator being obtained
     */
    public function testEmptyGeneratorSuffixDoesNotInitializeSource(): void
    {
        // Given
        $data = new Fixture\CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $result = Summary::endsWithCoercive($data, Fixture\GeneratorFixture::getGenerator([]));

        // Then
        $this->assertTrue($result);
        $this->assertSame(0, $data->getIteratorCallCount());
    }

    /**
     * @test endsWithCoercive - two IteratorAggregates over one advanced tee copy are true
     */
    public function testAggregatesSharingOneAdvancedTeeCopy(): void
    {
        // Given
        [$copy] = Transform::tee([1, 2, 3], 1);
        $copy->next();
        $data = new Fixture\WrappingIteratorAggregateFixture($copy);
        $suffix = new Fixture\WrappingIteratorAggregateFixture($copy);

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWithCoercive - a suffix failing to start keeps its exception when the source also fails to start
     */
    public function testFailingSuffixExceptionIsKeptWhenSourceAlsoFails(): void
    {
        // Given
        $data = new class () implements \IteratorAggregate {
            public function getIterator(): \Traversable
            {
                throw new \LogicException('source error');
            }
        };
        $suffix = (static function (): \Generator {
            throw new \DomainException('suffix error');
            yield; // unreachable
        })();

        // Then
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('suffix error');

        // When
        Summary::endsWithCoercive($data, $suffix);
    }

    /**
     * @test endsWithCoercive - the first suffix element is captured before the source is initialized, so a row filled
     *       through a shared reference that initialization changes keeps the value it held
     */
    public function testFirstSuffixElementCapturedBeforeSourceInitialized(): void
    {
        // Given
        $state = new \stdClass();
        $state->id = 1;
        $suffix = (static function () use ($state): \Generator {
            $row = ['id' => &$state->id];
            yield $row;
        })();
        $data = new class ($state) implements \IteratorAggregate {
            public function __construct(private \stdClass $state)
            {
            }

            public function getIterator(): \Iterator
            {
                $this->state->id = 2;
                return new \ArrayIterator([['id' => 2]]);
            }
        };

        // When
        $result = Summary::endsWithCoercive($data, $suffix);

        // Then
        $this->assertFalse($result);
    }
}
