<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ConsumeTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test consume drains a generator
     */
    public function testDrainsGenerator(): void
    {
        // Given
        $count     = 0;
        $generator = (function () use (&$count): \Generator {
            foreach ([1, 2, 3, 4, 5] as $n) {
                ++$count;
                yield $n;
            }
        })();

        // When
        Reduce::consume($generator);

        // Then
        $this->assertSame(5, $count);
    }

    /**
     * @test consume drains an array
     */
    public function testDrainsArray(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = Reduce::consume($data);

        // Then
        $this->assertNull($result);
    }

    /**
     * @test consume drains an Iterator
     */
    public function testDrainsIterator(): void
    {
        // Given
        $iterable = new ArrayIteratorFixture([1, 2, 3]);

        // When
        Reduce::consume($iterable);

        // Then
        $this->assertFalse($iterable->valid());
    }

    /**
     * @test consume drains an IteratorAggregate
     */
    public function testDrainsIteratorAggregate(): void
    {
        // Given
        $iterable = new IteratorAggregateFixture([1, 2, 3]);

        // When
        Reduce::consume($iterable);

        // Then
        $this->addToAssertionCount(1);
    }

    /**
     * @test         consume on empty iterable is a no-op
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIsNoOp(iterable $data): void
    {
        // When
        Reduce::consume($data);

        // Then
        $this->addToAssertionCount(1);
    }

    /**
     * @test consume returns void
     */
    public function testReturnsVoid(): void
    {
        // Given
        $reflection = new \ReflectionMethod(Reduce::class, 'consume');

        // When
        $returnType = $reflection->getReturnType();

        // Then
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertSame('void', $returnType->getName());
    }

    /**
     * @test consume drains a Generator built from a key-value source
     */
    public function testDrainsKeyValueGenerator(): void
    {
        // Given
        $iterable = GeneratorFixture::getKeyValueGenerator(['a' => 1, 'b' => 2, 'c' => 3]);

        // When
        Reduce::consume($iterable);

        // Then
        $this->assertFalse($iterable->valid());
    }

    /**
     * @test consume runs side effects in order
     */
    public function testSideEffectOrderPreserved(): void
    {
        // Given
        $observed  = [];
        $generator = (function () use (&$observed): \Generator {
            foreach ([10, 20, 30] as $n) {
                $observed[] = $n;
                yield $n;
            }
        })();

        // When
        Reduce::consume($generator);

        // Then
        $this->assertSame([10, 20, 30], $observed);
    }
}
