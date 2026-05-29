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
}
