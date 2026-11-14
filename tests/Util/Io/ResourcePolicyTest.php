<?php

declare(strict_types=1);

namespace IterTools\Tests\Util\Io;

use IterTools\Util\Io\ResourcePolicy;
use org\bovigo\vfs\vfsStream;

class ResourcePolicyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test assertIsSatisfied accepts stream resources: files, memory and temp streams, pipes, wrapper streams
     */
    public function testStreamResourcesAreAccepted(): void
    {
        // Given
        $root    = vfsStream::setup('test');
        $streams = [
            \fopen('php://memory', 'r+'),
            \fopen('php://temp', 'r+'),
            \fopen($root->url() . '/' . \uniqid(), 'w'),
            \popen('true', 'r'),
        ];

        // When
        foreach ($streams as $stream) {
            ResourcePolicy::assertIsSatisfied($stream);
        }

        // Then
        $this->expectNotToPerformAssertions();
    }

    /**
     * @test assertIsSatisfied rejects a resource that is not a stream, naming its resource type
     */
    public function testStreamContextIsRejected(): void
    {
        // Given
        $context = \stream_context_create();

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('stream-context');

        // When
        ResourcePolicy::assertIsSatisfied($context);
    }

    /**
     * @test assertIsSatisfied rejects a closed stream
     */
    public function testClosedStreamIsRejected(): void
    {
        // Given
        $stream = \fopen('php://memory', 'r+');
        \fclose($stream);

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        ResourcePolicy::assertIsSatisfied($stream);
    }

    /**
     * @test assertIsSatisfied rejects a value that is not a resource
     */
    public function testNonResourceIsRejected(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        ResourcePolicy::assertIsSatisfied('/not/a/resource');
    }
}
