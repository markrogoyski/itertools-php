<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class PeekTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArray
     * @param array $input
     * @param callable(iterable $input): Stream $leftChainFunc
     * @param callable(Stream $stream): Stream $rightChainFunc
     * @param array $expectedPeeked
     * @param array $expectedResult
     * @return void
     */
    public function testArray(array $input, callable $leftChainFunc, callable $rightChainFunc, array $expectedPeeked, array $expectedResult): void
    {
        // Given
        $stream = $leftChainFunc($input);
        $peeked = [];

        // When
        $stream->peek(static function (iterable $buffer) use (&$peeked) {
            foreach ($buffer as $key => $item) {
                $peeked[$key] = $item;
            }
        });

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $this->assertSame($expectedPeeked, $peeked);
        $this->assertSame($expectedResult, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                [],
                [],
            ],
            [
                [5, 4, 3, 2, 1],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                [5, 4, 3, 2, 1],
                [5, 4, 3, 2, 1],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                [],
                [],
            ],
            [
                [5, 4, 3, 2, 1],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                [5, 4, 3, 2, 1],
                [1, 2, 3, 4, 5],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                [],
                [],
            ],
            [
                [5, 4, 3, 2, 1],
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                [1, 2, 3, 4, 5],
                [1, 2, 3, 4, 5],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith([11, 22, 33, 44, 55]),
                fn (Stream $stream) => $stream
                    ->limit(3),
                [],
                [],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith([11, 22, 33, 44, 55]),
                fn (Stream $stream) => $stream
                    ->limit(3),
                [[1, 11], [2, 22], [3, 33], [4, 44], [5, 55]],
                [[1, 11], [2, 22], [3, 33]],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->sort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1)
                    ->pairwise(),
                [],
                [],
            ],
            [
                [9, 8, 7, 6, 5, 4, 3, 2, 1],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->sort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1)
                    ->pairwise(),
                [1, 3, 5, 7, 9],
                [[2, 4], [4, 6], [6, 8], [8, 10]],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->asort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                ['a' => 1, 'c' => 3],
                ['a' => 2, 'c' => 4],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator $input
     * @param callable(iterable $input): Stream $leftChainFunc
     * @param callable(Stream $stream): Stream $rightChainFunc
     * @param array $expectedPeeked
     * @param array $expectedResult
     * @return void
     */
    public function testGenerators(\Generator $input, callable $leftChainFunc, callable $rightChainFunc, array $expectedPeeked, array $expectedResult): void
    {
        // Given
        $stream = $leftChainFunc($input);
        $peeked = [];

        // When
        $stream->peek(static function (iterable $buffer) use (&$peeked) {
            foreach ($buffer as $key => $item) {
                $peeked[$key] = $item;
            }
        });

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $this->assertSame($expectedPeeked, $peeked);
        $this->assertSame($expectedResult, $result);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = fn ($data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                [],
                [],
            ],
            [
                $gen([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                [5, 4, 3, 2, 1],
                [5, 4, 3, 2, 1],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                [],
                [],
            ],
            [
                $gen([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                [5, 4, 3, 2, 1],
                [1, 2, 3, 4, 5],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                [],
                [],
            ],
            [
                $gen([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                [1, 2, 3, 4, 5],
                [1, 2, 3, 4, 5],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith([11, 22, 33, 44, 55]),
                fn (Stream $stream) => $stream
                    ->limit(3),
                [],
                [],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith([11, 22, 33, 44, 55]),
                fn (Stream $stream) => $stream
                    ->limit(3),
                [[1, 11], [2, 22], [3, 33], [4, 44], [5, 55]],
                [[1, 11], [2, 22], [3, 33]],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->sort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1)
                    ->pairwise(),
                [],
                [],
            ],
            [
                $gen([9, 8, 7, 6, 5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->sort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1)
                    ->pairwise(),
                [1, 3, 5, 7, 9],
                [[2, 4], [4, 6], [6, 8], [8, 10]],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->asort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                ['a' => 1, 'c' => 3],
                ['a' => 2, 'c' => 4],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator $input
     * @param callable(iterable $input): Stream $leftChainFunc
     * @param callable(Stream $stream): Stream $rightChainFunc
     * @param array $expectedPeeked
     * @param array $expectedResult
     * @return void
     */
    public function testIterators(\Iterator $input, callable $leftChainFunc, callable $rightChainFunc, array $expectedPeeked, array $expectedResult): void
    {
        // Given
        $stream = $leftChainFunc($input);
        $peeked = [];

        // When
        $stream->peek(static function (iterable $buffer) use (&$peeked) {
            foreach ($buffer as $key => $item) {
                $peeked[$key] = $item;
            }
        });

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $this->assertSame($expectedPeeked, $peeked);
        $this->assertSame($expectedResult, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = fn ($data) => new \ArrayIterator($data);

        return [
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                [],
                [],
            ],
            [
                $iter([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                [5, 4, 3, 2, 1],
                [5, 4, 3, 2, 1],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                [],
                [],
            ],
            [
                $iter([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                [5, 4, 3, 2, 1],
                [1, 2, 3, 4, 5],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                [],
                [],
            ],
            [
                $iter([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                [1, 2, 3, 4, 5],
                [1, 2, 3, 4, 5],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith([11, 22, 33, 44, 55]),
                fn (Stream $stream) => $stream
                    ->limit(3),
                [],
                [],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith([11, 22, 33, 44, 55]),
                fn (Stream $stream) => $stream
                    ->limit(3),
                [[1, 11], [2, 22], [3, 33], [4, 44], [5, 55]],
                [[1, 11], [2, 22], [3, 33]],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->sort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1)
                    ->pairwise(),
                [],
                [],
            ],
            [
                $iter([9, 8, 7, 6, 5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->sort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1)
                    ->pairwise(),
                [1, 3, 5, 7, 9],
                [[2, 4], [4, 6], [6, 8], [8, 10]],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->asort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                ['a' => 1, 'c' => 3],
                ['a' => 2, 'c' => 4],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable $input
     * @param callable(iterable $input): Stream $leftChainFunc
     * @param callable(Stream $stream): Stream $rightChainFunc
     * @param array $expectedPeeked
     * @param array $expectedResult
     * @return void
     */
    public function testTraversables(\Traversable $input, callable $leftChainFunc, callable $rightChainFunc, array $expectedPeeked, array $expectedResult): void
    {
        // Given
        $stream = $leftChainFunc($input);
        $peeked = [];

        // When
        $stream->peek(static function (iterable $buffer) use (&$peeked) {
            foreach ($buffer as $key => $item) {
                $peeked[$key] = $item;
            }
        });

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $this->assertSame($expectedPeeked, $peeked);
        $this->assertSame($expectedResult, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                [],
                [],
            ],
            [
                $trav([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                [5, 4, 3, 2, 1],
                [5, 4, 3, 2, 1],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                [],
                [],
            ],
            [
                $trav([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                [5, 4, 3, 2, 1],
                [1, 2, 3, 4, 5],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                [],
                [],
            ],
            [
                $trav([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                [1, 2, 3, 4, 5],
                [1, 2, 3, 4, 5],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith([11, 22, 33, 44, 55]),
                fn (Stream $stream) => $stream
                    ->limit(3),
                [],
                [],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith([11, 22, 33, 44, 55]),
                fn (Stream $stream) => $stream
                    ->limit(3),
                [[1, 11], [2, 22], [3, 33], [4, 44], [5, 55]],
                [[1, 11], [2, 22], [3, 33]],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->sort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1)
                    ->pairwise(),
                [],
                [],
            ],
            [
                $trav([9, 8, 7, 6, 5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->sort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1)
                    ->pairwise(),
                [1, 3, 5, 7, 9],
                [[2, 4], [4, 6], [6, 8], [8, 10]],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->asort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                ['a' => 1, 'c' => 3],
                ['a' => 2, 'c' => 4],
            ],
        ];
    }
}
