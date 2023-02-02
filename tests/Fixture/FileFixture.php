<?php

namespace IterTools\Tests\Fixture;

class FileFixture
{
    /**
     * @param array<string> $lines
     * @param string $dirPath
     * @return resource
     */
    public static function createFromLines(array $lines, string $dirPath)
    {
        return self::createFromString(implode(\PHP_EOL, $lines), $dirPath);
    }

    /**
     * @param string $string
     * @param string $dirPath
     * @return resource
     */
    public static function createFromString(string $string, string $dirPath)
    {
        $fileName = \uniqid();
        \file_put_contents("{$dirPath}/{$fileName}", $string);

        return \fopen("{$dirPath}/{$fileName}", 'r');
    }
}
