<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

class NonSerializableFixtureTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test stores id via constructor
     */
    public function testStoresId(): void
    {
        // Given
        $id = 42;

        // When
        $fixture = new NonSerializableFixture($id);

        // Then
        $this->assertSame($id, $fixture->id);
    }

    /**
     * @test serialize throws exception
     */
    public function testSerializeThrowsException(): void
    {
        // Given
        $fixture = new NonSerializableFixture(1);

        // Then
        $this->expectException(\Exception::class);

        // When
        \serialize($fixture);
    }

    /**
     * @test unserialize throws exception
     */
    public function testUnserializeThrowsException(): void
    {
        // Given
        $fixture = new NonSerializableFixture(1);

        // Then
        $this->expectException(\Exception::class);

        // When
        $fixture->__unserialize([]);
    }

    /**
     * @test two instances with same id are equal but not identical
     */
    public function testTwoInstancesWithSameIdAreEqualButNotIdentical(): void
    {
        // Given
        $a = new NonSerializableFixture(1);
        $b = new NonSerializableFixture(1);

        // Then
        $this->assertEquals($a, $b);
        $this->assertNotSame($a, $b);
    }
}
