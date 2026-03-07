<?php

declare(strict_types=1);

namespace IterTools\Tests\Set;

use IterTools\Set;
use IterTools\Tests\Fixture\SetFixture;

/**
 * Tests that set operations satisfy fundamental set theory properties.
 *
 * Properties are tested against both proper sets and multisets where applicable.
 * Some properties only hold for proper sets (no duplicates) — these are noted below.
 *
 * Difference:
 *   - A \ A = ∅
 *   - A \ ∅ = A
 *   - ∅ \ A = ∅
 *   - A \ B ⊆ A
 *
 * Intersection:
 *   - A ∩ B = B ∩ A                  (commutativity)
 *   - A ∩ A = A                      (idempotence)
 *   - A ∩ ∅ = ∅                      (empty annihilation)
 *   - A ∩ B ⊆ A  and  A ∩ B ⊆ B      (subset of operands)
 *
 * Union:
 *   - A ∪ B = B ∪ A                  (commutativity)
 *   - A ∪ A = A                      (idempotence)
 *   - A ∪ ∅ = A                      (identity)
 *   - A ⊆ A ∪ B  and  B ⊆ A ∪ B      (superset of operands)
 *
 * Symmetric difference:
 *   - A △ B = B △ A                  (commutativity)
 *   - A △ A = ∅                      (self-inverse)
 *   - A △ ∅ = A                      (identity)
 *
 * Cross-operation (sets and multisets):
 *   - A △ B = (A \ B) ∪ (B \ A)     (symmetric difference via difference)
 *   - A ∩ B = A \ (A \ B)           (intersection via difference)
 *
 * Cross-operation (proper sets only):
 *   - A ∪ B = (A \ B) ∪ (A ∩ B) ∪ (B \ A)     (disjoint partition)
 *   - (A \ B) ∩ (A ∩ B) = ∅                   (difference and intersection are disjoint)
 *   - (A △ B) ∩ (A ∩ B) = ∅                   (symmetric difference and intersection are disjoint)
 *
 * 3-operand properties (sets and multisets):
 *   - (A ∩ B) ∩ C = A ∩ (B ∩ C)              (intersection associativity)
 *   - (A ∪ B) ∪ C = A ∪ (B ∪ C)              (union associativity)
 *
 * 3-operand properties (proper sets only):
 *   - (A △ B) △ C = A △ (B △ C)              (symmetric difference associativity)
 *   - A ∩ (B ∪ C) = (A ∩ B) ∪ (A ∩ C)        (intersection distributes over union)
 *   - A ∪ (B ∩ C) = (A ∪ B) ∩ (A ∪ C)        (union distributes over intersection)
 *   - A \ (B ∪ C) = (A \ B) ∩ (A \ C)        (De Morgan's for difference)
 *   - A \ (B ∩ C) = (A \ B) ∪ (A \ C)        (De Morgan's for difference)
 */
class SetPropertiesTest extends \PHPUnit\Framework\TestCase
{
    // -- Data providers --

    /**
     * Pairs of proper sets (no duplicate elements within each set).
     */
    public static function dataProviderForSetPairs(): array
    {
        return [
            [[], []],
            [[1], []],
            [[], [1]],
            [[1], [1]],
            [[1, 2, 3], [1, 2, 3]],
            [[1, 2, 3, 4, 5], [3, 4, 5, 6, 7]],
            [[1, 2, 3], [4, 5, 6]],
            [[1, 2, 3, 4, 5], [1, 2, 3, '4', '5']],
            [['1', '2', '3', 4, 5], [1, 2, 3, '4', '5']],
            [[null, 1, 6], [null, 2, 7, 11]],
            [['1', 2, '3.3', true, false], [true, '2', 3.3, '4', '5']],
            [[[1, 2], [3, 4], [5, 6]], [[3, 4], [5, 6], [7, 8]]],
            [['a' => 1, 'b' => 2], ['b' => 2, 'c' => 3]],
        ];
    }

    /**
     * Pairs including multisets (may have duplicate elements).
     */
    public static function dataProviderForMultisetPairs(): array
    {
        return [
            [[2, 2], [2]],
            [[2, 2, 3], [2, 2, 4]],
            [[1, 1, 2, 2, 1, 1], [2, 2, 1, 1, 2, 2]],
            [[1, 1, 2, 2, 1, 1], [2, 2, '1', '1', 2, 2]],
            [[null, 1, null, 2], [null, 3, null]],
        ];
    }

    /**
     * All pairs (sets + multisets).
     */
    public static function dataProviderForAllPairs(): array
    {
        return \array_merge(
            static::dataProviderForSetPairs(),
            static::dataProviderForMultisetPairs(),
        );
    }

    /**
     * Triples of proper sets (no duplicate elements within each set).
     */
    public static function dataProviderForSetTriples(): array
    {
        return [
            [[], [], []],
            [[1, 2, 3], [], [4, 5]],
            [[1, 2, 3], [2, 3, 4], [3, 4, 5]],
            [[1, 2, 3, 4, 5], [3, 4, 5, 6, 7], [5, 6, 7, 8, 9]],
            [[1, 2, 3], [4, 5, 6], [7, 8, 9]],
            [[1, 2, 3], [1, 2, 3], [1, 2, 3]],
            [[1, 2, 3, 4, 5], [1, 2, 3, '4', '5'], ['1', '2', 3, 4, 5]],
            [[null, 1, 6], [null, 2, 7], [null, 3, 8]],
            [[[1, 2], [3, 4]], [[3, 4], [5, 6]], [[5, 6], [7, 8]]],
            [[[1], [2], [3]], [[2], [3], [4]], [[3], [4], [5]]],
            [['a' => 1, 'b' => 2], ['b' => 2, 'c' => 3], ['c' => 3, 'd' => 4]],
        ];
    }

    /**
     * Triples including multisets (may have duplicate elements).
     */
    public static function dataProviderForMultisetTriples(): array
    {
        return [
            [[1, 1, 2], [2, 2, 3], [3, 3, 1]],
            [[1, 1, 1], [1, 1], [1]],
            [[1, 1, 2, 2], [2, 2, 3, 3], [1, 1, 3, 3]],
        ];
    }

    /**
     * All triples (sets + multisets).
     */
    public static function dataProviderForAllTriples(): array
    {
        return \array_merge(
            static::dataProviderForSetTriples(),
            static::dataProviderForMultisetTriples(),
        );
    }

    public static function dataProviderForSingles(): array
    {
        return [
            [[]],
            [[1]],
            [[1, 2, 3]],
            [[1, 2, 3, 4, 5]],
            [[1, 1, 2, 2, 3]],
            [[null, 1, null, 2]],
            [['1', 2, '3.3', true, false]],
        ];
    }

    // -- Difference properties --

    /**
     * @dataProvider dataProviderForSingles
     */
    public function testDifferenceWithSelfIsEmpty(array $a): void
    {
        // A \ A = ∅
        $this->assertEqualsCanonicalizing([], \iterator_to_array(Set::difference($a, $a)));
    }

    /**
     * @dataProvider dataProviderForSingles
     */
    public function testDifferenceWithEmptyIsSelf(array $a): void
    {
        // A \ ∅ = A
        $this->assertEqualsCanonicalizing($a, \iterator_to_array(Set::difference($a, [])));
    }

    /**
     * @dataProvider dataProviderForSingles
     */
    public function testDifferenceOfEmptyWithAnythingIsEmpty(array $a): void
    {
        // ∅ \ A = ∅
        $this->assertEqualsCanonicalizing([], \iterator_to_array(Set::difference([], $a)));
    }

    /**
     * @dataProvider dataProviderForAllPairs
     */
    public function testDifferenceIsSubsetOfSource(array $a, array $b): void
    {
        // A \ B ⊆ A
        $diff = \iterator_to_array(Set::difference($a, $b));
        $this->assertTrue(SetFixture::isSubset($diff, $a));
    }

    // -- Intersection properties --

    /**
     * @dataProvider dataProviderForAllPairs
     */
    public function testIntersectionCommutativity(array $a, array $b): void
    {
        // A ∩ B = B ∩ A
        $ab = \iterator_to_array(Set::intersection($a, $b));
        $ba = \iterator_to_array(Set::intersection($b, $a));
        $this->assertEqualsCanonicalizing($ab, $ba);
    }

    /**
     * @dataProvider dataProviderForSingles
     */
    public function testIntersectionWithSelfIsSelf(array $a): void
    {
        // A ∩ A = A
        $this->assertEqualsCanonicalizing($a, \iterator_to_array(Set::intersection($a, $a)));
    }

    /**
     * @dataProvider dataProviderForSingles
     */
    public function testIntersectionWithEmptyIsEmpty(array $a): void
    {
        // A ∩ ∅ = ∅
        $this->assertEqualsCanonicalizing([], \iterator_to_array(Set::intersection($a, [])));
    }

    /**
     * @dataProvider dataProviderForAllPairs
     */
    public function testIntersectionIsSubsetOfBothOperands(array $a, array $b): void
    {
        // A ∩ B ⊆ A  and  A ∩ B ⊆ B
        $inter = \iterator_to_array(Set::intersection($a, $b));
        $this->assertTrue(SetFixture::isSubset($inter, $a));
        $this->assertTrue(SetFixture::isSubset($inter, $b));
    }

    // -- Union properties --

    /**
     * @dataProvider dataProviderForAllPairs
     */
    public function testUnionCommutativity(array $a, array $b): void
    {
        // A ∪ B = B ∪ A
        $ab = \iterator_to_array(Set::union($a, $b));
        $ba = \iterator_to_array(Set::union($b, $a));
        $this->assertEqualsCanonicalizing($ab, $ba);
    }

    /**
     * @dataProvider dataProviderForSingles
     */
    public function testUnionWithSelfIsSelf(array $a): void
    {
        // A ∪ A = A
        $this->assertEqualsCanonicalizing($a, \iterator_to_array(Set::union($a, $a)));
    }

    /**
     * @dataProvider dataProviderForSingles
     */
    public function testUnionWithEmptyIsSelf(array $a): void
    {
        // A ∪ ∅ = A
        $this->assertEqualsCanonicalizing($a, \iterator_to_array(Set::union($a, [])));
    }

    /**
     * @dataProvider dataProviderForAllPairs
     */
    public function testBothOperandsAreSubsetsOfUnion(array $a, array $b): void
    {
        // A ⊆ A ∪ B  and  B ⊆ A ∪ B
        $union = \iterator_to_array(Set::union($a, $b));
        $this->assertTrue(SetFixture::isSubset($a, $union));
        $this->assertTrue(SetFixture::isSubset($b, $union));
    }

    // -- Symmetric difference properties --

    /**
     * @dataProvider dataProviderForAllPairs
     */
    public function testSymmetricDifferenceCommutativity(array $a, array $b): void
    {
        // A △ B = B △ A
        $ab = \iterator_to_array(Set::symmetricDifference($a, $b));
        $ba = \iterator_to_array(Set::symmetricDifference($b, $a));
        $this->assertEqualsCanonicalizing($ab, $ba);
    }

    /**
     * @dataProvider dataProviderForSingles
     */
    public function testSymmetricDifferenceWithSelfIsEmpty(array $a): void
    {
        // A △ A = ∅
        $this->assertEqualsCanonicalizing([], \iterator_to_array(Set::symmetricDifference($a, $a)));
    }

    /**
     * @dataProvider dataProviderForSingles
     */
    public function testSymmetricDifferenceWithEmptyIsSelf(array $a): void
    {
        // A △ ∅ = A
        $this->assertEqualsCanonicalizing($a, \iterator_to_array(Set::symmetricDifference($a, [])));
    }

    // -- Cross-operation properties (sets and multisets) --

    /**
     * @dataProvider dataProviderForAllPairs
     */
    public function testSymmetricDifferenceEqualsUnionOfDifferences(array $a, array $b): void
    {
        // A △ B = (A \ B) ∪ (B \ A)
        $symDiff = \iterator_to_array(Set::symmetricDifference($a, $b));
        $diffUnion = \iterator_to_array(Set::union(
            Set::difference($a, $b),
            Set::difference($b, $a),
        ));
        $this->assertEqualsCanonicalizing($symDiff, $diffUnion);
    }

    /**
     * @dataProvider dataProviderForAllPairs
     */
    public function testIntersectionViaDifference(array $a, array $b): void
    {
        // A ∩ B = A \ (A \ B)
        $inter = \iterator_to_array(Set::intersection($a, $b));
        $viaDiff = \iterator_to_array(Set::difference($a, Set::difference($a, $b)));
        $this->assertEqualsCanonicalizing($inter, $viaDiff);
    }

    // -- Cross-operation properties (proper sets only) --

    /**
     * @dataProvider dataProviderForSetPairs
     */
    public function testUnionEqualsDisjointPartition(array $a, array $b): void
    {
        // A ∪ B = (A \ B) ∪ (A ∩ B) ∪ (B \ A)
        $union = \iterator_to_array(Set::union($a, $b));
        $partition = \iterator_to_array(Set::union(
            Set::difference($a, $b),
            Set::intersection($a, $b),
            Set::difference($b, $a),
        ));
        $this->assertEqualsCanonicalizing($union, $partition);
    }

    /**
     * @dataProvider dataProviderForSetPairs
     */
    public function testDifferenceAndIntersectionAreDisjoint(array $a, array $b): void
    {
        // (A \ B) ∩ (A ∩ B) = ∅
        $disjoint = \iterator_to_array(Set::intersection(
            Set::difference($a, $b),
            Set::intersection($a, $b),
        ));
        $this->assertEqualsCanonicalizing([], $disjoint);
    }

    /**
     * @dataProvider dataProviderForSetPairs
     */
    public function testSymmetricDifferenceAndIntersectionAreDisjoint(array $a, array $b): void
    {
        // (A △ B) ∩ (A ∩ B) = ∅
        $disjoint = \iterator_to_array(Set::intersection(
            Set::symmetricDifference($a, $b),
            Set::intersection($a, $b),
        ));
        $this->assertEqualsCanonicalizing([], $disjoint);
    }

    // -- 3-operand properties (sets and multisets) --

    /**
     * @dataProvider dataProviderForAllTriples
     */
    public function testIntersectionAssociativity(array $a, array $b, array $c): void
    {
        // (A ∩ B) ∩ C = A ∩ (B ∩ C)
        $lhs = \iterator_to_array(Set::intersection(Set::intersection($a, $b), $c));
        $rhs = \iterator_to_array(Set::intersection($a, Set::intersection($b, $c)));
        $this->assertEqualsCanonicalizing($lhs, $rhs);
    }

    /**
     * @dataProvider dataProviderForAllTriples
     */
    public function testUnionAssociativity(array $a, array $b, array $c): void
    {
        // (A ∪ B) ∪ C = A ∪ (B ∪ C)
        $lhs = \iterator_to_array(Set::union(Set::union($a, $b), $c));
        $rhs = \iterator_to_array(Set::union($a, Set::union($b, $c)));
        $this->assertEqualsCanonicalizing($lhs, $rhs);
    }

    // -- 3-operand properties (proper sets only) --

    /**
     * @dataProvider dataProviderForSetTriples
     */
    public function testSymmetricDifferenceAssociativity(array $a, array $b, array $c): void
    {
        // (A △ B) △ C = A △ (B △ C)
        $lhs = \iterator_to_array(Set::symmetricDifference(Set::symmetricDifference($a, $b), $c));
        $rhs = \iterator_to_array(Set::symmetricDifference($a, Set::symmetricDifference($b, $c)));
        $this->assertEqualsCanonicalizing($lhs, $rhs);
    }

    /**
     * @dataProvider dataProviderForSetTriples
     */
    public function testIntersectionDistributesOverUnion(array $a, array $b, array $c): void
    {
        // A ∩ (B ∪ C) = (A ∩ B) ∪ (A ∩ C)
        $lhs = \iterator_to_array(Set::intersection($a, Set::union($b, $c)));
        $rhs = \iterator_to_array(Set::union(Set::intersection($a, $b), Set::intersection($a, $c)));
        $this->assertEqualsCanonicalizing($lhs, $rhs);
    }

    /**
     * @dataProvider dataProviderForSetTriples
     */
    public function testUnionDistributesOverIntersection(array $a, array $b, array $c): void
    {
        // A ∪ (B ∩ C) = (A ∪ B) ∩ (A ∪ C)
        $lhs = \iterator_to_array(Set::union($a, Set::intersection($b, $c)));
        $rhs = \iterator_to_array(Set::intersection(Set::union($a, $b), Set::union($a, $c)));
        $this->assertEqualsCanonicalizing($lhs, $rhs);
    }

    /**
     * @dataProvider dataProviderForSetTriples
     */
    public function testDeMorganDifferenceOverUnion(array $a, array $b, array $c): void
    {
        // A \ (B ∪ C) = (A \ B) ∩ (A \ C)
        $lhs = \iterator_to_array(Set::difference($a, Set::union($b, $c)));
        $rhs = \iterator_to_array(Set::intersection(Set::difference($a, $b), Set::difference($a, $c)));
        $this->assertEqualsCanonicalizing($lhs, $rhs);
    }

    /**
     * @dataProvider dataProviderForSetTriples
     */
    public function testDeMorganDifferenceOverIntersection(array $a, array $b, array $c): void
    {
        // A \ (B ∩ C) = (A \ B) ∪ (A \ C)
        $lhs = \iterator_to_array(Set::difference($a, Set::intersection($b, $c)));
        $rhs = \iterator_to_array(Set::union(Set::difference($a, $b), Set::difference($a, $c)));
        $this->assertEqualsCanonicalizing($lhs, $rhs);
    }
}
