<?php

namespace IterTools\Tests\Fixture;

class FileFixture
{
    public static function createFromLines(array $lines)
    {
        return self::createFromString(implode("\n", $lines));
    }

    public static function createFromString(string $string)
    {
        $file = tmpfile();
        fwrite($file, $string);
        fseek($file, 0);

        return $file;
    }
}