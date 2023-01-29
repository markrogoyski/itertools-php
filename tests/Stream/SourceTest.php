<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Random;
use IterTools\Stream;
use IterTools\Tests\Fixture;

class SourceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test count of empty stream
     */
    public function testStreamOfEmptyCount(): void
    {
        // Given
        $stream = Stream::ofEmpty();

        // When
        $count = $stream->toCount();

        // Then
        $this->assertEquals(0, $count);
    }

    /**
     * @test empty stream to array
     */
    public function testStreamOfEmptyToArray(): void
    {
        // Given
        $stream = Stream::ofEmpty();

        // When
        $array = $stream->toArray();

        // Then
        $this->assertEmpty($array);
    }

    public function testStreamOfEmptyAllowsContinuation(): void
    {
        // Given
        $stream   = Stream::ofEmpty();
        $expected = [5, 5, 10];

        // When
        $array = $stream->chainWith([5, 5, 10])
            ->toArray();

        // Then
        $this->assertEquals($expected, $array);
    }

    /**
     * @test stream of data count
     * @dataProvider dataProviderForSourceCounts
     */
    public function testStreamOfCount(iterable $iterable, int $expectedCount): void
    {
        // Given
        $stream = Stream::of($iterable);

        // When
        $count = $stream->toCount();

        // Then
        $this->assertEquals($expectedCount, $count);
    }

    public function dataProviderForSourceCounts(): array
    {
        return [
            [
                [],
                0,
            ],
            [
                Fixture\GeneratorFixture::getGenerator([]),
                0,
            ],
            [
                new Fixture\ArrayIteratorFixture([]),
                0,
            ],
            [
                new Fixture\IteratorAggregateFixture([]),
                0,
            ],
            [
                [5],
                1,
            ],
            [
                Fixture\GeneratorFixture::getGenerator([5]),
                1,
            ],
            [
                new Fixture\ArrayIteratorFixture([5]),
                1,
            ],
            [
                new Fixture\IteratorAggregateFixture([5]),
                1,
            ],
            [
                [1, 2, 3],
                3,
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                3,
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                3,
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                3,
            ],
        ];
    }

    /**
     * @test stream of data array
     * @dataProvider dataProviderForSourceArray
     */
    public function testStreamOfArray(iterable $iterable, array $expected): void
    {
        // Given
        $stream = Stream::of($iterable);

        // When
        $array = $stream->toArray();

        // Then
        $this->assertEquals($expected, $array);
    }

    public function dataProviderForSourceArray(): array
    {
        return [
            [
                [],
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([]),
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([]),
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([]),
                [],
            ],
            [
                [5],
                [5]
            ],
            [
                Fixture\GeneratorFixture::getGenerator([5]),
                [5]
            ],
            [
                new Fixture\ArrayIteratorFixture([5]),
                [5]
            ],
            [
                new Fixture\IteratorAggregateFixture([5]),
                [5]
            ],
            [
                [1, 2, 3],
                [1, 2, 3],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                [1, 2, 3],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                [1, 2, 3],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                [1, 2, 3],
            ],
        ];
    }

    /**
     * @test stream of random choice array
     * @dataProvider dataProviderForStreamOfRandomChoice
     */
    public function testStreamOfRandomChoice(array $items, int $repetitions): void
    {
        // Given
        $stream = Stream::ofRandomChoice($items, $repetitions);

        // When
        $actual = $stream->toArray();

        // Then
        $this->assertCount($repetitions, $actual);

        // And
        foreach ($actual as $choice) {
            $this->assertTrue(\in_array($choice, $items));
        }
    }

    public function dataProviderForStreamOfRandomChoice(): array
    {
        return [
            [[-5, -1, 0, 1, 7, 4, 10, 8847], 0],
            [[-5, -1, 0, 1, 7, 4, 10, 8847], 1],
            [[-5, -1, 0, 1, 7, 4, 10, 8847], 2],
            [[-5, -1, 0, 1, 7, 4, 10, 8847], 10],
            [[-5, -1, 0, 1, 7, 4, 10, 8847], 50],
            [[-5, -1, 0, 1, 7, 4, 10, 8847], 9873],
            [[-5.0, -1.2, 0.0, 1.2, 7.65, 4.339, 10.10, 8847.00001, 0.00005], 0],
            [[-5.0, -1.2, 0.0, 1.2, 7.65, 4.339, 10.10, 8847.00001, 0.00005], 1],
            [[-5.0, -1.2, 0.0, 1.2, 7.65, 4.339, 10.10, 8847.00001, 0.00005], 2],
            [[-5.0, -1.2, 0.0, 1.2, 7.65, 4.339, 10.10, 8847.00001, 0.00005], 10],
            [[-5.0, -1.2, 0.0, 1.2, 7.65, 4.339, 10.10, 8847.00001, 0.00005], 50],
            [[-5.0, -1.2, 0.0, 1.2, 7.65, 4.339, 10.10, 8847.00001, 0.00005], 9873],
            [['php', 'go', 'python', 'java', 'c++', 'lisp', 'ruby', 'perl'], 0],
            [['php', 'go', 'python', 'java', 'c++', 'lisp', 'ruby', 'perl'], 1],
            [['php', 'go', 'python', 'java', 'c++', 'lisp', 'ruby', 'perl'], 2],
            [['php', 'go', 'python', 'java', 'c++', 'lisp', 'ruby', 'perl'], 10],
            [['php', 'go', 'python', 'java', 'c++', 'lisp', 'ruby', 'perl'], 50],
            [['php', 'go', 'python', 'java', 'c++', 'lisp', 'ruby', 'perl'], 9873],
        ];
    }

    /**
     * @test stream of random coin flip
     * @dataProvider dataProviderForStreamOfCoinFlips
     */
    public function testStreamOfCoinFlips(int $repetitions): void
    {
        // Given
        $stream = Stream::ofCoinFlips($repetitions);

        // When
        $actual = $stream->toArray();

        // Then
        $this->assertCount($repetitions, $actual);

        // And
        foreach ($actual as $coinFlip) {
            $this->assertIsInt($coinFlip);
            $this->assertThat(
                $coinFlip,
                $this->logicalOr(
                    $this->equalTo(0),
                    $this->equalTo(1)
                )
            );
        }
    }

    public function dataProviderForStreamOfCoinFlips(): array
    {
        return [
            [0],
            [1],
            [2],
            [10],
            [50],
            [9873],
        ];
    }

    /**
     * @test stream of random numbers
     * @dataProvider dataProviderForStreamOfRandomNumbers
     */
    public function testStreamOfRandomNumbers(int $min, int $max, int $repetitions): void
    {
        // Given
        $stream = Stream::ofRandomNumbers($min, $max, $repetitions);

        // When
        $actual = $stream->toArray();

        // Then
        $this->assertCount($repetitions, $actual);

        // And
        foreach ($actual as $number) {
            $this->assertIsInt($number);
            $this->assertTrue($number >= $min);
            $this->assertTrue($number <= $max);
        }
    }

    public function dataProviderForStreamOfRandomNumbers(): array
    {
        return [
            [0, 0, 0],
            [0, 1, 0],
            [0, 10, 0],
            [0, 10, 1],
            [0, 10, 2],
            [0, 10, 10],
            [0, 10, 50],
            [-5, 5, 100],
            [-928739, 9872937492, 9482],
        ];
    }

    /**
     * @test stream of random percentage
     * @dataProvider dataProviderForStreamOfRandomPercentage
     */
    public function testStreamOfRandomPercentage(int $repetitions): void
    {
        // Given
        $stream = Stream::ofRandomPercentage($repetitions);

        // When
        $actual = $stream->toArray();

        // Then
        $this->assertCount($repetitions, $actual);

        // And
        foreach ($actual as $percentage) {
            $this->assertIsFloat($percentage);
            $this->assertTrue($percentage >= 0);
            $this->assertTrue($percentage <= 1);
        }
    }

    public function dataProviderForStreamOfRandomPercentage(): array
    {
        return [
            [0],
            [1],
            [2],
            [10],
            [50],
            [9873],
        ];
    }

    /**
     * @test stream of rock-paper-scissors hands
     * @dataProvider dataProviderForStreamOfRockPaperScissors
     */
    public function testStreamOfRockPaperScissors(int $repetitions): void
    {
        // Given
        $stream = Stream::ofRockPaperScissors($repetitions);

        // When
        $actual = $stream->toArray();

        // Then
        $this->assertCount($repetitions, $actual);

        // And
        foreach ($actual as $rockPaperScissors) {
            $this->assertIsString($rockPaperScissors);
            $this->assertThat(
                $rockPaperScissors,
                $this->logicalOr(
                    $this->equalTo(Random::RPS_ROCK),
                    $this->equalTo(Random::RPS_PAPER),
                    $this->equalTo(Random::RPS_SCISSORS)
                )
            );
        }
    }

    public function dataProviderForStreamOfRockPaperScissors(): array
    {
        return [
            [0],
            [1],
            [2],
            [10],
            [50],
            [9873],
        ];
    }

    /**
     * @test         ofRange ints default step
     * @dataProvider dataProviderForRangeIntsDefaultStep
     * @param int $start
     * @param int $end
     * @param array<int> $expected
     */
    public function testOfRangeIntsDefaultStep(int $start, int $end, array $expected): void
    {
        // Given
        $stream = Stream::ofRange($start, $end);

        // When
        $result = $stream->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForRangeIntsDefaultStep(): array
    {
        return [
            [
                0,
                0,
                [0]
            ],
            [
                0,
                1,
                [0, 1]
            ],
            [
                0,
                10,
                [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
            ],
        ];
    }

    /**
     * @test         ofRange float default step
     * @dataProvider dataProviderForRangeFloatsDefaultStep
     * @param float $start
     * @param float $end
     * @param array<float> $expected
     */
    public function testOfRangeFloatsDefaultStep(float $start, float $end, array $expected): void
    {
        // Given
        $stream = Stream::ofRange($start, $end);

        // When
        $result = $stream->toArray();

        // Then
        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }

    public function dataProviderForRangeFloatsDefaultStep(): array
    {
        return [
            [
                0.0,
                0.0,
                [0.0]
            ],
            [
                0.0,
                1.0,
                [0.0, 1.0]
            ],
            [
                0.0,
                10.0,
                [0.0, 1.0, 2.0, 3.0, 4.0, 5.0, 6.0, 7.0, 8.0, 9.0, 10.0]
            ],
            [
                0.5,
                5.5,
                [0.5, 1.5, 2.5, 3.5, 4.5, 5.5]
            ],
        ];
    }

    /**
     * @test         ofRange ints custom step
     * @dataProvider dataProviderForRangeIntsCustomStep
     * @param int $start
     * @param int $end
     * @param int $step
     * @param array<int> $expected
     */
    public function testOfRangeIntsCustomStep(int $start, int $end, int $step, array $expected): void
    {
        // Given
        $stream = Stream::ofRange($start, $end, $step);

        // When
        $result = $stream->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForRangeIntsCustomStep(): array
    {
        return [
            [
                0,
                0,
                2,
                [0]
            ],
            [
                0,
                4,
                2,
                [0, 2, 4]
            ],
            [
                0,
                10,
                3,
                [0, 3, 6, 9]
            ],
        ];
    }

    /**
     * @test         ofRange floats custom step
     * @dataProvider dataProviderForRangeFloatsCustomStep
     * @param float $start
     * @param float $end
     * @param float $step
     * @param array<float> $expected
     */
    public function testOfRangeFloatsCustomStep(float $start, float $end, float $step, array $expected): void
    {
        // Given
        $stream = Stream::ofRange($start, $end, $step);

        // When
        $result = $stream->toArray();

        // Then
        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }

    public function dataProviderForRangeFloatsCustomStep(): array
    {
        return [
            [
                0.0,
                0.0,
                2.0,
                [0.0]
            ],
            [
                0.0,
                4.0,
                2.0,
                [0.0, 2.0, 4.0]
            ],
            [
                0.0,
                10.0,
                3.0,
                [0.0, 3.0, 6.0, 9.0]
            ],
            [
                0.0,
                5.0,
                1.2,
                [0.0, 1.2, 2.4, 3.6, 4.8]
            ],
        ];
    }

    /**
     * @test         ofRange invalid arguments
     * @dataProvider dataProviderForRangeInvalidArguments
     * @param mixed $start
     * @param mixed $end
     * @param float $step
     */
    public function testOfRangeInvalidArguments($start, $end, $step): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        $stream = Stream::ofRange($start, $end, $step);
    }

    public function dataProviderForRangeInvalidArguments(): array
    {
        return [
            [
                [0],
                4,
                2,
            ],
            [
                0,
                [4],
                2,
            ],
            [
                0,
                4,
                [2],
            ],
            [
                'a',
                4,
                2,
            ],
        ];
    }
}
