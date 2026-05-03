<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture;

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
}
