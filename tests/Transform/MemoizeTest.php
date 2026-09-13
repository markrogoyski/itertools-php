<?php

declare(strict_types=1);

namespace IterTools\Tests\Transform;

use IterTools\Single;
use IterTools\Transform;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\CountingIteratorAggregateFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class MemoizeTest extends \PHPUnit\Framework\TestCase
{
    /** @dataProvider replayableSourceProvider */
    public function testReplaysEverySupportedSource(iterable $source): void
    {
        // Given
        $memoized = Transform::memoize($source);

        // When
        $first = \iterator_to_array($memoized, false);
        $second = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame([1, 2, 3], $first);
        $this->assertSame($first, $second);
    }

    public static function replayableSourceProvider(): array
    {
        return [
            'array' => [[1, 2, 3]],
            'generator' => [(static function (): \Generator {
                yield from [1, 2, 3];
            })()],
            'iterator' => [new ArrayIteratorFixture([1, 2, 3])],
            'iterator aggregate' => [new IteratorAggregateFixture([1, 2, 3])],
        ];
    }

    public function testReplaysOneShotSourceAndPreservesDuplicateKeys(): void
    {
        // Given
        $pulls = 0;
        $source = (static function () use (&$pulls): \Generator {
            ++$pulls;
            yield 'a' => 1;
            ++$pulls;
            yield 'a' => 2;
        })();
        $memoized = Transform::memoize($source);

        // When
        $first = \iterator_to_array($memoized, false);
        $second = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame([1, 2], $first);
        $this->assertSame($first, $second);
        $this->assertSame(2, $pulls);
    }

    public function testIsIncrementallyLazyAndSupportsInfiniteSources(): void
    {
        // Given
        $pulls = 0;
        $source = (static function () use (&$pulls): \Generator {
            for ($value = 0;; ++$value) {
                ++$pulls;
                yield $value;
            }
        })();
        $memoized = Transform::memoize($source);
        $this->assertSame(0, $pulls);

        // When
        $first = \iterator_to_array(Single::limit($memoized, 3));
        $second = \iterator_to_array(Single::limit($memoized, 5));

        // Then
        $this->assertSame([0, 1, 2], $first);
        $this->assertSame([0, 1, 2, 3, 4], $second);
        $this->assertSame(5, $pulls);
    }

    public function testPreservesValuesKeysAndObjectIdentityPositionally(): void
    {
        // Given
        $object = new \stdClass();
        $source = (static function () use ($object): \Generator {
            yield 'same' => null;
            yield 'same' => ['array'];
            yield 9 => $object;
        })();
        $memoized = Transform::memoize($source);

        // When
        $tuples = [];
        foreach ($memoized as $key => $value) {
            $tuples[] = [$key, $value];
        }

        // Then
        $this->assertSame('same', $tuples[0][0]);
        $this->assertNull($tuples[0][1]);
        $this->assertSame(['same', ['array']], $tuples[1]);
        $this->assertSame($object, $tuples[2][1]);
    }

    public function testReplaysEmptySourceAndDoesNotReinitialize(): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture([]);
        $memoized = Transform::memoize($aggregate);

        // When
        $first = \iterator_to_array($memoized, false);
        $second = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame([], $first);
        $this->assertSame([], $second);
        $this->assertSame(1, $aggregate->getIteratorCallCount());
    }

    public function testConstructionDoesNotTouchIteratorAggregateSource(): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $memoized = Transform::memoize($aggregate);

        // Then
        $this->assertSame(0, $aggregate->getIteratorCallCount());

        // When
        $first = \iterator_to_array($memoized, false);
        $second = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame([1, 2, 3], $first);
        $this->assertSame([1, 2, 3], $second);
        $this->assertSame(1, $aggregate->getIteratorCallCount());
    }
}
