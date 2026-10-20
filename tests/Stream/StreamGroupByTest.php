<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamGroupByTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         Stream groupBy array throws TypeError for a group name that isn't int|string|bool
     * @dataProvider dataProviderForInvalidGroupNamesArray
     * @param        array $iterable
     * @param        callable $groupKeyFunction
     * @param        string $expectedType
     */
    public function testInvalidGroupNamesArray(array $iterable, callable $groupKeyFunction, string $expectedType): void
    {
        // Given
        $stream = Stream::of($iterable)->groupBy($groupKeyFunction);

        // Then
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(\sprintf('Group name must be int|string|bool, got %s', $expectedType));

        // When
        $stream->toArray();
    }

    public static function dataProviderForInvalidGroupNamesArray(): array
    {
        return [
            'float' => [
                [1, 2, 3],
                fn ($x) => 4.5,
                'double',
            ],
            'integral float' => [
                [1, 2, 3],
                fn ($x) => 4.0,
                'double',
            ],
            'null' => [
                [1, 2, 3],
                fn ($x) => null,
                'NULL',
            ],
            'object' => [
                [1, 2, 3],
                fn ($x) => (object)['v' => $x],
                'object',
            ],
            'float in a list' => [
                [1, 2, 3],
                fn ($x) => ['a', 2.5],
                'double',
            ],
            'null in a list' => [
                [1, 2, 3],
                fn ($x) => ['a', null],
                'NULL',
            ],
            'array in a list' => [
                [1, 2, 3],
                fn ($x) => ['a', [$x]],
                'array',
            ],
            'float for a later item only' => [
                [1, 2, 3],
                fn ($x) => $x === 1 ? 'a' : 4.5,
                'double',
            ],
        ];
    }

    /**
     * @test         Stream groupBy array accepts int, string and bool group names, alone or in a list
     * @dataProvider dataProviderForValidGroupNamesArray
     * @param        array $iterable
     * @param        callable $groupKeyFunction
     * @param        array $expected
     */
    public function testValidGroupNamesArray(array $iterable, callable $groupKeyFunction, array $expected): void
    {
        // When
        $result = Stream::of($iterable)
            ->groupBy($groupKeyFunction)
            ->toAssociativeArray();

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForValidGroupNamesArray(): array
    {
        return [
            'int' => [
                [1, 2, 3],
                fn ($x) => $x % 2,
                [1 => [1, 3], 0 => [2]],
            ],
            'string' => [
                [1, 2, 3],
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd' => [1, 3], 'even' => [2]],
            ],
            'bool' => [
                [1, 2, 3],
                fn ($x) => $x > 1,
                [0 => [1], 1 => [2, 3]],
            ],
            'list of int, string and bool' => [
                [1, 2, 3],
                fn ($x) => [$x, 'all', $x > 2],
                [1 => [1, 3], 'all' => [1, 2, 3], 0 => [1, 2], 2 => [2], 3 => [3]],
            ],
        ];
    }

    /**
     * @test         Stream groupBy generator throws TypeError for a group name that isn't int|string|bool
     * @dataProvider dataProviderForInvalidGroupNamesGenerator
     * @param        \Generator $iterable
     * @param        callable $groupKeyFunction
     * @param        string $expectedType
     */
    public function testInvalidGroupNamesGenerator(\Generator $iterable, callable $groupKeyFunction, string $expectedType): void
    {
        // Given
        $stream = Stream::of($iterable)->groupBy($groupKeyFunction);

        // Then
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(\sprintf('Group name must be int|string|bool, got %s', $expectedType));

        // When
        $stream->toArray();
    }

    public static function dataProviderForInvalidGroupNamesGenerator(): array
    {
        return [
            'float' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => 4.5,
                'double',
            ],
            'integral float' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => 4.0,
                'double',
            ],
            'null' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => null,
                'NULL',
            ],
            'object' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => (object)['v' => $x],
                'object',
            ],
            'float in a list' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => ['a', 2.5],
                'double',
            ],
            'null in a list' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => ['a', null],
                'NULL',
            ],
            'array in a list' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => ['a', [$x]],
                'array',
            ],
            'float for a later item only' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => $x === 1 ? 'a' : 4.5,
                'double',
            ],
        ];
    }

    /**
     * @test         Stream groupBy generator accepts int, string and bool group names, alone or in a list
     * @dataProvider dataProviderForValidGroupNamesGenerator
     * @param        \Generator $iterable
     * @param        callable $groupKeyFunction
     * @param        array $expected
     */
    public function testValidGroupNamesGenerator(\Generator $iterable, callable $groupKeyFunction, array $expected): void
    {
        // When
        $result = Stream::of($iterable)
            ->groupBy($groupKeyFunction)
            ->toAssociativeArray();

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForValidGroupNamesGenerator(): array
    {
        return [
            'int' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => $x % 2,
                [1 => [1, 3], 0 => [2]],
            ],
            'string' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd' => [1, 3], 'even' => [2]],
            ],
            'bool' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => $x > 1,
                [0 => [1], 1 => [2, 3]],
            ],
            'list of int, string and bool' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => [$x, 'all', $x > 2],
                [1 => [1, 3], 'all' => [1, 2, 3], 0 => [1, 2], 2 => [2], 3 => [3]],
            ],
        ];
    }

    /**
     * @test         Stream groupBy iterator throws TypeError for a group name that isn't int|string|bool
     * @dataProvider dataProviderForInvalidGroupNamesIterator
     * @param        \Iterator $iterable
     * @param        callable $groupKeyFunction
     * @param        string $expectedType
     */
    public function testInvalidGroupNamesIterator(\Iterator $iterable, callable $groupKeyFunction, string $expectedType): void
    {
        // Given
        $stream = Stream::of($iterable)->groupBy($groupKeyFunction);

        // Then
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(\sprintf('Group name must be int|string|bool, got %s', $expectedType));

        // When
        $stream->toArray();
    }

    public static function dataProviderForInvalidGroupNamesIterator(): array
    {
        return [
            'float' => [
                new ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => 4.5,
                'double',
            ],
            'integral float' => [
                new ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => 4.0,
                'double',
            ],
            'null' => [
                new ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => null,
                'NULL',
            ],
            'object' => [
                new ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => (object)['v' => $x],
                'object',
            ],
            'float in a list' => [
                new ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => ['a', 2.5],
                'double',
            ],
            'null in a list' => [
                new ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => ['a', null],
                'NULL',
            ],
            'array in a list' => [
                new ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => ['a', [$x]],
                'array',
            ],
            'float for a later item only' => [
                new ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => $x === 1 ? 'a' : 4.5,
                'double',
            ],
        ];
    }

    /**
     * @test         Stream groupBy iterator accepts int, string and bool group names, alone or in a list
     * @dataProvider dataProviderForValidGroupNamesIterator
     * @param        \Iterator $iterable
     * @param        callable $groupKeyFunction
     * @param        array $expected
     */
    public function testValidGroupNamesIterator(\Iterator $iterable, callable $groupKeyFunction, array $expected): void
    {
        // When
        $result = Stream::of($iterable)
            ->groupBy($groupKeyFunction)
            ->toAssociativeArray();

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForValidGroupNamesIterator(): array
    {
        return [
            'int' => [
                new ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => $x % 2,
                [1 => [1, 3], 0 => [2]],
            ],
            'string' => [
                new ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd' => [1, 3], 'even' => [2]],
            ],
            'bool' => [
                new ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => $x > 1,
                [0 => [1], 1 => [2, 3]],
            ],
            'list of int, string and bool' => [
                new ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => [$x, 'all', $x > 2],
                [1 => [1, 3], 'all' => [1, 2, 3], 0 => [1, 2], 2 => [2], 3 => [3]],
            ],
        ];
    }

    /**
     * @test         Stream groupBy traversable throws TypeError for a group name that isn't int|string|bool
     * @dataProvider dataProviderForInvalidGroupNamesTraversable
     * @param        \Traversable $iterable
     * @param        callable $groupKeyFunction
     * @param        string $expectedType
     */
    public function testInvalidGroupNamesTraversable(\Traversable $iterable, callable $groupKeyFunction, string $expectedType): void
    {
        // Given
        $stream = Stream::of($iterable)->groupBy($groupKeyFunction);

        // Then
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(\sprintf('Group name must be int|string|bool, got %s', $expectedType));

        // When
        $stream->toArray();
    }

    public static function dataProviderForInvalidGroupNamesTraversable(): array
    {
        return [
            'float' => [
                new IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => 4.5,
                'double',
            ],
            'integral float' => [
                new IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => 4.0,
                'double',
            ],
            'null' => [
                new IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => null,
                'NULL',
            ],
            'object' => [
                new IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => (object)['v' => $x],
                'object',
            ],
            'float in a list' => [
                new IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => ['a', 2.5],
                'double',
            ],
            'null in a list' => [
                new IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => ['a', null],
                'NULL',
            ],
            'array in a list' => [
                new IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => ['a', [$x]],
                'array',
            ],
            'float for a later item only' => [
                new IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => $x === 1 ? 'a' : 4.5,
                'double',
            ],
        ];
    }

    /**
     * @test         Stream groupBy traversable accepts int, string and bool group names, alone or in a list
     * @dataProvider dataProviderForValidGroupNamesTraversable
     * @param        \Traversable $iterable
     * @param        callable $groupKeyFunction
     * @param        array $expected
     */
    public function testValidGroupNamesTraversable(\Traversable $iterable, callable $groupKeyFunction, array $expected): void
    {
        // When
        $result = Stream::of($iterable)
            ->groupBy($groupKeyFunction)
            ->toAssociativeArray();

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForValidGroupNamesTraversable(): array
    {
        return [
            'int' => [
                new IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => $x % 2,
                [1 => [1, 3], 0 => [2]],
            ],
            'string' => [
                new IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd' => [1, 3], 'even' => [2]],
            ],
            'bool' => [
                new IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => $x > 1,
                [0 => [1], 1 => [2, 3]],
            ],
            'list of int, string and bool' => [
                new IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => [$x, 'all', $x > 2],
                [1 => [1, 3], 'all' => [1, 2, 3], 0 => [1, 2], 2 => [2], 3 => [3]],
            ],
        ];
    }
}
