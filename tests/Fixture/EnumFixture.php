<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

/**
 * Pure enum used to exercise enum cases in equality and hashing tests.
 */
enum EnumFixture
{
    case One;
    case Two;
}
