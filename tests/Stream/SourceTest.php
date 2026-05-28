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

    public static function dataProviderForSourceCounts(): array
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

    public static function dataProviderForSourceArray(): array
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

    public static function dataProviderForStreamOfRandomChoice(): array
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

    public static function dataProviderForStreamOfCoinFlips(): array
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

    public static function dataProviderForStreamOfRandomNumbers(): array
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

    public static function dataProviderForStreamOfRandomPercentage(): array
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

    public static function dataProviderForStreamOfRockPaperScissors(): array
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
     * @test stream of coin flips with seeded engine
     */
    public function testStreamOfCoinFlipsWithSeededEngine(): void
    {
        // Given
        $stream = Stream::ofCoinFlips(5, new \Random\Engine\Mt19937(42));

        // When
        $actual = $stream->toArray();

        // Then
        $this->assertEquals([0, 1, 0, 0, 0], $actual);
    }

    /**
     * @test stream of random choice with seeded engine
     */
    public function testStreamOfRandomChoiceWithSeededEngine(): void
    {
        // Given
        $stream = Stream::ofRandomChoice(['a', 'b', 'c', 'd'], 5, new \Random\Engine\Mt19937(42));

        // When
        $actual = $stream->toArray();

        // Then
        $this->assertEquals(['c', 'd', 'a', 'c', 'c'], $actual);
    }

    /**
     * @test stream of random numbers with seeded engine
     */
    public function testStreamOfRandomNumbersWithSeededEngine(): void
    {
        // Given
        $stream = Stream::ofRandomNumbers(1, 100, 5, new \Random\Engine\Mt19937(42));

        // When
        $actual = $stream->toArray();

        // Then
        $this->assertEquals([43, 68, 77, 15, 27], $actual);
    }

    /**
     * @test stream of random percentage with seeded engine
     */
    public function testStreamOfRandomPercentageWithSeededEngine(): void
    {
        // Given
        $stream = Stream::ofRandomPercentage(3, new \Random\Engine\Mt19937(42));

        // When
        $actual = $stream->toArray();

        // Then
        $this->assertCount(3, $actual);
        $this->assertEqualsWithDelta(0.59308596857569, $actual[0], 1e-10);
        $this->assertEqualsWithDelta(0.36686957578674, $actual[1], 1e-10);
        $this->assertEqualsWithDelta(0.55938199522532, $actual[2], 1e-10);
    }

    /**
     * @test stream of rock-paper-scissors with seeded engine
     */
    public function testStreamOfRockPaperScissorsWithSeededEngine(): void
    {
        // Given
        $stream = Stream::ofRockPaperScissors(5, new \Random\Engine\Mt19937(42));

        // When
        $actual = $stream->toArray();

        // Then
        $this->assertEquals(['rock', 'scissors', 'paper', 'paper', 'rock'], $actual);
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

    public static function dataProviderForRangeIntsDefaultStep(): array
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

    public static function dataProviderForRangeFloatsDefaultStep(): array
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

    public static function dataProviderForRangeIntsCustomStep(): array
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

    public static function dataProviderForRangeFloatsCustomStep(): array
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
     * @test         ofRange invalid type arguments
     * @dataProvider dataProviderForRangeInvalidTypeArguments
     * @param mixed $start
     * @param mixed $end
     * @param mixed $step
     */
    public function testOfRangeInvalidTypeArguments($start, $end, $step): void
    {
        // Then
        $this->expectException(\TypeError::class);

        // When
        $stream = Stream::ofRange($start, $end, $step);
    }

    public static function dataProviderForRangeInvalidTypeArguments(): array
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
        ];
    }

    /**
     * @test         ofRange invalid arguments
     * @dataProvider dataProviderForRangeInvalidArguments
     * @param int|float|string $start
     * @param int|float|string $end
     * @param int|float $step
     */
    public function testOfRangeInvalidArguments(int|float|string $start, int|float|string $end, int|float $step): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::ofRange($start, $end, $step)->toArray();
    }

    public static function dataProviderForRangeInvalidArguments(): array
    {
        return [
            [
                'a',
                4,
                2,
            ],
            // Conflicting direction
            [
                1,
                5,
                -1,
            ],
            // Step strictly greater than span
            [
                1,
                5,
                10,
            ],
            // Zero step
            [
                1,
                5,
                0,
            ],
        ];
    }

    /**
     * @test         ofRange non-finite operands throw
     * @dataProvider dataProviderForRangeNonFiniteArguments
     * @param        int|float $start
     * @param        int|float $end
     * @param        int|float $step
     */
    public function testOfRangeNonFiniteArguments(int|float $start, int|float $end, int|float $step): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::ofRange($start, $end, $step)->toArray();
    }

    public static function dataProviderForRangeNonFiniteArguments(): array
    {
        return [
            'INF start'  => [\INF, 5, 1],
            '-INF start' => [-\INF, 5, 1],
            'NAN start'  => [\NAN, 5, 1],
            'INF end'    => [1, \INF, 1],
            '-INF end'   => [1, -\INF, 1],
            'NAN end'    => [1, \NAN, 1],
            'INF step'   => [1, 5, \INF],
            'NAN step'   => [1, 5, \NAN],
        ];
    }

    /**
     * @test ofRange with exponent-string operands that overflow to INF rejects cleanly (no hang)
     */
    public function testOfRangeOverflowExponentStringRejects(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::ofRange('1e309', '1e310')->toArray();
    }

    /**
     * @test ofRange numeric-string inputs coerce to ints
     */
    public function testOfRangeNumericStringIntCoercion(): void
    {
        // Given/When
        $result = Stream::ofRange('1', '5')->toArray();

        // Then
        $this->assertSame([1, 2, 3, 4, 5], $result);
    }

    /**
     * @test ofRange numeric-string float inputs coerce to floats
     */
    public function testOfRangeNumericStringFloatCoercion(): void
    {
        // Given/When
        $result = Stream::ofRange('1.0', '5.0')->toArray();

        // Then
        $this->assertSame([1.0, 2.0, 3.0, 4.0, 5.0], $result);
    }

    /**
     * @test ofRange leading-zero numeric strings coerce to ints
     */
    public function testOfRangeLeadingZeroStringsCoerce(): void
    {
        // Given/When
        $result = Stream::ofRange('01', '05')->toArray();

        // Then
        $this->assertSame([1, 2, 3, 4, 5], $result);
    }

    /**
     * @test ofRange mixed int and float strings yields float-stepped range
     */
    public function testOfRangeMixedStringIntFloatCoercion(): void
    {
        // Given/When
        $result = Stream::ofRange('1', '5.5', 1.5)->toArray();

        // Then
        $this->assertEqualsWithDelta([1.0, 2.5, 4.0, 5.5], $result, 0.0001);
    }

    /**
     * @test ofRange exponent notation coerces to floats
     */
    public function testOfRangeExponentNotationCoerces(): void
    {
        // Given/When
        $result = Stream::ofRange('1e3', '1e5', 1000)->toArray();

        // Then
        $this->assertSame([1000.0, 2000.0, 3000.0, 4000.0, 5000.0, 6000.0, 7000.0, 8000.0, 9000.0, 10000.0,
            11000.0, 12000.0, 13000.0, 14000.0, 15000.0, 16000.0, 17000.0, 18000.0, 19000.0, 20000.0,
            21000.0, 22000.0, 23000.0, 24000.0, 25000.0, 26000.0, 27000.0, 28000.0, 29000.0, 30000.0,
            31000.0, 32000.0, 33000.0, 34000.0, 35000.0, 36000.0, 37000.0, 38000.0, 39000.0, 40000.0,
            41000.0, 42000.0, 43000.0, 44000.0, 45000.0, 46000.0, 47000.0, 48000.0, 49000.0, 50000.0,
            51000.0, 52000.0, 53000.0, 54000.0, 55000.0, 56000.0, 57000.0, 58000.0, 59000.0, 60000.0,
            61000.0, 62000.0, 63000.0, 64000.0, 65000.0, 66000.0, 67000.0, 68000.0, 69000.0, 70000.0,
            71000.0, 72000.0, 73000.0, 74000.0, 75000.0, 76000.0, 77000.0, 78000.0, 79000.0, 80000.0,
            81000.0, 82000.0, 83000.0, 84000.0, 85000.0, 86000.0, 87000.0, 88000.0, 89000.0, 90000.0,
            91000.0, 92000.0, 93000.0, 94000.0, 95000.0, 96000.0, 97000.0, 98000.0, 99000.0, 100000.0], $result);
    }

    /**
     * @test ofRange real int and float inputs pass through
     */
    public function testOfRangeRealNumericInputsPassThrough(): void
    {
        // Given/When
        $resultInt   = Stream::ofRange(1, 5)->toArray();
        $resultFloat = Stream::ofRange(1.0, 5.0)->toArray();

        // Then
        $this->assertSame([1, 2, 3, 4, 5], $resultInt);
        $this->assertSame([1.0, 2.0, 3.0, 4.0, 5.0], $resultFloat);
    }

    /**
     * @test ofRange ascending descending negative-step matrix (backward-compat)
     */
    public function testOfRangeBackwardCompatMatrix(): void
    {
        $this->assertSame([1, 2, 3, 4, 5], Stream::ofRange(1, 5, 1)->toArray());
        $this->assertSame([5, 4, 3, 2, 1], Stream::ofRange(5, 1, 1)->toArray());
        $this->assertSame([5, 4, 3, 2, 1], Stream::ofRange(5, 1, -1)->toArray());
        $this->assertSame([1], Stream::ofRange(1, 1, 1)->toArray());
    }

    /**
     * @test ofRange integer-valued float step on int operands keeps int output (matches native range)
     */
    public function testOfRangeIntegerValuedFloatStepKeepsIntOutput(): void
    {
        // Native PHP: range(1, 5, 1.0) → ints. Stream::ofRange must match.
        $this->assertSame([1, 2, 3, 4, 5], Stream::ofRange(1, 5, 1.0)->toArray());
        // Numeric-string endpoints with int-valued float step also keep int output.
        $this->assertSame([1, 2, 3, 4, 5], Stream::ofRange('1', '5', 1.0)->toArray());
    }

    /**
     * @test ofRange float range does not yield past $end despite IEEE 754 rounding
     */
    public function testOfRangeFloatDoesNotOvershoot(): void
    {
        // Native \range(0.3, 0.9, 0.2) returns [0.3, 0.5, 0.7].
        $result = Stream::ofRange(0.3, 0.9, 0.2)->toArray();

        foreach ($result as $v) {
            $this->assertLessThanOrEqual(0.9, $v);
        }
    }

    /**
     * @test ofRange integer span from PHP_INT_MIN to 0 iterates lazily
     */
    public function testOfRangeIntegerSpanFromPhpIntMinToZeroIteratesLazily(): void
    {
        // Span overflows int subtraction; the implementation must iterate lazily
        // so callers can take a finite prefix via downstream limit().
        $first5 = Stream::ofRange(\PHP_INT_MIN, 0, 1)->limit(5)->toArray();

        $this->assertSame(
            [\PHP_INT_MIN, \PHP_INT_MIN + 1, \PHP_INT_MIN + 2, \PHP_INT_MIN + 3, \PHP_INT_MIN + 4],
            $first5
        );
    }

    /**
     * @test ofRange laziness — large end works with downstream limit
     */
    public function testOfRangeIsLazyWithDownstreamLimit(): void
    {
        // Given
        $stream = Stream::ofRange(1, \PHP_INT_MAX);

        // When
        $first5 = $stream->limit(5)->toArray();

        // Then
        $this->assertSame([1, 2, 3, 4, 5], $first5);
    }

    /**
     * @test ofRange leading-whitespace numeric strings coerce to int (matches `+ 0` semantics)
     */
    public function testOfRangeLeadingWhitespaceIntCoercion(): void
    {
        // Given
        // is_numeric() accepts leading whitespace (e.g. " 1"), and PHP arithmetic
        // coercion (`" 1" + 0`) yields int. The lexical int-bound check must trim
        // whitespace before parsing, otherwise leading whitespace inflates the
        // digit-string length past the int-range limit (19 chars) and the result
        // is incorrectly promoted to float for near-PHP_INT_MAX values.

        // When — short value (does not trip the length check, included for coverage)
        $shortResult = Stream::ofRange(' 1', ' 5')->toArray();

        // Then
        $this->assertSame([1, 2, 3, 4, 5], $shortResult);

        // When — value at PHP_INT_MAX with leading whitespace; without trimming,
        // the leading space pushes the digit-string length to 20 and promotes to float
        $maxString = ' ' . \PHP_INT_MAX;
        $result = Stream::ofRange($maxString, $maxString)->toArray();

        // Then — single-element range yields the int value, not a float
        $this->assertCount(1, $result);
        $this->assertIsInt($result[0]);
        $this->assertSame(\PHP_INT_MAX, $result[0]);

        // When — trailing whitespace at PHP_INT_MAX; is_numeric and PHP arithmetic
        // coercion both tolerate trailing whitespace, so we must trim both ends
        $trailingMaxString = \PHP_INT_MAX . ' ';
        $trailingResult = Stream::ofRange($trailingMaxString, $trailingMaxString)->toArray();

        // Then
        $this->assertCount(1, $trailingResult);
        $this->assertIsInt($trailingResult[0]);
        $this->assertSame(\PHP_INT_MAX, $trailingResult[0]);
    }

    /**
     * @test ofRange numeric string exceeding PHP_INT_MAX promotes to float (matches `+ 0` semantics, not clamped to int)
     */
    public function testOfRangeIntOverflowNumericStringPromotesToFloat(): void
    {
        // Given
        $overflow = '9223372036854775808'; // PHP_INT_MAX + 1, exceeds int range

        // When
        $stream = Stream::ofRange($overflow, $overflow);
        $result = $stream->toArray();

        // Then
        $this->assertCount(1, $result);
        $this->assertIsFloat($result[0]);
        $this->assertEqualsWithDelta((float)$overflow, $result[0], 0.0);
    }

    /**
     * @test ofRange with PHP_INT_MIN step is rejected before iteration
     */
    public function testOfRangePhpIntMinStepRejects(): void
    {
        // Given
        // PHP_INT_MIN can be passed as $step. The Single::range guard must fire
        // through the Stream delegation path too.

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::ofRange(\PHP_INT_MAX, 0, \PHP_INT_MIN)->limit(1)->toArray();
    }

    /**
     * @test ofRange negative numeric string at PHP_INT_MIN coerces to int (`+ 0` semantics)
     */
    public function testOfRangePhpIntMinNumericStringCoercesToInt(): void
    {
        // Given
        $minString = (string)\PHP_INT_MIN;

        // When
        $result = Stream::ofRange($minString, $minString)->toArray();

        // Then
        $this->assertCount(1, $result);
        $this->assertIsInt($result[0]);
        $this->assertSame(\PHP_INT_MIN, $result[0]);
    }

    /**
     * @test ofRange numeric string one below PHP_INT_MIN promotes to float
     */
    public function testOfRangeBelowPhpIntMinNumericStringPromotesToFloat(): void
    {
        // Given
        // PHP_INT_MIN = -9223372036854775808; one beyond is -9223372036854775809
        $belowMin = '-9223372036854775809';

        // When
        $result = Stream::ofRange($belowMin, $belowMin)->toArray();

        // Then
        $this->assertCount(1, $result);
        $this->assertIsFloat($result[0]);
    }

    /**
     * @test ofRange signed numeric strings (positive sign) coerce correctly
     */
    public function testOfRangePositiveSignedNumericStringCoercesToInt(): void
    {
        // Given/When
        $result = Stream::ofRange('+1', '+5')->toArray();

        // Then
        $this->assertSame([1, 2, 3, 4, 5], $result);
    }

    /**
     * @test ofRange alpha strings still rejected with the existing "must be numeric" error
     */
    public function testOfRangeAlphaStringStillRejected(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/numeric/');

        // When
        Stream::ofRange('a', 'e', 1)->toArray();
    }
}
