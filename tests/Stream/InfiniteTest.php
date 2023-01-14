<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class InfiniteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param Stream $stream
     * @param array  $expected
     * @return void
     * @dataProvider dataProviderForArray
     */
    public function testArray(Stream $stream, array $expected): void
    {
        // Given
        $result = [];
        $i      = 0;

        // When
        foreach ($stream as $value) {
            $result[] = $value;

            if (count($expected) > 0 && $i === count($expected) - 1) {
                break;
            }

            ++$i;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                Stream::of([])->infiniteCycle(),
                [],
            ],
            [
                Stream::of([1, 2, 3, 4, 5])
                    ->filterTrue(fn ($item) => $item < 0)
                    ->infiniteCycle(),
                [],
            ],
            [
                Stream::of([1, 2, 3, 4, 5])->infiniteCycle(),
                [1, 2, 3, 4, 5, 1, 2, 3, 4, 5, 1, 2, 3, 4, 5],
            ],
            [
                Stream::of([1, 2, 3, 4, 5])
                    ->filterTrue(fn ($item) => $item % 2 !== 0)
                    ->infiniteCycle(),
                [1, 3, 5, 1, 3, 5, 1, 3, 5],
            ],
            [
                Stream::of([1, 2, 3, 4, 5])
                    ->filterTrue(fn ($item) => $item % 2 !== 0)
                    ->infiniteCycle()
                    ->runningMax(),
                [1, 3, 5, 5, 5, 5, 5, 5, 5],
            ],
            [
                Stream::of([1, 2, 3, 4, 5])
                    ->infiniteCycle()
                    ->runningTotal(),
                [1, 3, 6, 10, 15, 16, 18, 21, 25, 30, 31, 33, 36, 40, 45],
            ],
        ];
    }

    /**
     * @param Stream $stream
     * @param array  $expected
     * @return void
     * @dataProvider dataProviderForGenerator
     */
    public function testGenerator(Stream $stream, array $expected): void
    {
        // Given
        $result = [];
        $i      = 0;

        // When
        foreach ($stream as $value) {
            $result[] = $value;

            if (count($expected) > 0 && $i === count($expected) - 1) {
                break;
            }

            ++$i;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForGenerator(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                Stream::of($gen([]))->infiniteCycle(),
                [],
            ],
            [
                Stream::of($gen([1, 2, 3, 4, 5]))
                    ->filterTrue(fn ($item) => $item < 0)
                    ->infiniteCycle(),
                [],
            ],
            [
                Stream::of($gen([1, 2, 3, 4, 5]))->infiniteCycle(),
                [1, 2, 3, 4, 5, 1, 2, 3, 4, 5, 1, 2, 3, 4, 5],
            ],
            [
                Stream::of($gen([1, 2, 3, 4, 5]))
                    ->filterTrue(fn ($item) => $item % 2 !== 0)
                    ->infiniteCycle(),
                [1, 3, 5, 1, 3, 5, 1, 3, 5],
            ],
            [
                Stream::of($gen([1, 2, 3, 4, 5]))
                    ->filterTrue(fn ($item) => $item % 2 !== 0)
                    ->infiniteCycle()
                    ->runningMax(),
                [1, 3, 5, 5, 5, 5, 5, 5, 5],
            ],
            [
                Stream::of($gen([1, 2, 3, 4, 5]))
                    ->infiniteCycle()
                    ->runningTotal(),
                [1, 3, 6, 10, 15, 16, 18, 21, 25, 30, 31, 33, 36, 40, 45],
            ],
        ];
    }

    /**
     * @param Stream $stream
     * @param array  $expected
     * @return void
     * @dataProvider dataProviderForIterator
     */
    public function testIterator(Stream $stream, array $expected): void
    {
        // Given
        $result = [];
        $i      = 0;

        // When
        foreach ($stream as $value) {
            $result[] = $value;

            if (count($expected) > 0 && $i === count($expected) - 1) {
                break;
            }

            ++$i;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForIterator(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                Stream::of($iter([]))->infiniteCycle(),
                [],
            ],
            [
                Stream::of($iter([1, 2, 3, 4, 5]))
                    ->filterTrue(fn ($item) => $item < 0)
                    ->infiniteCycle(),
                [],
            ],
            [
                Stream::of($iter([1, 2, 3, 4, 5]))->infiniteCycle(),
                [1, 2, 3, 4, 5, 1, 2, 3, 4, 5, 1, 2, 3, 4, 5],
            ],
            [
                Stream::of($iter([1, 2, 3, 4, 5]))
                    ->filterTrue(fn ($item) => $item % 2 !== 0)
                    ->infiniteCycle(),
                [1, 3, 5, 1, 3, 5, 1, 3, 5],
            ],
            [
                Stream::of($iter([1, 2, 3, 4, 5]))
                    ->filterTrue(fn ($item) => $item % 2 !== 0)
                    ->infiniteCycle()
                    ->runningMax(),
                [1, 3, 5, 5, 5, 5, 5, 5, 5],
            ],
            [
                Stream::of($iter([1, 2, 3, 4, 5]))
                    ->infiniteCycle()
                    ->runningTotal(),
                [1, 3, 6, 10, 15, 16, 18, 21, 25, 30, 31, 33, 36, 40, 45],
            ],
        ];
    }

    /**
     * @param Stream $stream
     * @param array  $expected
     * @return void
     * @dataProvider dataProviderForTraversable
     */
    public function testTraversable(Stream $stream, array $expected): void
    {
        // Given
        $result = [];
        $i      = 0;

        // When
        foreach ($stream as $value) {
            $result[] = $value;

            if (count($expected) > 0 && $i === count($expected) - 1) {
                break;
            }

            ++$i;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForTraversable(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                Stream::of($trav([]))->infiniteCycle(),
                [],
            ],
            [
                Stream::of($trav([1, 2, 3, 4, 5]))
                    ->filterTrue(fn ($item) => $item < 0)
                    ->infiniteCycle(),
                [],
            ],
            [
                Stream::of($trav([1, 2, 3, 4, 5]))->infiniteCycle(),
                [1, 2, 3, 4, 5, 1, 2, 3, 4, 5, 1, 2, 3, 4, 5],
            ],
            [
                Stream::of($trav([1, 2, 3, 4, 5]))
                    ->filterTrue(fn ($item) => $item % 2 !== 0)
                    ->infiniteCycle(),
                [1, 3, 5, 1, 3, 5, 1, 3, 5],
            ],
            [
                Stream::of($trav([1, 2, 3, 4, 5]))
                    ->filterTrue(fn ($item) => $item % 2 !== 0)
                    ->infiniteCycle()
                    ->runningMax(),
                [1, 3, 5, 5, 5, 5, 5, 5, 5],
            ],
            [
                Stream::of($trav([1, 2, 3, 4, 5]))
                    ->infiniteCycle()
                    ->runningTotal(),
                [1, 3, 6, 10, 15, 16, 18, 21, 25, 30, 31, 33, 36, 40, 45],
            ],
        ];
    }
}
