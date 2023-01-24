<?php

namespace IterTools\Tests\Fixture;

class FileFixture
{
    /**
     * @param array<string> $lines
     * @return resource
     */
    public static function createFromLines(array $lines)
    {
        return self::createFromString(implode("\n", $lines));
    }

    /**
     * @param string $string
     * @return resource
     */
    public static function createFromString(string $string)
    {
        $file = tmpfile();
        fwrite($file, $string);
        fseek($file, 0);

        return $file;
    }
}
