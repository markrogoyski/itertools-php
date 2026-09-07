<?php

declare(strict_types=1);

namespace IterTools\Tests\Util\Iterators;

use IterTools\Util\Iterators\MemoizedIterable;

class MemoizedIterableTest extends \PHPUnit\Framework\TestCase
{
    public function testInterleavedConsumersShareOneFrontier(): void
    {
        // Given
        $pulls = 0;
        $source = (static function () use (&$pulls): \Generator {
            foreach ([10, 20, 30] as $value) {
                ++$pulls;
                yield $value;
            }
        })();
        $memoized = new MemoizedIterable($source);
        $first = $memoized->getIterator();
        $second = $memoized->getIterator();

        // When / Then
        $this->assertSame(10, $first->current());
        $first->next();
        $this->assertSame(20, $first->current());
        $this->assertSame(10, $second->current());
        $second->next();
        $second->next();
        $this->assertSame(30, $second->current());
        $first->next();
        $this->assertSame(30, $first->current());
        $this->assertSame(3, $pulls);
    }

    /** @dataProvider initializationFailureProvider */
    public function testInitializationMethodFailuresAreReplayed(string $method): void
    {
        // Given
        $failure = new \RuntimeException("$method failed");
        $memoized = new MemoizedIterable(self::failingIterator($method, $failure));

        // When
        $first = self::captureFailure($memoized);
        $second = self::captureFailure($memoized);

        // Then
        $this->assertSame($failure, $first);
        $this->assertSame($failure, $second);
    }

    public static function initializationFailureProvider(): array
    {
        return [
            'rewind' => ['rewind'],
            'valid' => ['valid'],
            'key' => ['key'],
            'current' => ['current'],
        ];
    }

    public function testNextFailureKeepsSuccessfulPrefixAndIsReplayed(): void
    {
        // Given
        $failure = new \RuntimeException('next failed');
        $memoized = new MemoizedIterable(self::failingIterator('next', $failure));

        // When
        [$firstValues, $firstFailure] = self::captureValuesAndFailure($memoized);
        [$secondValues, $secondFailure] = self::captureValuesAndFailure($memoized);

        // Then
        $this->assertSame([10], $firstValues);
        $this->assertSame($firstValues, $secondValues);
        $this->assertSame($failure, $firstFailure);
        $this->assertSame($failure, $secondFailure);
    }

    public function testReentrantFrontierAccessThrowsExactCachedFailure(): void
    {
        // Given
        $memoized = null;
        $source = (static function () use (&$memoized): \Generator {
            foreach ($memoized as $_value) {
            }
            yield 1;
        })();
        $memoized = new MemoizedIterable($source);

        // When
        $first = self::captureFailure($memoized);
        $second = self::captureFailure($memoized);

        // Then
        $this->assertInstanceOf(\LogicException::class, $first);
        $this->assertSame('Memoized iterable cannot advance its source re-entrantly', $first->getMessage());
        $this->assertSame($first, $second);
    }

    private static function failingIterator(string $method, \Throwable $failure): \Iterator
    {
        return new class ($method, $failure) implements \Iterator {
            private int $position = 0;

            public function __construct(private string $method, private \Throwable $failure)
            {
            }

            public function rewind(): void
            {
                $this->fail('rewind');
                $this->position = 0;
            }

            public function valid(): bool
            {
                $this->fail('valid');
                return $this->position === 0;
            }

            public function key(): int
            {
                $this->fail('key');
                return 7;
            }

            public function current(): int
            {
                $this->fail('current');
                return 10;
            }

            public function next(): void
            {
                $this->fail('next');
                ++$this->position;
            }

            private function fail(string $method): void
            {
                if ($this->method === $method) {
                    throw $this->failure;
                }
            }
        };
    }

    private static function captureFailure(iterable $memoized): \Throwable
    {
        try {
            \iterator_to_array($memoized);
        } catch (\Throwable $failure) {
            return $failure;
        }

        throw new \LogicException('Expected memoized source to fail');
    }

    /** @return array{0: list<int>, 1: \Throwable} */
    private static function captureValuesAndFailure(iterable $memoized): array
    {
        $values = [];
        try {
            foreach ($memoized as $value) {
                $values[] = $value;
            }
        } catch (\Throwable $failure) {
            return [$values, $failure];
        }

        throw new \LogicException('Expected memoized source to fail');
    }
}
