<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class PeekPrintTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArray
     * @param array $input
     * @param array $config
     * @param callable(iterable $input): Stream $leftChainFunc
     * @param callable(Stream $stream): Stream $rightChainFunc
     * @param string $expectedPeeked
     * @param array $expectedResult
     * @return void
     */
    public function testArray(array $input, array $config, callable $leftChainFunc, callable $rightChainFunc, string $expectedPeeked, array $expectedResult): void
    {
        // Given
        $stream = $leftChainFunc($input);

        // When
        $stream->peekPrint(...$config);

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $output = $this->getActualOutputForAssertion();
        $this->assertSame($expectedPeeked, $output);
        $this->assertSame($expectedResult, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                [],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                '',
                [],
            ],
            [
                [],
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                '()',
                [],
            ],
            [
                [5, 4, 3, 2, 1],
                [],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                '54321',
                [5, 4, 3, 2, 1],
            ],
            [
                [5, 4, 3, 2, 1],
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                '(5, 4, 3, 2, 1)',
                [5, 4, 3, 2, 1],
            ],
            [
                [5, 4, 3, 2, 1],
                [],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                '54321',
                [1, 2, 3, 4, 5],
            ],
            [
                [5, 4, 3, 2, 1],
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                '(5, 4, 3, 2, 1)',
                [1, 2, 3, 4, 5],
            ],
            [
                [5, 4, 3, 2, 1],
                [],
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                '12345',
                [1, 2, 3, 4, 5],
            ],
            [
                [5, 4, 3, 2, 1],
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                '(1, 2, 3, 4, 5)',
                [1, 2, 3, 4, 5],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->asort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                '13',
                ['a' => 2, 'c' => 4],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->asort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                '(1, 3)',
                ['a' => 2, 'c' => 4],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator $input
     * @param array $config
     * @param callable(iterable $input): Stream $leftChainFunc
     * @param callable(Stream $stream): Stream $rightChainFunc
     * @param string $expectedPeeked
     * @param array $expectedResult
     * @return void
     */
    public function testGenerators(\Generator $input, array $config, callable $leftChainFunc, callable $rightChainFunc, string $expectedPeeked, array $expectedResult): void
    {
        // Given
        $stream = $leftChainFunc($input);

        // When
        $stream->peekPrint(...$config);

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $output = $this->getActualOutputForAssertion();
        $this->assertSame($expectedPeeked, $output);
        $this->assertSame($expectedResult, $result);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = fn ($data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [
                $gen([]),
                [],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                '',
                [],
            ],
            [
                $gen([]),
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                '()',
                [],
            ],
            [
                $gen([5, 4, 3, 2, 1]),
                [],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                '54321',
                [5, 4, 3, 2, 1],
            ],
            [
                $gen([5, 4, 3, 2, 1]),
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                '(5, 4, 3, 2, 1)',
                [5, 4, 3, 2, 1],
            ],
            [
                $gen([5, 4, 3, 2, 1]),
                [],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                '54321',
                [1, 2, 3, 4, 5],
            ],
            [
                $gen([5, 4, 3, 2, 1]),
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                '(5, 4, 3, 2, 1)',
                [1, 2, 3, 4, 5],
            ],
            [
                $gen([5, 4, 3, 2, 1]),
                [],
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                '12345',
                [1, 2, 3, 4, 5],
            ],
            [
                $gen([5, 4, 3, 2, 1]),
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                '(1, 2, 3, 4, 5)',
                [1, 2, 3, 4, 5],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3]),
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->asort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                '13',
                ['a' => 2, 'c' => 4],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3]),
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->asort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                '(1, 3)',
                ['a' => 2, 'c' => 4],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator $input
     * @param array $config
     * @param callable(iterable $input): Stream $leftChainFunc
     * @param callable(Stream $stream): Stream $rightChainFunc
     * @param string $expectedPeeked
     * @param array $expectedResult
     * @return void
     */
    public function testIterators(\Iterator $input, array $config, callable $leftChainFunc, callable $rightChainFunc, string $expectedPeeked, array $expectedResult): void
    {
        // Given
        $stream = $leftChainFunc($input);

        // When
        $stream->peekPrint(...$config);

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $output = $this->getActualOutputForAssertion();
        $this->assertSame($expectedPeeked, $output);
        $this->assertSame($expectedResult, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = fn ($data) => new \ArrayIterator($data);

        return [
            [
                $iter([]),
                [],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                '',
                [],
            ],
            [
                $iter([]),
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                '()',
                [],
            ],
            [
                $iter([5, 4, 3, 2, 1]),
                [],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                '54321',
                [5, 4, 3, 2, 1],
            ],
            [
                $iter([5, 4, 3, 2, 1]),
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                '(5, 4, 3, 2, 1)',
                [5, 4, 3, 2, 1],
            ],
            [
                $iter([5, 4, 3, 2, 1]),
                [],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                '54321',
                [1, 2, 3, 4, 5],
            ],
            [
                $iter([5, 4, 3, 2, 1]),
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                '(5, 4, 3, 2, 1)',
                [1, 2, 3, 4, 5],
            ],
            [
                $iter([5, 4, 3, 2, 1]),
                [],
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                '12345',
                [1, 2, 3, 4, 5],
            ],
            [
                $iter([5, 4, 3, 2, 1]),
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                '(1, 2, 3, 4, 5)',
                [1, 2, 3, 4, 5],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3]),
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->asort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                '13',
                ['a' => 2, 'c' => 4],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3]),
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->asort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                '(1, 3)',
                ['a' => 2, 'c' => 4],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable $input
     * @param array $config
     * @param callable(iterable $input): Stream $leftChainFunc
     * @param callable(Stream $stream): Stream $rightChainFunc
     * @param string $expectedPeeked
     * @param array $expectedResult
     * @return void
     */
    public function testTraversables(\Traversable $input, array $config, callable $leftChainFunc, callable $rightChainFunc, string $expectedPeeked, array $expectedResult): void
    {
        // Given
        $stream = $leftChainFunc($input);

        // When
        $stream->peekPrint(...$config);

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $output = $this->getActualOutputForAssertion();
        $this->assertSame($expectedPeeked, $output);
        $this->assertSame($expectedResult, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                [],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                '',
                [],
            ],
            [
                $trav([]),
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                '()',
                [],
            ],
            [
                $trav([5, 4, 3, 2, 1]),
                [],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                '54321',
                [5, 4, 3, 2, 1],
            ],
            [
                $trav([5, 4, 3, 2, 1]),
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream,
                '(5, 4, 3, 2, 1)',
                [5, 4, 3, 2, 1],
            ],
            [
                $trav([5, 4, 3, 2, 1]),
                [],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                '54321',
                [1, 2, 3, 4, 5],
            ],
            [
                $trav([5, 4, 3, 2, 1]),
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable),
                fn (Stream $stream) => $stream->sort(),
                '(5, 4, 3, 2, 1)',
                [1, 2, 3, 4, 5],
            ],
            [
                $trav([5, 4, 3, 2, 1]),
                [],
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                '12345',
                [1, 2, 3, 4, 5],
            ],
            [
                $trav([5, 4, 3, 2, 1]),
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable)->sort(),
                fn (Stream $stream) => $stream,
                '(1, 2, 3, 4, 5)',
                [1, 2, 3, 4, 5],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3]),
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->asort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                '13',
                ['a' => 2, 'c' => 4],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3]),
                [', ', '(', ')'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($x) => $x % 2 !== 0)
                    ->asort(),
                fn (Stream $stream) => $stream
                    ->map(fn ($x) => $x + 1),
                '(1, 3)',
                ['a' => 2, 'c' => 4],
            ],
        ];
    }
}
