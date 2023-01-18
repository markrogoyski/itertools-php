<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class PrintTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test print
     * @dataProvider dataProviderForPrintArray
     * @dataProvider dataProviderForPrintGenerator
     * @dataProvider dataProviderForPrintIterator
     * @dataProvider dataProviderForPrintTraversable
     */
    public function testPrint(iterable $data, string $expectedOutput): void
    {
        // Given
        $stream = Stream::of($data);

        // Then
        $this->expectOutputString($expectedOutput);

        // When
        $stream->print();
    }

    public function dataProviderForPrintArray(): array
    {
        return [
            [
                [],
                '',
            ],
            [
                [1],
                '1',
            ],
            [
                [1, 2, 3],
                '123',
            ],
            [
                ['first', 'second', 'third'],
                'firstsecondthird',
            ],
            [
                ['日本語', 'English', 'Español'],
                '日本語EnglishEspañol',
            ],
        ];
    }

    public function dataProviderForPrintGenerator(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                '',
            ],
            [
                $gen([1]),
                '1',
            ],
            [
                $gen([1, 2, 3]),
                '123',
            ],
            [
                $gen(['first', 'second', 'third']),
                'firstsecondthird',
            ],
            [
                $gen(['日本語', 'English', 'Español']),
                '日本語EnglishEspañol',
            ],
        ];
    }

    public function dataProviderForPrintIterator(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                '',
            ],
            [
                $iter([1]),
                '1',
            ],
            [
                $iter([1, 2, 3]),
                '123',
            ],
            [
                $iter(['first', 'second', 'third']),
                'firstsecondthird',
            ],
            [
                $iter(['日本語', 'English', 'Español']),
                '日本語EnglishEspañol',
            ],
        ];
    }

    public function dataProviderForPrintTraversable(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                '',
            ],
            [
                $trav([1]),
                '1',
            ],
            [
                $trav([1, 2, 3]),
                '123',
            ],
            [
                $trav(['first', 'second', 'third']),
                'firstsecondthird',
            ],
            [
                $trav(['日本語', 'English', 'Español']),
                '日本語EnglishEspañol',
            ],
        ];
    }

    /**
     * @test print
     * @dataProvider dataProviderForPrintGluePrefixSuffix
     */
    public function testPrintGluePrefixSuffix(iterable $data, string $separator, string $prefix, string $suffix, string $expectedOutput): void
    {
        // Given
        $stream = Stream::of($data);

        // Then
        $this->expectOutputString($expectedOutput);

        // When
        $stream->print($separator, $prefix, $suffix);
    }

    public function dataProviderForPrintGluePrefixSuffix(): array
    {
        return [
            [
                [],
                '',
                '',
                '',
                '',
            ],
            [
                [],
                ',',
                '',
                '',
                '',
            ],
            [
                [],
                '',
                '',
                'pre:',
                'pre:',
            ],
            [
                [],
                '',
                '',
                ':suffix',
                ':suffix',
            ],
            [
                [1],
                '',
                '',
                '',
                '1',
            ],
            [
                [1],
                ',',
                '',
                '',
                '1',
            ],
            [
                [1],
                '',
                'pre:',
                '',
                'pre:1',
            ],
            [
                [1],
                '',
                '',
                ':suffix',
                '1:suffix',
            ],
            [
                [1, 2, 3],
                '',
                '',
                '',
                '123',
            ],
            [
                [1, 2, 3],
                ',',
                '',
                '',
                '1,2,3',
            ],
            [
                [1, 2, 3],
                ', ',
                '',
                '',
                '1, 2, 3',
            ],
            [
                [1, 2, 3],
                '',
                'pre:',
                '',
                'pre:123',
            ],
            [
                [1, 2, 3],
                '',
                '',
                ':suffix',
                '123:suffix',
            ],
            [
                [1, 2, 3],
                ',',
                'pre:',
                ':suffix',
                'pre:1,2,3:suffix',
            ],
            [
                [1, 2, 3],
                ', ',
                'pre: ',
                ' :suffix',
                'pre: 1, 2, 3 :suffix',
            ],
        ];
    }

    /**
     * @test printLn
     * @dataProvider dataProviderForPrintLnArray
     * @dataProvider dataProviderForPrintLnGenerator
     * @dataProvider dataProviderForPrintLnIterator
     * @dataProvider dataProviderForPrintLnTraversable
     */
    public function testPrintLn(iterable $data, string $expectedOutput): void
    {
        // Given
        $stream = Stream::of($data);

        // Then
        $this->expectOutputString($expectedOutput);

        // When
        $stream->printLn();
    }

    public function dataProviderForPrintLnArray(): array
    {
        return [
            [
                [],
                '',
            ],
            [
                [1],
                '1' . \PHP_EOL,
            ],
            [
                [1, 2, 3],
                '1' . \PHP_EOL . '2' . \PHP_EOL . '3' . \PHP_EOL,
            ],
            [
                ['first', 'second', 'third'],
                'first' . \PHP_EOL . 'second' . \PHP_EOL . 'third' . \PHP_EOL,
            ],
            [
                ['日本語', 'English', 'Español'],
                '日本語' . \PHP_EOL . 'English' . \PHP_EOL . 'Español' . \PHP_EOL,
            ],
        ];
    }

    public function dataProviderForPrintLnGenerator(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                '',
            ],
            [
                $gen([1]),
                '1' . \PHP_EOL,
            ],
            [
                $gen([1, 2, 3]),
                '1' . \PHP_EOL . '2' . \PHP_EOL . '3' . \PHP_EOL,

            ],
            [
                $gen(['first', 'second', 'third']),
                'first' . \PHP_EOL . 'second' . \PHP_EOL . 'third' . \PHP_EOL,
            ],
            [
                $gen(['日本語', 'English', 'Español']),
                '日本語' . \PHP_EOL . 'English' . \PHP_EOL . 'Español' . \PHP_EOL,
            ],
        ];
    }

    public function dataProviderForPrintLnIterator(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                '',
            ],
            [
                $iter([1]),
                '1' . \PHP_EOL,
            ],
            [
                $iter([1, 2, 3]),
                '1' . \PHP_EOL . '2' . \PHP_EOL . '3' . \PHP_EOL,
            ],
            [
                $iter(['first', 'second', 'third']),
                'first' . \PHP_EOL . 'second' . \PHP_EOL . 'third' . \PHP_EOL,
            ],
            [
                $iter(['日本語', 'English', 'Español']),
                '日本語' . \PHP_EOL . 'English' . \PHP_EOL . 'Español' . \PHP_EOL,
            ],
        ];
    }

    public function dataProviderForPrintLnTraversable(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                '',
            ],
            [
                $trav([1]),
                '1' . \PHP_EOL,
            ],
            [
                $trav([1, 2, 3]),
                '1' . \PHP_EOL . '2' . \PHP_EOL . '3' . \PHP_EOL,
            ],
            [
                $trav(['first', 'second', 'third']),
                'first' . \PHP_EOL . 'second' . \PHP_EOL . 'third' . \PHP_EOL,
            ],
            [
                $trav(['日本語', 'English', 'Español']),
                '日本語' . \PHP_EOL . 'English' . \PHP_EOL . 'Español' . \PHP_EOL,
            ],
        ];
    }

    /**
     * @test printR
     */
    public function testPrintR(): void
    {
        // Given
        $data = [1, 2.2, ['3', 4, new \stdClass()], 5];

        // And
        $stream = Stream::of($data);

        // And

        // When
        $stream->printR();

        // Then
        $output = $this->getActualOutputForAssertion();
        $this->assertNotEmpty($output);
    }

    /**
     * @test varDump
     */
    public function testVarDump(): void
    {
        // Given
        $data = [1, 2.2, ['3', 4, new \stdClass()], 5];

        // And
        $stream = Stream::of($data);

        // And

        // When
        $stream->varDump();

        // Then
        $output = $this->getActualOutputForAssertion();
        $this->assertNotEmpty($output);
    }

    /**
     * @test callForEach - print_r callable
     * @dataProvider dataProviderForPrintArray
     * @dataProvider dataProviderForPrintGenerator
     * @dataProvider dataProviderForPrintIterator
     * @dataProvider dataProviderForPrintTraversable
     */
    public function testCallForEachPrintRCallable(iterable $data, string $expectedOutput): void
    {
        // Given
        $stream = Stream::of($data);
        $func = 'print_r';

        // Then
        $this->expectOutputString($expectedOutput);

        // When
        $stream->callForEach($func);
    }

    /**
     * @test callForEach - print closure
     * @dataProvider dataProviderForPrintArray
     * @dataProvider dataProviderForPrintGenerator
     * @dataProvider dataProviderForPrintIterator
     * @dataProvider dataProviderForPrintTraversable
     */
    public function testCallForEachPrintClosure(iterable $data, string $expectedOutput): void
    {
        // Given
        $stream = Stream::of($data);
        $func = fn ($x) => print($x);

        // Then
        $this->expectOutputString($expectedOutput);

        // When
        $stream->callForEach($func);
    }

    /**
     * @test callForEach - zip print closure
     */
    public function testCallForEachZipPrintClosure(): void
    {
        // Given
        $stream = Stream::of(['a', 'b', 'c'])->zipWith([1, 2, 3]);
        $func = fn ($x) => print("Left:{$x[0]} Right:{$x[1]}\n");

        // Then
        $this->expectOutputString("Left:a Right:1\nLeft:b Right:2\nLeft:c Right:3\n");

        // When
        $stream->callForEach($func);
    }
}
