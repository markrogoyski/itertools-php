<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi;

use IterTools\Multi;
use IterTools\Tests\Fixture;

class IsIterableTest extends \PHPUnit\Framework\TestCase
{
    use Fixture\DataProvider;

    /**
     * @test loop tools is_iterable
     * @dataProvider dataProviderForIterableLoopTools
     * @dataProvider dataProviderForIterableStreamTools
     * @param iterable $loopToolIterable
     * @return void
     */
    public function testLoopToolsAreIterable(iterable $loopToolIterable): void
    {
        // When
        $isIterable = \is_iterable($loopToolIterable);

        // Then
        $this->assertTrue($isIterable);
    }
}
