<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToStringTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         toString - no glue, no prefix, no suffix
     * @dataProvider dataProviderForNoGlueNoPrefixNoSuffix
     * @param        iterable<mixed> $data
     * @param        string $expected
     */
    public function testToStringNoGlueNoPrefixNoSuffix(iterable $data, string $expected)
    {
        // When
        $result = Reduce::toString($data);

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForNoGlueNoPrefixNoSuffix(): array
    {
        return [
            [
                [],
                ''
            ],
            [
                [0],
                '0'
            ],
            [
                [1],
                '1'
            ],
            [
                [1.5],
                '1.5'
            ],
            [
                [true],
                '1'
            ],
            [
                [false],
                ''
            ],
            [
                [0, 1],
                '01'
            ],
            [
                [0, 1, 2],
                '012'
            ],
            [
                [0, 1, 2, 3],
                '0123'
            ],
            [
                [0, 1, 2, 3, 4],
                '01234'
            ],
            [
                ['Iter', 'Tools', 'PHP'],
                'IterToolsPHP'
            ],
            [
                ['Iter', 'Tools', 'v', 1],
                'IterToolsv1'
            ],
            [
                [0, 1, 2, 3, 4],
                '01234'
            ],
            [
                GeneratorFixture::getGenerator([0, 1, 2, 3, 4]),
                '01234'
            ],
            [
                new ArrayIteratorFixture([0, 1, 2, 3, 4]),
                '01234'
            ],
            [
                new IteratorAggregateFixture([0, 1, 2, 3, 4]),
                '01234'
            ],
        ];
    }

    /**
     * @test         toString - with glue, no prefix, no suffix
     * @dataProvider dataProviderForWithGlueNoPrefixNoSuffix
     * @param        iterable<mixed> $data
     * @param        string $separator
     * @param        string $expected
     */
    public function testToStringWithGlueNoPrefixNoSuffix(iterable $data, string $separator, string $expected)
    {
        // When
        $result = Reduce::toString($data, $separator);

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForWithGlueNoPrefixNoSuffix(): array
    {
        return [
            [
                [],
                '',
                ''
            ],
            [
                [],
                ', ',
                ''
            ],
            [
                [0],
                '',
                '0'
            ],
            [
                [0],
                ', ',
                '0'
            ],
            [
                [1],
                '',
                '1'
            ],
            [
                [1],
                ', ',
                '1'
            ],
            [
                [1.5],
                '',
                '1.5'
            ],
            [
                [1.5],
                ', ',
                '1.5'
            ],
            [
                [true],
                '',
                '1'
            ],
            [
                [true],
                ', ',
                '1'
            ],
            [
                [false],
                '',
                ''
            ],
            [
                [false],
                ', ',
                ''
            ],
            [
                [0, 1],
                '',
                '01'
            ],
            [
                [0, 1],
                ', ',
                '0, 1'
            ],
            [
                [0, 1, 2],
                '',
                '012'
            ],
            [
                [0, 1, 2],
                ', ',
                '0, 1, 2'
            ],
            [
                [0, 1, 2, 3],
                '',
                '0123'
            ],
            [
                [0, 1, 2, 3],
                ',',
                '0,1,2,3'
            ],
            [
                [0, 1, 2, 3],
                ', ',
                '0, 1, 2, 3'
            ],
            [
                [0, 1, 2, 3, 4],
                '',
                '01234'
            ],
            [
                [0, 1, 2, 3, 4],
                '.',
                '0.1.2.3.4'
            ],
            [
                [0, 1, 2, 3, 4],
                ' - ',
                '0 - 1 - 2 - 3 - 4'
            ],
            [
                ['Iter', 'Tools', 'PHP'],
                '',
                'IterToolsPHP'
            ],
            [
                ['Iter', 'Tools', 'PHP'],
                '-',
                'Iter-Tools-PHP'
            ],
            [
                ['Iter', 'Tools', 'v', 1],
                '',
                'IterToolsv1'
            ],
            [
                ['Iter', 'Tools', 'v', 1],
                '_',
                'Iter_Tools_v_1'
            ],
            [
                GeneratorFixture::getGenerator([0, 1, 2, 3, 4]),
                ', ',
                '0, 1, 2, 3, 4'
            ],
            [
                new ArrayIteratorFixture([0, 1, 2, 3, 4]),
                ', ',
                '0, 1, 2, 3, 4'
            ],
            [
                new IteratorAggregateFixture([0, 1, 2, 3, 4]),
                ', ',
                '0, 1, 2, 3, 4'
            ],
        ];
    }

    /**
     * @test         toString - with glue, prefix, and suffix
     * @dataProvider dataProviderForWithGlueWithPrefixWithSuffix
     * @param iterable<mixed> $data
     * @param string          $separator
     * @param string          $prefix
     * @param string          $suffix
     * @param string          $expected
     */
    public function testToStringWithGlueWithPrefixWithSuffix(iterable $data, string $separator, string $prefix, string $suffix, string $expected)
    {
        // When
        $result = Reduce::toString($data, $separator, $prefix, $suffix);

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForWithGlueWithPrefixWithSuffix(): array
    {
        return [
            [
                [],
                '',
                '',
                '',
                ''
            ],
            [
                [],
                ', ',
                'prefix:',
                '-suffix',
                'prefix:-suffix'
            ],
            [
                [0],
                '',
                '',
                '',
                '0'
            ],
            [
                [0],
                '',
                'prefix:',
                '',
                'prefix:0'
            ],
            [
                [0],
                '',
                '',
                '-suffix',
                '0-suffix'
            ],
            [
                [0],
                '',
                'prefix-',
                '-suffix',
                'prefix-0-suffix'
            ],
            [
                [0],
                ', ',
                'prefix:',
                '-suffix',
                'prefix:0-suffix'
            ],
            [
                [1],
                '',
                '',
                '',
                '1'
            ],
            [
                [1],
                ', ',
                'prefix:',
                '-suffix',
                'prefix:1-suffix'
            ],
            [
                [1.5],
                '',
                '',
                '',
                '1.5'
            ],
            [
                [1.5],
                ', ',
                'prefix:',
                '-suffix',
                'prefix:1.5-suffix'
            ],
            [
                [true],
                '',
                '',
                '',
                '1'
            ],
            [
                [true],
                ', ',
                'prefix:',
                '-suffix',
                'prefix:1-suffix'
            ],
            [
                [false],
                '',
                '',
                '',
                ''
            ],
            [
                [false],
                ', ',
                'prefix:',
                '-suffix',
                'prefix:-suffix'
            ],
            [
                [0, 1],
                '',
                '',
                '',
                '01'
            ],
            [
                [0, 1],
                ', ',
                'prefix:',
                '-suffix',
                'prefix:0, 1-suffix'
            ],
            [
                [0, 1, 2],
                '',
                '',
                '',
                '012'
            ],
            [
                [0, 1, 2],
                ', ',
                'prefix:',
                '-suffix',
                'prefix:0, 1, 2-suffix'
            ],
            [
                [0, 1, 2, 3],
                '',
                '',
                '',
                '0123'
            ],
            [
                [0, 1, 2, 3],
                ',',
                'prefix:',
                '-suffix',
                'prefix:0,1,2,3-suffix'
            ],
            [
                [0, 1, 2, 3],
                ', ',
                'prefix:',
                '-suffix',
                'prefix:0, 1, 2, 3-suffix'
            ],
            [
                [0, 1, 2, 3, 4],
                '',
                '',
                '',
                '01234'
            ],
            [
                [0, 1, 2, 3, 4],
                '.',
                'prefix:',
                '-suffix',
                'prefix:0.1.2.3.4-suffix'
            ],
            [
                [0, 1, 2, 3, 4],
                ' - ',
                'prefix::',
                '::suffix',
                'prefix::0 - 1 - 2 - 3 - 4::suffix'
            ],
            [
                ['Iter', 'Tools', 'PHP'],
                '',
                '',
                '',
                'IterToolsPHP'
            ],
            [
                ['Iter', 'Tools', 'PHP'],
                '-',
                'prefix::',
                '::suffix',
                'prefix::Iter-Tools-PHP::suffix'
            ],
            [
                ['Iter', 'Tools', 'v', 1],
                '',
                '',
                '',
                'IterToolsv1'
            ],
            [
                ['Iter', 'Tools', 'v', 1],
                '_',
                'prefix:',
                '-suffix',
                'prefix:Iter_Tools_v_1-suffix'
            ],
            [
                GeneratorFixture::getGenerator([0, 1, 2, 3, 4]),
                ', ',
                'prefix:',
                '-suffix',
                'prefix:0, 1, 2, 3, 4-suffix'
            ],
            [
                new ArrayIteratorFixture([0, 1, 2, 3, 4]),
                ', ',
                'prefix:',
                '-suffix',
                'prefix:0, 1, 2, 3, 4-suffix'
            ],
            [
                new IteratorAggregateFixture([0, 1, 2, 3, 4]),
                ', ',
                'prefix:',
                '-suffix',
                'prefix:0, 1, 2, 3, 4-suffix'
            ],
        ];
    }
}
