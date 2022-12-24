<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;

class ReduceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test testSum
     */
    public function testSum(): void
    {
        $input = [1, 2, 3, 4, 5];

        $sum = function ($carry, $item) {
            return $carry + $item;
        };

        $result = Single::reduce($input, $sum, 0);

        $this->assertEquals(array_reduce($input, $sum), $result);
        $this->assertEquals(15, $result);
    }

    /**
     * @test testProduct
     */
    public function testProduct(): void
    {
        $input = [1, 2, 3, 4, 5];

        $product = function ($carry, $item) {
            return $carry * $item;
        };

        $result = Single::reduce($input, $product, 1);

        $this->assertEquals(array_reduce($input, $product, 1), $result);
        $this->assertEquals(120, $result);
    }

    /**
     * @test testNestedArrayAccess
     * @throws \Exception
     */
    public function testNestedArrayAccess(): void
    {
        $source = ['a' => ['b' => ['c' => 22]]];

        $implicitDiver = function (?array $carry, string $key) {
            return $carry[$key] ?? null;
        };

        $result = Single::reduce(['a', 'b', 'c'], $implicitDiver, $source);
        $this->assertEquals(22, $result);

        $result = Single::reduce(['bad_key', 'b', 'c'], $implicitDiver, $source);
        $this->assertNull($result);

        $explicitDiver = function (?array $carry, string $key) {
            if ($carry === null || !isset($carry[$key])) {
                throw new \Exception("key '{$key}' not found");
            }
            return $carry[$key];
        };

        $result = Single::reduce(['a', 'b', 'c'], $explicitDiver, $source);
        $this->assertEquals(22, $result);

        try {
            Single::reduce(['a', 'bad_key', 'c'], $explicitDiver, $source);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals("key 'bad_key' not found", $e->getMessage());
        }
    }
}
