<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture;

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
}
