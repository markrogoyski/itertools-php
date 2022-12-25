<?php

declare(strict_types=1);

namespace IterTools;

/**
 * @phpstan-type DictAccess = array<string, mixed>|\ArrayAccess<string, mixed>|object
 */
class Tree
{
    /**
     *
     *
     * @param iterable<DictAccess> $data
     * @param ?string              $childrenContainerKey
     * @param int                  $initialLevel
     *
     * @return \Generator
     */
    public static function flattenDeep(
        iterable $data,
        ?string $childrenContainerKey = null,
        int $initialLevel = 0
    ): \Generator {
        $level = $initialLevel;
        foreach($data as $datum) {
            yield $level => $datum;
            if ($childrenContainerKey !== null) {
                $childrenContainer = static::accessValue($datum, $childrenContainerKey);
            } else {
                $childrenContainer = $datum;
            }
            if (is_iterable($childrenContainer)) {
                yield from static::flattenDeep($childrenContainer, $childrenContainerKey, $level+1);
            }
        }
    }

    /**
     *
     *
     * @param iterable<DictAccess> $data
     * @param ?string              $childrenContainerKey
     * @param int                  $initialLevel
     *
     * @return \Generator
     */
    public static function flattenWide(
        iterable $data,
        ?string $childrenContainerKey = null,
        int $initialLevel = 0
    ): \Generator {
        $level = $initialLevel;

        do {
            $subLevelContainer = [];
            foreach($data as $datum) {
                yield $level => $datum;
                if ($childrenContainerKey !== null) {
                    $childrenContainer = static::accessValue($datum, $childrenContainerKey);
                } else {
                    $childrenContainer = $datum;
                }
                if (is_iterable($childrenContainer)) {
                    foreach($childrenContainer as $child) {
                        $subLevelContainer[] = $child;
                    }
                }
            }
            $data = $subLevelContainer;
            ++$level;
        } while(count($subLevelContainer));
    }

    /**
     * @param DictAccess|mixed $container
     * @param string $key
     * @return mixed|null
     */
    protected static function accessValue($container, string $key)
    {
        switch(true) {
            case is_array($container) || $container instanceof \ArrayAccess:
                if (array_key_exists($key, $container)) {
                    return $container[$key];
                }
                break;
            case $container instanceof \stdClass:
                if (property_exists($container, $key)) {
                    return $container->{$childrenContainerKey};
                }
                break;
            case is_object($container):
                if (property_exists($container, $key)) {
                    $ref = new \ReflectionProperty(get_class($container), $key);
                    if ($ref->isPublic()) {
                        return $container->{$childrenContainerKey};
                    }
                }

                $getterName = 'get'.ucfirst($key);
                if (method_exists($container, $getterName)) {
                    $ref = new \ReflectionMethod(get_class($container), $getterName);
                    if ($ref->isPublic()) {
                        return $container->{$getterName}();
                    }
                }
                break;
        }

        return null;
    }
}
