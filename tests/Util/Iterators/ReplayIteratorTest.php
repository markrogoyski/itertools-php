<?php

declare(strict_types=1);

namespace IterTools\Tests\Util\Iterators;

use IterTools\Tests\Fixture;
use IterTools\Util\Iterators\MemoizedIterable;
use IterTools\Util\Iterators\ReplayIterator;

class ReplayIteratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test every pass yields the same keys and values as the source
     * @dataProvider dataProviderForSources
     * @param iterable $source
     * @param array    $expected
     */
    public function testEveryPassMatchesSource(iterable $source, array $expected): void
    {
        // Given
        $iterator = new ReplayIterator(new MemoizedIterable($source));

        // When
        $firstPass  = \iterator_to_array($iterator);
        $secondPass = \iterator_to_array($iterator);
        $thirdPass  = \iterator_to_array($iterator);

        // Then
        $this->assertSame($expected, $firstPass);
        $this->assertSame($expected, $secondPass);
        $this->assertSame($expected, $thirdPass);
    }

    public static function dataProviderForSources(): array
    {
        return [
            'array' => [
                ['a' => 1, 'b' => 2, 'c' => 3],
                ['a' => 1, 'b' => 2, 'c' => 3],
            ],
            'Generator' => [
                Fixture\GeneratorFixture::getKeyValueGenerator(['a' => 1, 'b' => 2, 'c' => 3]),
                ['a' => 1, 'b' => 2, 'c' => 3],
            ],
            'Iterator' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                [1, 2, 3],
            ],
            'IteratorAggregate' => [
                new Fixture\IteratorAggregateFixture(['a' => 1, 'b' => 2, 'c' => 3]),
                ['a' => 1, 'b' => 2, 'c' => 3],
            ],
            'empty Generator' => [
                Fixture\GeneratorFixture::getGenerator([]),
                [],
            ],
            'Generator yielding null' => [
                Fixture\GeneratorFixture::getGenerator([null, 0, null]),
                [null, 0, null],
            ],
        ];
    }

    /**
     * @test the source is not opened before the first rewind and is opened only once
     */
    public function testSourceOpenedOnceOnFirstRewind(): void
    {
        // Given
        $aggregate = new Fixture\CountingIteratorAggregateFixture([1, 2]);

        // When
        $iterator = new ReplayIterator(new MemoizedIterable($aggregate));

        // Then
        $this->assertSame(0, $aggregate->getIteratorCallCount());

        // When
        $iterator->rewind();
        $iterator->rewind();
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertSame([1, 2], $result);
        $this->assertSame(1, $aggregate->getIteratorCallCount());
    }

    /**
     * @test a rewind part-way through the first pass replays the cache and then resumes reading the source
     */
    public function testRewindDuringFirstPassResumesSource(): void
    {
        // Given
        $pulled   = 0;
        $source   = (function () use (&$pulled) {
            foreach ([1, 2, 3, 4] as $value) {
                ++$pulled;
                yield $value;
            }
        })();
        $iterator = new ReplayIterator(new MemoizedIterable($source));

        // When
        $iterator->rewind();
        $iterator->next();
        $partial = [$iterator->current(), $pulled];
        $iterator->rewind();
        $result = \iterator_to_array($iterator, false);

        // Then
        $this->assertSame([2, 2], $partial);
        $this->assertSame([1, 2, 3, 4], $result);
        $this->assertSame(4, $pulled);
    }

    /**
     * @test past the end, current() and key() return null and next() stays put
     */
    public function testPastTheEnd(): void
    {
        // Given
        $iterator = new ReplayIterator(new MemoizedIterable(Fixture\GeneratorFixture::getGenerator([1])));

        // When
        $iterator->rewind();
        $iterator->next();
        $iterator->next();

        // Then
        $this->assertFalse($iterator->valid());
        $this->assertNull($iterator->current());
        $this->assertNull($iterator->key());
    }
}
