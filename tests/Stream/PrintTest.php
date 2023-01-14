<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
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
    public function testPrint(iterable $data, ?string $glue, string $expectedOutput): void
    {
        // Given
        $stream = Stream::of($data);

        // Then
        $this->expectOutputString($expectedOutput);

        // When
        $stream->print($glue);
    }

    public function dataProviderForPrintArray(): array
    {
        return [
            [
                [],
                null,
                '',
            ],
            [
                [],
                ', ',
                '',
            ],
            [
                [1],
                null,
                '1',
            ],
            [
                [1],
                ', ',
                '1',
            ],
            [
                [1, 2, 3],
                null,
                '123',
            ],
            [
                [1, 2, 3],
                ', ',
                '1, 2, 3',
            ],
            [
                ['first', 'second', 'third'],
                null,
                'firstsecondthird',
            ],
            [
                ['first', 'second', 'third'],
                ', ',
                'first, second, third',
            ],
            [
                ['日本語', 'English', 'Español'],
                null,
                '日本語EnglishEspañol',
            ],
            [
                ['日本語', 'English', 'Español'],
                ', ',
                '日本語, English, Español',
            ],
        ];
    }

    public function dataProviderForPrintGenerator(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                null,
                '',
            ],
            [
                $gen([]),
                ', ',
                '',
            ],
            [
                $gen([1]),
                null,
                '1',
            ],
            [
                $gen([1]),
                ', ',
                '1',
            ],
            [
                $gen([1, 2, 3]),
                null,
                '123',
            ],
            [
                $gen([1, 2, 3]),
                ', ',
                '1, 2, 3',
            ],
            [
                $gen(['first', 'second', 'third']),
                null,
                'firstsecondthird',
            ],
            [
                $gen(['first', 'second', 'third']),
                ', ',
                'first, second, third',
            ],
            [
                $gen(['日本語', 'English', 'Español']),
                null,
                '日本語EnglishEspañol',
            ],
            [
                $gen(['日本語', 'English', 'Español']),
                ', ',
                '日本語, English, Español',
            ],
        ];
    }

    public function dataProviderForPrintIterator(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                null,
                '',
            ],
            [
                $iter([]),
                ', ',
                '',
            ],
            [
                $iter([1]),
                null,
                '1',
            ],
            [
                $iter([1]),
                ', ',
                '1',
            ],
            [
                $iter([1, 2, 3]),
                null,
                '123',
            ],
            [
                $iter([1, 2, 3]),
                ', ',
                '1, 2, 3',
            ],
            [
                $iter(['first', 'second', 'third']),
                null,
                'firstsecondthird',
            ],
            [
                $iter(['first', 'second', 'third']),
                ', ',
                'first, second, third',
            ],
            [
                $iter(['日本語', 'English', 'Español']),
                null,
                '日本語EnglishEspañol',
            ],
            [
                $iter(['日本語', 'English', 'Español']),
                ', ',
                '日本語, English, Español',
            ],
        ];
    }

    public function dataProviderForPrintTraversable(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                null,
                '',
            ],
            [
                $trav([]),
                ', ',
                '',
            ],
            [
                $trav([1]),
                null,
                '1',
            ],
            [
                $trav([1]),
                ', ',
                '1',
            ],
            [
                $trav([1, 2, 3]),
                null,
                '123',
            ],
            [
                $trav([1, 2, 3]),
                ', ',
                '1, 2, 3',
            ],
            [
                $trav(['first', 'second', 'third']),
                null,
                'firstsecondthird',
            ],
            [
                $trav(['first', 'second', 'third']),
                ', ',
                'first, second, third',
            ],
            [
                $trav(['日本語', 'English', 'Español']),
                null,
                '日本語EnglishEspañol',
            ],
            [
                $trav(['日本語', 'English', 'Español']),
                ', ',
                '日本語, English, Español',
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
}
