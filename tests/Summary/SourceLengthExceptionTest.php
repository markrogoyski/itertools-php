<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Multi;
use IterTools\Summary;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class SourceLengthExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         Summary::same propagates a \LengthException thrown by a source
     * @dataProvider dataProviderForSourceThrowsLengthException
     * @param        iterable ...$iterables
     */
    public function testSamePropagatesSourceLengthException(iterable ...$iterables): void
    {
        // Then
        $this->expectException(\LengthException::class);

        // When
        Summary::same(...$iterables);
    }

    /**
     * @test         Summary::arePermutations propagates a \LengthException thrown by a source
     * @dataProvider dataProviderForSourceThrowsLengthException
     * @param        iterable ...$iterables
     */
    public function testArePermutationsPropagatesSourceLengthException(iterable ...$iterables): void
    {
        // Then
        $this->expectException(\LengthException::class);

        // When
        Summary::arePermutations(...$iterables);
    }

    /**
     * @test         Summary::arePermutationsCoercive propagates a \LengthException thrown by a source
     * @dataProvider dataProviderForSourceThrowsLengthException
     * @param        iterable ...$iterables
     */
    public function testArePermutationsCoercivePropagatesSourceLengthException(iterable ...$iterables): void
    {
        // Then
        $this->expectException(\LengthException::class);

        // When
        Summary::arePermutationsCoercive(...$iterables);
    }

    public static function dataProviderForSourceThrowsLengthException(): array
    {
        $throwingAtStart = static function (): \Generator {
            throw new \LengthException('thrown by source');
            yield;
        };

        $throwingAfterOne = static function (): \Generator {
            yield 1;
            throw new \LengthException('thrown by source');
        };

        $throwingAggregate = static function () use ($throwingAfterOne): \IteratorAggregate {
            return new class ($throwingAfterOne) implements \IteratorAggregate {
                /** @var callable */
                private $factory;

                public function __construct(callable $factory)
                {
                    $this->factory = $factory;
                }

                public function getIterator(): \Iterator
                {
                    return ($this->factory)();
                }
            };
        };

        $nestedZipEqual = static function (): \Generator {
            return Multi::zipEqual([1, 2], [1]);
        };

        return [
            'nested zipEqual of unequal inputs' => [$nestedZipEqual(), [[1, 1]]],
            'nested zipEqual, source second' => [[[1, 1]], $nestedZipEqual()],
            'generator throws on first element' => [$throwingAtStart(), [1]],
            'generator throws on first element, source second' => [[1], $throwingAtStart()],
            'generator throws after first element' => [$throwingAfterOne(), [1, 2]],
            'generator throws after first element, source second' => [[1, 2], $throwingAfterOne()],
            'generator throws after first element, equal length' => [$throwingAfterOne(), [1, 2], [1, 2]],
            'aggregate throws after first element' => [$throwingAggregate(), [1, 2]],
            'both sources throw' => [$throwingAfterOne(), $throwingAfterOne()],
        ];
    }

    /**
     * @test         Summary::same returns false for inputs of different lengths
     * @dataProvider dataProviderForDifferentLengths
     * @param        iterable ...$iterables
     */
    public function testSameDifferentLengthsAreFalse(iterable ...$iterables): void
    {
        // When
        $result = Summary::same(...$iterables);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         Summary::arePermutations returns false for inputs of different lengths
     * @dataProvider dataProviderForDifferentLengths
     * @param        iterable ...$iterables
     */
    public function testArePermutationsDifferentLengthsAreFalse(iterable ...$iterables): void
    {
        // When
        $result = Summary::arePermutations(...$iterables);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         Summary::arePermutationsCoercive returns false for inputs of different lengths
     * @dataProvider dataProviderForDifferentLengths
     * @param        iterable ...$iterables
     */
    public function testArePermutationsCoerciveDifferentLengthsAreFalse(iterable ...$iterables): void
    {
        // When
        $result = Summary::arePermutationsCoercive(...$iterables);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForDifferentLengths(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [[1], [1, 1]],
            [[1, 1], [1]],
            [[], [1]],
            [[1], []],
            [[1], $gen([1, 1])],
            [$gen([1, 1]), [1]],
            [$iter([1]), $iter([1, 1])],
            [$iter([1, 1]), $iter([1])],
            [$trav([1]), $trav([1, 1])],
            [$trav([1, 1]), $trav([1])],
            [[1, 1], [1, 1], [1]],
            [[1], [1, 1], [1, 1]],
            [$gen([1]), [1, 1], $iter([1, 1]), $trav([1, 1])],
        ];
    }
}
