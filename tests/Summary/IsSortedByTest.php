<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture;

class IsSortedByTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test isSortedBy example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $people = [
            (object)['name' => 'Alice', 'age' => 25],
            (object)['name' => 'Bob',   'age' => 30],
            (object)['name' => 'Carol', 'age' => 42],
        ];

        // When
        $result = Summary::isSortedBy($people, fn ($p) => $p->age);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         isSortedBy array true
     * @dataProvider dataProviderForTrue
     * @param        array    $data
     * @param        callable $keyFunc
     */
    public function testArrayTrue(array $data, callable $keyFunc): void
    {
        // When
        $result = Summary::isSortedBy($data, $keyFunc);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         isSortedBy generator true
     * @dataProvider dataProviderForTrue
     * @param        array    $data
     * @param        callable $keyFunc
     */
    public function testGeneratorTrue(array $data, callable $keyFunc): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Summary::isSortedBy($generator, $keyFunc);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         isSortedBy iterator true
     * @dataProvider dataProviderForTrue
     * @param        array    $data
     * @param        callable $keyFunc
     */
    public function testIteratorTrue(array $data, callable $keyFunc): void
    {
        // Given
        $iterator = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Summary::isSortedBy($iterator, $keyFunc);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         isSortedBy traversable true
     * @dataProvider dataProviderForTrue
     * @param        array    $data
     * @param        callable $keyFunc
     */
    public function testTraversableTrue(array $data, callable $keyFunc): void
    {
        // Given
        $traversable = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Summary::isSortedBy($traversable, $keyFunc);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForTrue(): array
    {
        return [
            // Empty and single element are vacuously sorted
            [[], fn ($x) => $x],
            [[5], fn ($x) => $x],
            // Non-decreasing projections
            [[1, 2, 3], fn ($x) => $x],
            [[3, 2, 1], fn ($x) => -$x],
            // Equal projections (non-decreasing → true)
            [[1, 1, 1], fn ($x) => $x],
            [['ab', 'cd', 'ef'], fn ($x) => \strlen($x)],
            // Sort by absolute value
            [[0, -1, 2, -3], fn ($x) => \abs($x)],
            // Objects by property
            [
                [
                    (object)['v' => 1],
                    (object)['v' => 2],
                    (object)['v' => 2],
                    (object)['v' => 5],
                ],
                fn ($o) => $o->v,
            ],
            // Strings projected by length (pear=4, apple=5, banana=6)
            [['pear', 'apple', 'banana'], fn ($x) => \strlen($x)],
        ];
    }

    /**
     * @test         isSortedBy array false
     * @dataProvider dataProviderForFalse
     * @param        array    $data
     * @param        callable $keyFunc
     */
    public function testArrayFalse(array $data, callable $keyFunc): void
    {
        // When
        $result = Summary::isSortedBy($data, $keyFunc);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         isSortedBy generator false
     * @dataProvider dataProviderForFalse
     * @param        array    $data
     * @param        callable $keyFunc
     */
    public function testGeneratorFalse(array $data, callable $keyFunc): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Summary::isSortedBy($generator, $keyFunc);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         isSortedBy iterator false
     * @dataProvider dataProviderForFalse
     * @param        array    $data
     * @param        callable $keyFunc
     */
    public function testIteratorFalse(array $data, callable $keyFunc): void
    {
        // Given
        $iterator = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Summary::isSortedBy($iterator, $keyFunc);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         isSortedBy traversable false
     * @dataProvider dataProviderForFalse
     * @param        array    $data
     * @param        callable $keyFunc
     */
    public function testTraversableFalse(array $data, callable $keyFunc): void
    {
        // Given
        $traversable = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Summary::isSortedBy($traversable, $keyFunc);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForFalse(): array
    {
        return [
            [[2, 1], fn ($x) => $x],
            [[1, 2, 3], fn ($x) => -$x],
            [['banana', 'pear', 'apple'], fn ($x) => \strlen($x)],
            [
                [
                    (object)['v' => 1],
                    (object)['v' => 5],
                    (object)['v' => 3],
                ],
                fn ($o) => $o->v,
            ],
            // NAN projection makes it unsorted
            [[1, 2, 3], fn ($x) => $x === 2 ? \NAN : $x],
        ];
    }

    /**
     * @test         isSortedBy array of int and float keys beyond 2^53, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array    $data
     * @param        callable $keyFunc
     * @param        bool     $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionArray(array $data, callable $keyFunc, bool $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Summary::isSortedBy($data, $keyFunc);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         isSortedBy generator of int and float keys beyond 2^53, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array    $data
     * @param        callable $keyFunc
     * @param        bool     $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionGenerator(array $data, callable $keyFunc, bool $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $generator = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Summary::isSortedBy($generator, $keyFunc);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         isSortedBy iterator of int and float keys beyond 2^53, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array    $data
     * @param        callable $keyFunc
     * @param        bool     $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionIterator(array $data, callable $keyFunc, bool $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $iterator = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Summary::isSortedBy($iterator, $keyFunc);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         isSortedBy traversable of int and float keys beyond 2^53, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array    $data
     * @param        callable $keyFunc
     * @param        bool     $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionTraversable(array $data, callable $keyFunc, bool $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $traversable = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Summary::isSortedBy($traversable, $keyFunc);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which compares int and float by exact value:
     *   all(a <= b for a, b in pairwise(r['v'] for r in rows))
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecision(): array
    {
        return [
            'float key 2.0^53 then int key 2^53 + 1' => [
                [['v' => 9007199254740992.0], ['v' => 9007199254740993]],
                fn (array $row) => $row['v'],
                true,
            ],
            'int key 2^53 + 1 then float key 2.0^53' => [
                [['v' => 9007199254740993], ['v' => 9007199254740992.0]],
                fn (array $row) => $row['v'],
                false,
            ],
            'int key PHP_INT_MAX then float key 2.0^63' => [
                [['v' => 9223372036854775807], ['v' => 9.223372036854776e+18]],
                fn (array $row) => $row['v'],
                true,
            ],
            'float key 2.0^63 then int key PHP_INT_MAX' => [
                [['v' => 9.223372036854776e+18], ['v' => 9223372036854775807]],
                fn (array $row) => $row['v'],
                false,
            ],
        ];
    }
}
