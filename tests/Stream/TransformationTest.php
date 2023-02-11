<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Multi;
use IterTools\Stream;

class TransformationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test toArray
     * @param array    $input
     * @param callable $summaryStreamFactoryFunc
     * @param array    $expected
     * @dataProvider   dataProviderForToArray
     */
    public function testToArrayTrue(array $input, callable $summaryStreamFactoryFunc, array $expected): void
    {
        // When
        $result = $summaryStreamFactoryFunc($input);

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForToArray(): array
    {
        return [
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->toArray(),
                [],
            ],
            [
                [5],
                fn (iterable $iterable) => Stream::of($iterable)->toArray(),
                [5],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)->toArray(),
                [1, 2, 3, 4, 5],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                fn (iterable $iterable) => Stream::of($iterable)->toArray(),
                [1, 2, 3],
            ],
        ];
    }

    /**
     * @test toAssociativeArray
     * @param array    $input
     * @param callable $summaryStreamFactoryFunc
     * @param array    $expected
     * @dataProvider   dataProviderForToAssociativeArray
     */
    public function testToAssociativeArrayTrue(array $input, callable $summaryStreamFactoryFunc, array $expected): void
    {
        // When
        $result = $summaryStreamFactoryFunc($input);

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForToAssociativeArray(): array
    {
        return [
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toAssociativeArray(),
                [],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toAssociativeArray(
                        fn ($x) => $x,
                        fn ($x) => $x,
                    ),
                [],
            ],
            [
                [null],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toAssociativeArray(
                        fn ($x) => 'always',
                        fn ($x) => 'something',
                    ),
                ['always' => 'something'],
            ],
            [
                [5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toAssociativeArray(
                        fn ($x) => $x,
                        fn ($x) => $x,
                    ),
                ['5' => 5],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toAssociativeArray(),
                [1, 2, 3, 4, 5],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toAssociativeArray(
                        fn ($x) => $x,
                        fn ($x) => $x,
                    ),
                ['1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5],
            ],
            [
                [65, 66, 67],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toAssociativeArray(
                        fn ($x) => \chr($x),
                        fn ($x) => $x,
                    ),
                ['A' => 65, 'B' => 66, 'C' => 67],
            ],
            [
                [65, 66, 67],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toAssociativeArray(
                        fn ($x) => $x,
                        fn ($x) => \chr($x),
                    ),
                ['65' => 'A', '66' => 'B', '67' => 'C'],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toAssociativeArray(),
                ['a' => 1, 'b' => 2, 'c' => 3],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toAssociativeArray(
                        fn ($x) => $x,
                        fn ($x) => $x,
                    ),
                ['1' => 1, '2' => 2, '3' => 3],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toAssociativeArray(
                        fn ($value, $key) => $key,
                        fn ($value) => $value,
                    ),
                ['a' => 1, 'b' => 2, 'c' => 3],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toAssociativeArray(
                        fn ($value, $key) => "{$key}_{$value}",
                        fn ($value) => $value,
                    ),
                ['a_1' => 1, 'b_2' => 2, 'c_3' => 3],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTee
     * @param $input $input
     * @param int $count
     * @param array<callable(Stream $stream): Stream> $extraOperations
     * @param array<array> $expected
     * @return void
     */
    public function testTee(array $input, int $count, array $extraOperations, array $expected): void
    {
        // Given
        $inputStream = Stream::of($input);
        $result = [];

        // When
        $streams = $inputStream->tee($count);
        /**
         * @var Stream $stream
         * @var callable(Stream $stream): Stream $func
         */
        foreach (Multi::zipEqual($streams, $extraOperations) as [$stream, $func]) {
            $result[] = $func($stream)->toArray();
        }

        // Then
        $this->assertCount($count, $streams);
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForTee(): array
    {
        return [
            [
                [],
                1,
                [
                    fn (Stream $stream) => $stream,
                ],
                [
                    [],
                ],
            ],
            [
                [],
                2,
                [
                    fn (Stream $stream) => $stream,
                    fn (Stream $stream) => $stream,
                ],
                [
                    [],
                    [],
                ],
            ],
            [
                [1, 2, 3],
                1,
                [
                    fn (Stream $stream) => $stream,
                ],
                [
                    [1, 2, 3],
                ],
            ],
            [
                [1, 2, 3],
                1,
                [
                    fn (Stream $stream) => $stream->map(fn ($item) => $item * 2),
                ],
                [
                    [2, 4, 6],
                ],
            ],
            [
                [1, 2, 3],
                2,
                [
                    fn (Stream $stream) => $stream,
                    fn (Stream $stream) => $stream,
                ],
                [
                    [1, 2, 3],
                    [1, 2, 3],
                ],
            ],
            [
                [1, 2, 3],
                2,
                [
                    fn (Stream $stream) => $stream,
                    fn (Stream $stream) => $stream->map(fn ($item) => $item * 2),
                ],
                [
                    [1, 2, 3],
                    [2, 4, 6],
                ],
            ],
            [
                [1, 2, 3],
                3,
                [
                    fn (Stream $stream) => $stream,
                    fn (Stream $stream) => $stream->map(fn ($item) => $item * 2),
                    fn (Stream $stream) => $stream->map(fn ($item) => $item ** 3),
                ],
                [
                    [1, 2, 3],
                    [2, 4, 6],
                    [1, 8, 27],
                ],
            ],
            [
                [1, 2, 3],
                3,
                [
                    fn (Stream $stream) => $stream,
                    fn (Stream $stream) => $stream->map(fn ($item) => $item * 2),
                    fn (Stream $stream) => $stream->map(fn ($item) => $item ** 3)->filterTrue(fn ($item) => $item < 10),
                ],
                [
                    [1, 2, 3],
                    [2, 4, 6],
                    [1, 8],
                ],
            ],
        ];
    }

    /**
     * @test tee array destructuring
     */
    public function testTeeArrayDestructuring(): void
    {
        // Given
        $daysOfWeek = ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun'];
        $count = 3;

        // When
        [$week1Stream, $week2Stream, $week3Stream] = Stream::of($daysOfWeek)
            ->tee($count);

        // Then
        $this->assertInstanceOf(Stream::class, $week1Stream);
        $this->assertInstanceOf(Stream::class, $week2Stream);
        $this->assertInstanceOf(Stream::class, $week3Stream);

        // And
        $this->assertEquals($daysOfWeek, $week1Stream->toArray());
        $this->assertEquals($daysOfWeek, $week2Stream->toArray());
        $this->assertEquals($daysOfWeek, $week3Stream->toArray());
    }
}
