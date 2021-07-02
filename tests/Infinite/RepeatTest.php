<?php

declare(strict_types=1);

namespace IterTools\Tests\Infinite;

use IterTools\Infinite;
use IterTools\Tests\Fixture;

class RepeatTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         repeat integer
     * @dataProvider dataProviderForInteger
     * @param        int   $integer
     * @param        int[] $expected
     */
    public function testRepeatInteger(int $integer, array $expected)
    {
        // Given
        $result = [];

        // And
        $count = 0;

        // When
        foreach (Infinite::repeat($integer) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 5) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForInteger(): array
    {
        return [
            [0, [0, 0, 0, 0, 0]],
            [1, [1, 1, 1, 1, 1]],
            [87384, [87384, 87384, 87384, 87384, 87384]],
            [-827, [-827, -827, -827, -827, -827]],
        ];
    }

    /**
     * @test         repeat float
     * @dataProvider dataProviderForFloat
     * @param        float   $float
     * @param        float[] $expected
     */
    public function testRepeatFloat(float $float, array $expected)
    {
        // Given
        $result = [];

        // And
        $count = 0;

        // When
        foreach (Infinite::repeat($float) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 5) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForFloat(): array
    {
        return [
            [0.0, [0.0, 0.0, 0.0, 0.0, 0.0]],
            [1.5, [1.5, 1.5, 1.5, 1.5, 1.5]],
            [87384.94782, [87384.94782, 87384.94782, 87384.94782, 87384.94782, 87384.94782]],
            [-827.003, [-827.003, -827.003, -827.003, -827.003, -827.003]],
            [\INF, [\INF, \INF, \INF, \INF, \INF]],
            [-\INF, [-\INF, -\INF, -\INF, -\INF, -\INF]],
        ];
    }

    /**
     * @test repeat NAN
     */
    public function testRepeatNan()
    {
        // Given
        $result = [];

        // And
        $count = 0;

        // When
        foreach (Infinite::repeat(\NAN) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 5) {
                break;
            }
        }

        // Then
        $this->assertCount(5, $result);
        foreach ($result as $item) {
            $this->assertNan($item);
        }
    }

    /**
     * @test repeat NULL
     */
    public function testRepeatNull()
    {
        // Given
        $result = [];

        // And
        $count = 0;

        // When
        foreach (Infinite::repeat(null) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 5) {
                break;
            }
        }

        // Then
        $this->assertCount(5, $result);
        foreach ($result as $item) {
            $this->assertNull($item);
        }
    }

    /**
     * @test         repeat boolean
     * @dataProvider dataProviderForBoolean
     * @param        bool   $boolean
     * @param        bool[] $expected
     */
    public function testRepeatBoolean(bool $boolean, array $expected)
    {
        // Given
        $result = [];

        // And
        $count = 0;

        // When
        foreach (Infinite::repeat($boolean) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 5) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForBoolean(): array
    {
        return [
            [true, [true, true, true, true, true]],
            [false, [false, false, false, false, false]],
        ];
    }

    /**
     * @test         repeat string
     * @dataProvider dataProviderForString
     * @param        string   $string
     * @param        string[] $expected
     */
    public function testRepeatString(string $string, array $expected)
    {
        // Given
        $result = [];

        // And
        $count = 0;

        // When
        foreach (Infinite::repeat($string) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 5) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForString(): array
    {
        return [
            ['', ['', '', '', '', '']],
            ['a', ['a', 'a', 'a', 'a', 'a']],
            ['IterTools PHP', ['IterTools PHP', 'IterTools PHP', 'IterTools PHP', 'IterTools PHP', 'IterTools PHP']],
            ['日本語もできます。', ['日本語もできます。', '日本語もできます。', '日本語もできます。', '日本語もできます。', '日本語もできます。']],
        ];
    }

    /**
     * @test cycle array
     */
    public function testRepeatArray()
    {
        // Given
        $array    = [1, 2, 3, 4, 5];
        $result   = [];
        $expected = [[1, 2, 3, 4, 5], [1, 2, 3, 4, 5], [1, 2, 3, 4, 5], [1, 2, 3, 4, 5], [1, 2, 3, 4, 5]];

        // And
        $count = 0;

        // When
        foreach (Infinite::repeat($array) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 5) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle empty array
     */
    public function testCycleEmptyArray()
    {
        // Given
        $array    = [];
        $result   = [];
        $expected = [[], [], [], [], []];

        // And
        $count = 0;

        // When
        foreach (Infinite::repeat($array) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 5) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle object
     */
    public function testRepeatObject()
    {
        // Given
        $object = (object) [
            'key1' => 'value1',
            'key2' => 1,
            'key3' => 4.5,
            'key4' => true,
            'key5' => \NAN,
            'key6' => null,
            'key7' => [1, 2, 3, 4, 5],
            'key8' => (object) ['key' => 'value'],
        ];
        $result   = [];
        $expected = [$object, $object, $object, $object, $object];

        // And
        $count = 0;

        // When
        foreach (Infinite::repeat($object) as $item) {
            $result[] = $item;
            $count++;
            if ($count === 5) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }
}
