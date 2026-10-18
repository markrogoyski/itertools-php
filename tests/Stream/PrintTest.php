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

    public static function dataProviderForPrintArray(): array
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

    public static function dataProviderForPrintGenerator(): array
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

    public static function dataProviderForPrintIterator(): array
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

    public static function dataProviderForPrintTraversable(): array
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

    public static function dataProviderForPrintGluePrefixSuffix(): array
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

    public static function dataProviderForPrintLnArray(): array
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

    public static function dataProviderForPrintLnGenerator(): array
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

    public static function dataProviderForPrintLnIterator(): array
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

    public static function dataProviderForPrintLnTraversable(): array
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


    /**
     * @test print, printLn and printR format NAN as 'NAN' without raising PHP 8.5's NAN-to-string warning
     * @dataProvider dataProviderForPrintNan
     */
    public function testPrintNan(iterable $data, callable $printer, string $expectedOutput): void
    {
        // Given
        $stream   = Stream::of($data);
        $raised   = [];
        $previous = \set_error_handler(static function (int $errno, string $errstr) use (&$raised): bool {
            $raised[] = $errstr;
            return true;
        });

        // Then
        $this->expectOutputString($expectedOutput);

        try {
            // When
            $printer($stream);
        } finally {
            \set_error_handler($previous);
        }

        // Then
        $this->assertSame([], $raised);
    }

    public static function dataProviderForPrintNan(): array
    {
        $print   = fn (Stream $stream) => $stream->print(', ');
        $printLn = fn (Stream $stream) => $stream->printLn();
        $printR  = fn (Stream $stream) => $stream->printR();

        return [
            'print array'                => [[1, \NAN, 2.5], $print, '1, NAN, 2.5'],
            'print generator'            => [GeneratorFixture::getGenerator([1, \NAN, 2.5]), $print, '1, NAN, 2.5'],
            'print iterator'             => [new ArrayIteratorFixture([1, \NAN, 2.5]), $print, '1, NAN, 2.5'],
            'print iterator aggregate'   => [new IteratorAggregateFixture([1, \NAN, 2.5]), $print, '1, NAN, 2.5'],
            'printLn array'              => [[1, \NAN, 2.5], $printLn, '1' . \PHP_EOL . 'NAN' . \PHP_EOL . '2.5' . \PHP_EOL],
            'printLn generator'          => [GeneratorFixture::getGenerator([1, \NAN, 2.5]), $printLn, '1' . \PHP_EOL . 'NAN' . \PHP_EOL . '2.5' . \PHP_EOL],
            'printLn iterator'           => [new ArrayIteratorFixture([1, \NAN, 2.5]), $printLn, '1' . \PHP_EOL . 'NAN' . \PHP_EOL . '2.5' . \PHP_EOL],
            'printLn iterator aggregate' => [new IteratorAggregateFixture([1, \NAN, 2.5]), $printLn, '1' . \PHP_EOL . 'NAN' . \PHP_EOL . '2.5' . \PHP_EOL],
            'printR array'               => [[1, \NAN, 2.5], $printR, '1NAN2.5'],
            'printR generator'           => [GeneratorFixture::getGenerator([1, \NAN, 2.5]), $printR, '1NAN2.5'],
            'printR iterator'            => [new ArrayIteratorFixture([1, \NAN, 2.5]), $printR, '1NAN2.5'],
            'printR iterator aggregate'  => [new IteratorAggregateFixture([1, \NAN, 2.5]), $printR, '1NAN2.5'],
        ];
    }
}
