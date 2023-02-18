<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class PeekPrintRTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForEmptyArray
     * @param array $input
     * @param callable(iterable $input): Stream $leftChainFunc
     * @param callable(Stream $stream): Stream $rightChainFunc
     * @param array $expected
     * @return void
     */
    public function testEmptyArray(array $input, callable $leftChainFunc, callable $rightChainFunc, array $expected): void
    {
        // Given
        $stream = $leftChainFunc($input);

        // When
        $stream->peekPrintR();

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $this->assertSame($expected, $result);

        // And then
        $output = $this->getActualOutputForAssertion();
        $this->assertEmpty($output);
    }

    public function dataProviderForEmptyArray(): array
    {
        return [
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                [],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                [],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                [],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith([11, 22, 33, 44, 55]),
                fn (Stream $stream) => $stream
                    ->limit(3),
                [],
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
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn () => false),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                [],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForNonEmptyArray
     * @param array $input
     * @param callable(iterable $input): Stream $leftChainFunc
     * @param callable(Stream $stream): Stream $rightChainFunc
     * @param array $expected
     * @return void
     */
    public function testNonEmptyArray(array $input, callable $leftChainFunc, callable $rightChainFunc, array $expected): void
    {
        // Given
        $stream = $leftChainFunc($input);

        // When
        $stream->peekPrintR();

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $this->assertSame($expected, $result);

        // And then
        $output = $this->getActualOutputForAssertion();
        $this->assertNotEmpty($output);
    }

    public function dataProviderForNonEmptyArray(): array
    {
        return [
            [
                [5, 4, 3, 2, 1],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                [5, 4, 3, 2, 1],
            ],
            [
                [5, 4, 3, 2, 1],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                [1, 2, 3, 4, 5],
            ],
            [
                [5, 4, 3, 2, 1],
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                [1, 2, 3, 4, 5],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith([11, 22, 33, 44, 55]),
                fn (Stream $stream) => $stream
                    ->limit(3),
                [[1, 11], [2, 22], [3, 33]],
            ],
            [
                [9, 8, 7, 6, 5, 4, 3, 2, 1],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->sort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1)
                    ->pairwise(),
                [[2, 4], [4, 6], [6, 8], [8, 10]],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->asort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                ['a' => 2, 'c' => 4],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForEmptyGenerators
     * @param \Generator $input
     * @param callable(iterable $input): Stream $leftChainFunc
     * @param callable(Stream $stream): Stream $rightChainFunc
     * @param array $expected
     * @return void
     */
    public function testEmptyGenerators(\Generator $input, callable $leftChainFunc, callable $rightChainFunc, array $expected): void
    {
        // Given
        $stream = $leftChainFunc($input);

        // When
        $stream->peekPrintR();

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $this->assertSame($expected, $result);

        // And then
        $output = $this->getActualOutputForAssertion();
        $this->assertEmpty($output);
    }

    public function dataProviderForEmptyGenerators(): array
    {
        $gen = fn ($data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                [],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                [],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                [],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith([11, 22, 33, 44, 55]),
                fn (Stream $stream) => $stream
                    ->limit(3),
                [],
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
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn () => false),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                [],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForNonEmptyGenerators
     * @param \Generator $input
     * @param callable(iterable $input): Stream $leftChainFunc
     * @param callable(Stream $stream): Stream $rightChainFunc
     * @param array $expected
     * @return void
     */
    public function testNonEmptyGenerators(\Generator $input, callable $leftChainFunc, callable $rightChainFunc, array $expected): void
    {
        // Given
        $stream = $leftChainFunc($input);

        // When
        $stream->peekPrintR();

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $this->assertSame($expected, $result);

        // And then
        $output = $this->getActualOutputForAssertion();
        $this->assertNotEmpty($output);
    }

    public function dataProviderForNonEmptyGenerators(): array
    {
        $gen = fn ($data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [
                $gen([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                [5, 4, 3, 2, 1],
            ],
            [
                $gen([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                [1, 2, 3, 4, 5],
            ],
            [
                $gen([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                [1, 2, 3, 4, 5],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith([11, 22, 33, 44, 55]),
                fn (Stream $stream) => $stream
                    ->limit(3),
                [[1, 11], [2, 22], [3, 33]],
            ],
            [
                $gen([9, 8, 7, 6, 5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->sort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1)
                    ->pairwise(),
                [[2, 4], [4, 6], [6, 8], [8, 10]],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->asort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                ['a' => 2, 'c' => 4],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForEmptyIterators
     * @param \Generator $input
     * @param callable(iterable $input): Stream $leftChainFunc
     * @param callable(Stream $stream): Stream $rightChainFunc
     * @param array $expected
     * @return void
     */
    public function testEmptyIterators(\Iterator $input, callable $leftChainFunc, callable $rightChainFunc, array $expected): void
    {
        // Given
        $stream = $leftChainFunc($input);

        // When
        $stream->peekPrintR();

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $this->assertSame($expected, $result);

        // And then
        $output = $this->getActualOutputForAssertion();
        $this->assertEmpty($output);
    }

    public function dataProviderForEmptyIterators(): array
    {
        $iter = fn ($data) => new \ArrayIterator($data);

        return [
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                [],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                [],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                [],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith([11, 22, 33, 44, 55]),
                fn (Stream $stream) => $stream
                    ->limit(3),
                [],
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
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn () => false),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                [],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForNonEmptyIterators
     * @param \Generator $input
     * @param callable(iterable $input): Stream $leftChainFunc
     * @param callable(Stream $stream): Stream $rightChainFunc
     * @param array $expected
     * @return void
     */
    public function testNonEmptyIterators(\Iterator $input, callable $leftChainFunc, callable $rightChainFunc, array $expected): void
    {
        // Given
        $stream = $leftChainFunc($input);

        // When
        $stream->peekPrintR();

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $this->assertSame($expected, $result);

        // And then
        $output = $this->getActualOutputForAssertion();
        $this->assertNotEmpty($output);
    }

    public function dataProviderForNonEmptyIterators(): array
    {
        $iter = fn ($data) => new \ArrayIterator($data);

        return [
            [
                $iter([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                [5, 4, 3, 2, 1],
            ],
            [
                $iter([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                [1, 2, 3, 4, 5],
            ],
            [
                $iter([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                [1, 2, 3, 4, 5],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith([11, 22, 33, 44, 55]),
                fn (Stream $stream) => $stream
                    ->limit(3),
                [[1, 11], [2, 22], [3, 33]],
            ],
            [
                $iter([9, 8, 7, 6, 5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->sort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1)
                    ->pairwise(),
                [[2, 4], [4, 6], [6, 8], [8, 10]],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->asort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                ['a' => 2, 'c' => 4],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForEmptyTraversables
     * @param \Generator $input
     * @param callable(iterable $input): Stream $leftChainFunc
     * @param callable(Stream $stream): Stream $rightChainFunc
     * @param array $expected
     * @return void
     */
    public function testEmptyTraversables(\Traversable $input, callable $leftChainFunc, callable $rightChainFunc, array $expected): void
    {
        // Given
        $stream = $leftChainFunc($input);

        // When
        $stream->peekPrintR();

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $this->assertSame($expected, $result);

        // And then
        $output = $this->getActualOutputForAssertion();
        $this->assertEmpty($output);
    }

    public function dataProviderForEmptyTraversables(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                [],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                [],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                [],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith([11, 22, 33, 44, 55]),
                fn (Stream $stream) => $stream
                    ->limit(3),
                [],
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
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn () => false),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                [],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForNonEmptyTraversables
     * @param \Generator $input
     * @param callable(iterable $input): Stream $leftChainFunc
     * @param callable(Stream $stream): Stream $rightChainFunc
     * @param array $expected
     * @return void
     */
    public function testNonEmptyTraversables(\Traversable $input, callable $leftChainFunc, callable $rightChainFunc, array $expected): void
    {
        // Given
        $stream = $leftChainFunc($input);

        // When
        $stream->peekPrintR();

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $this->assertSame($expected, $result);

        // And then
        $output = $this->getActualOutputForAssertion();
        $this->assertNotEmpty($output);
    }

    public function dataProviderForNonEmptyTraversables(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                [5, 4, 3, 2, 1],
            ],
            [
                $trav([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                [1, 2, 3, 4, 5],
            ],
            [
                $trav([5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                [1, 2, 3, 4, 5],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith([11, 22, 33, 44, 55]),
                fn (Stream $stream) => $stream
                    ->limit(3),
                [[1, 11], [2, 22], [3, 33]],
            ],
            [
                $trav([9, 8, 7, 6, 5, 4, 3, 2, 1]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->sort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1)
                    ->pairwise(),
                [[2, 4], [4, 6], [6, 8], [8, 10]],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->asort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                ['a' => 2, 'c' => 4],
            ],
        ];
    }
}
