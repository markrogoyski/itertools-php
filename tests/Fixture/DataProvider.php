<?php

namespace IterTools\Tests\Fixture;

use IterTools\Tests\Fixture;

trait DataProvider
{
    public function dataProviderForEmptyIterable(): array
    {
        return [
            [[]],
            [Fixture\GeneratorFixture::getGenerator([])],
            [new Fixture\ArrayIteratorFixture([])],
            [new Fixture\IteratorAggregateFixture([])],
        ];
    }
}
