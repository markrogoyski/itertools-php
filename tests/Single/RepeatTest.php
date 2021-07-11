<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;

class RepeatTest extends \PHPUnit\Framework\TestCase
{
    private const REPETITIONS = 5;

    /**
     * @test         repeat integer
     * @dataProvider dataProviderForInteger
     * @param        int   $integer
     * @param        int[] $expected
     */
    public function testRepeatInteger(int $integer, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::repeat($integer, self::REPETITIONS) as $item) {
            $result[] = $item;
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
    public function testRepeatFloat(float $float, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::repeat($float, self::REPETITIONS) as $item) {
            $result[] = $item;
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
    public function testRepeatNan(): void
    {
        // Given
        $result = [];
        // When
        foreach (Single::repeat(\NAN, self::REPETITIONS) as $item) {
            $result[] = $item;
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
    public function testRepeatNull(): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::repeat(null, self::REPETITIONS) as $item) {
            $result[] = $item;
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
    public function testRepeatBoolean(bool $boolean, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::repeat($boolean, self::REPETITIONS) as $item) {
            $result[] = $item;
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
    public function testRepeatString(string $string, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::repeat($string, self::REPETITIONS) as $item) {
            $result[] = $item;
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
    public function testRepeatArray(): void
    {
        // Given
        $array    = [1, 2, 3, 4, 5];
        $result   = [];
        $expected = [[1, 2, 3, 4, 5], [1, 2, 3, 4, 5], [1, 2, 3, 4, 5], [1, 2, 3, 4, 5], [1, 2, 3, 4, 5]];

        // And
        $count = 0;

        // When
        foreach (Single::repeat($array, self::REPETITIONS) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle empty array
     */
    public function testCycleEmptyArray(): void
    {
        // Given
        $array    = [];
        $result   = [];
        $expected = [[], [], [], [], []];

        // When
        foreach (Single::repeat($array, self::REPETITIONS) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test cycle object
     */
    public function testRepeatObject(): void
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

        // When
        foreach (Single::repeat($object, self::REPETITIONS) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         repetitions
     * @dataProvider dataProviderForRepetitions
     * @param        int   $item
     * @param        int   $repetitions
     * @param        array $expected
     */
    public function testRepetitions(int $item, int $repetitions, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::repeat($item, $repetitions) as $repetition) {
            $result[] = $repetition;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForRepetitions(): array
    {
        return [
            [0, 0, []],
            [1, 1, [1]],
            [2, 2, [2, 2]],
            [3, 3, [3, 3, 3]],
            [4, 4, [4, 4, 4, 4]],
            [10, 10, [10, 10, 10, 10, 10, 10, 10, 10, 10, 10]],
        ];
    }

    /**
     * @test negative number of repetitions is an error
     */
    public function testRepetitionsErrorNegativeNumber(): void
    {
        // Given
        $item = 1;
        $repetitions = -1;

        // Then
        $this->expectException(\RangeException::class);

        // When
        foreach (Single::repeat($item, $repetitions) as $_) {
            break;
        }

        // Then
        $this->fail('Expected an exception to be thrown.');
    }
}
