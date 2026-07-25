<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
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
        $stream->peek(static function ($item) use (&$peeked) {
            $peeked[] = $item;
        });

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $this->assertSame($expectedPeeked, $peeked);
        $this->assertSame($expectedResult, $result);
    }

    public static function dataProviderForArray(): array
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
                [[1, 11], [2, 22], [3, 33]],
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
                [1, 3],
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
        $stream->peek(static function ($item) use (&$peeked) {
            $peeked[] = $item;
        });

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $this->assertSame($expectedPeeked, $peeked);
        $this->assertSame($expectedResult, $result);
    }

    public static function dataProviderForGenerators(): array
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
                [[1, 11], [2, 22], [3, 33]],
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
                [1, 3],
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
        $stream->peek(static function ($item) use (&$peeked) {
            $peeked[] = $item;
        });

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $this->assertSame($expectedPeeked, $peeked);
        $this->assertSame($expectedResult, $result);
    }

    public static function dataProviderForIterators(): array
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
                [[1, 11], [2, 22], [3, 33]],
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
                [1, 3],
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
        $stream->peek(static function ($item) use (&$peeked) {
            $peeked[] = $item;
        });

        // And when
        $result = $rightChainFunc($stream)->toAssociativeArray();

        // Then
        $this->assertSame($expectedPeeked, $peeked);
        $this->assertSame($expectedResult, $result);
    }

    public static function dataProviderForTraversables(): array
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
                [[1, 11], [2, 22], [3, 33]],
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
                [1, 3],
                ['a' => 2, 'c' => 4],
            ],
        ];
    }

    /**
     * @test Stream::peek is lazy — only elements pulled by downstream are peeked (array)
     */
    public function testLazyPeeksOnlyConsumedElementsArray(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $peeked = [];

        // When
        $result = Stream::of($data)
            ->peek(static function ($item) use (&$peeked) {
                $peeked[] = $item;
            })
            ->limit(3)
            ->toArray();

        // Then
        $this->assertSame([1, 2, 3], $result);

        // And the spy was called exactly once per element consumed downstream
        $this->assertSame([1, 2, 3], $peeked);
    }

    /**
     * @test Stream::peek is lazy — only elements pulled by downstream are peeked (generator)
     */
    public function testLazyPeeksOnlyConsumedElementsGenerator(): void
    {
        // Given
        $data = GeneratorFixture::getGenerator([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $peeked = [];

        // When
        $result = Stream::of($data)
            ->peek(static function ($item) use (&$peeked) {
                $peeked[] = $item;
            })
            ->limit(3)
            ->toArray();

        // Then
        $this->assertSame([1, 2, 3], $result);

        // And the spy was called exactly once per element consumed downstream
        $this->assertSame([1, 2, 3], $peeked);
    }

    /**
     * @test Stream::peek is lazy — only elements pulled by downstream are peeked (iterator)
     */
    public function testLazyPeeksOnlyConsumedElementsIterator(): void
    {
        // Given
        $data = new ArrayIteratorFixture([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $peeked = [];

        // When
        $result = Stream::of($data)
            ->peek(static function ($item) use (&$peeked) {
                $peeked[] = $item;
            })
            ->limit(3)
            ->toArray();

        // Then
        $this->assertSame([1, 2, 3], $result);

        // And the spy was called exactly once per element consumed downstream
        $this->assertSame([1, 2, 3], $peeked);
    }

    /**
     * @test Stream::peek is lazy — only elements pulled by downstream are peeked (traversable)
     */
    public function testLazyPeeksOnlyConsumedElementsTraversable(): void
    {
        // Given
        $data = new IteratorAggregateFixture([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $peeked = [];

        // When
        $result = Stream::of($data)
            ->peek(static function ($item) use (&$peeked) {
                $peeked[] = $item;
            })
            ->limit(3)
            ->toArray();

        // Then
        $this->assertSame([1, 2, 3], $result);

        // And the spy was called exactly once per element consumed downstream
        $this->assertSame([1, 2, 3], $peeked);
    }

    /**
     * @test Stream::peek does not consume the upstream past what downstream requests
     */
    public function testLazyDoesNotConsumeUpstreamPastLimit(): void
    {
        // Given: a source that errors if iterated past index 3
        $data = (static function (): \Generator {
            yield 1;
            yield 2;
            yield 3;
            throw new \RuntimeException('Source iterated too far');
        })();
        $peeked = [];

        // When
        $result = Stream::of($data)
            ->peek(static function ($item) use (&$peeked) {
                $peeked[] = $item;
            })
            ->limit(3)
            ->toArray();

        // Then: no exception was thrown, and the spy was called exactly once
        // per element consumed downstream
        $this->assertSame([1, 2, 3], $result);
        $this->assertSame([1, 2, 3], $peeked);
    }

    /**
     * @test Stream::peek callback fires before downstream consumes each element (array)
     */
    public function testCallbackFiresBeforeDownstreamForEachElementArray(): void
    {
        // Given
        $data = [1, 2, 3];
        $log = [];

        // When
        Stream::of($data)
            ->peek(static function ($item) use (&$log) {
                $log[] = "peek: {$item}";
            })
            ->map(static function ($item) use (&$log) {
                $log[] = "map: {$item}";
                return $item;
            })
            ->toArray();

        // Then: interleaved per element, not phase-by-phase
        $this->assertSame(['peek: 1', 'map: 1', 'peek: 2', 'map: 2', 'peek: 3', 'map: 3'], $log);
    }

    /**
     * @test Stream::peek callback fires before downstream consumes each element (generator)
     */
    public function testCallbackFiresBeforeDownstreamForEachElementGenerator(): void
    {
        // Given
        $data = GeneratorFixture::getGenerator([1, 2, 3]);
        $log = [];

        // When
        Stream::of($data)
            ->peek(static function ($item) use (&$log) {
                $log[] = "peek: {$item}";
            })
            ->map(static function ($item) use (&$log) {
                $log[] = "map: {$item}";
                return $item;
            })
            ->toArray();

        // Then: interleaved per element, not phase-by-phase
        $this->assertSame(['peek: 1', 'map: 1', 'peek: 2', 'map: 2', 'peek: 3', 'map: 3'], $log);
    }

    /**
     * @test Stream::peek callback fires before downstream consumes each element (iterator)
     */
    public function testCallbackFiresBeforeDownstreamForEachElementIterator(): void
    {
        // Given
        $data = new ArrayIteratorFixture([1, 2, 3]);
        $log = [];

        // When
        Stream::of($data)
            ->peek(static function ($item) use (&$log) {
                $log[] = "peek: {$item}";
            })
            ->map(static function ($item) use (&$log) {
                $log[] = "map: {$item}";
                return $item;
            })
            ->toArray();

        // Then: interleaved per element, not phase-by-phase
        $this->assertSame(['peek: 1', 'map: 1', 'peek: 2', 'map: 2', 'peek: 3', 'map: 3'], $log);
    }

    /**
     * @test Stream::peek callback fires before downstream consumes each element (traversable)
     */
    public function testCallbackFiresBeforeDownstreamForEachElementTraversable(): void
    {
        // Given
        $data = new IteratorAggregateFixture([1, 2, 3]);
        $log = [];

        // When
        Stream::of($data)
            ->peek(static function ($item) use (&$log) {
                $log[] = "peek: {$item}";
            })
            ->map(static function ($item) use (&$log) {
                $log[] = "map: {$item}";
                return $item;
            })
            ->toArray();

        // Then: interleaved per element, not phase-by-phase
        $this->assertSame(['peek: 1', 'map: 1', 'peek: 2', 'map: 2', 'peek: 3', 'map: 3'], $log);
    }

    /**
     * @test Stream::peek passes elements through unchanged with keys preserved (array)
     */
    public function testPreservesElementsAndKeysArray(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 2, 'c' => 3];

        // When
        $result = Stream::of($data)
            ->peek(static function ($item) {
            })
            ->toAssociativeArray();

        // Then
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], $result);
    }

    /**
     * @test Stream::peek passes elements through unchanged with keys preserved (generator)
     */
    public function testPreservesElementsAndKeysGenerator(): void
    {
        // Given
        $data = GeneratorFixture::getKeyValueGenerator(['a' => 1, 'b' => 2, 'c' => 3]);

        // When
        $result = Stream::of($data)
            ->peek(static function ($item) {
            })
            ->toAssociativeArray();

        // Then
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], $result);
    }

    /**
     * @test Stream::peek passes elements through unchanged with keys preserved (iterator)
     */
    public function testPreservesElementsAndKeysIterator(): void
    {
        // Given
        $data = new \ArrayIterator(['a' => 1, 'b' => 2, 'c' => 3]);

        // When
        $result = Stream::of($data)
            ->peek(static function ($item) {
            })
            ->toAssociativeArray();

        // Then
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], $result);
    }

    /**
     * @test Stream::peek passes elements through unchanged with keys preserved (traversable)
     */
    public function testPreservesElementsAndKeysTraversable(): void
    {
        // Given
        $data = new IteratorAggregateFixture(['a' => 1, 'b' => 2, 'c' => 3]);

        // When
        $result = Stream::of($data)
            ->peek(static function ($item) {
            })
            ->toAssociativeArray();

        // Then
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], $result);
    }
}
