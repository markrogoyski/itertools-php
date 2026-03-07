<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

class SetFixtureTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForIsSubsetTrue
     */
    public function testIsSubsetTrue(array $a, array $b): void
    {
        $this->assertTrue(SetFixture::isSubset($a, $b));
    }

    public static function dataProviderForIsSubsetTrue(): array
    {
        return [
            // empty is subset of anything
            [[], []],
            [[], [1]],
            [[], [1, 2, 3]],
            // equal sets
            [[1], [1]],
            [[1, 2, 3], [1, 2, 3]],
            // proper subsets
            [[1], [1, 2, 3]],
            [[1, 3], [1, 2, 3]],
            // multiset: count respected
            [[1, 1], [1, 1, 2]],
            [[2, 2, 2], [2, 2, 2]],
            // order doesn't matter
            [[3, 1, 2], [1, 2, 3]],
            // mixed types: strict means '1' != 1
            [['1'], ['1', 1]],
            [[1], ['1', 1]],
            // arrays as elements
            [[[1, 2]], [[1, 2], [3, 4]]],
            // null
            [[null], [null, 1]],
            [[null, null], [null, null]],
        ];
    }

    /**
     * @dataProvider dataProviderForIsSubsetFalse
     */
    public function testIsSubsetFalse(array $a, array $b): void
    {
        $this->assertFalse(SetFixture::isSubset($a, $b));
    }

    public static function dataProviderForIsSubsetFalse(): array
    {
        return [
            // non-empty not subset of empty
            [[1], []],
            // element not present
            [[4], [1, 2, 3]],
            [[1, 4], [1, 2, 3]],
            // multiset: not enough copies
            [[1, 1], [1]],
            [[1, 1, 1], [1, 1]],
            [[2, 2, 2], [2, 2]],
            // strict types: '1' is not 1
            [['1'], [1]],
            [[1], ['1']],
            // arrays as elements: different content
            [[[1, 2]], [[1, 3], [3, 4]]],
            // null count
            [[null, null], [null]],
        ];
    }
}
