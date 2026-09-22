<?php

declare(strict_types=1);

namespace IterTools\Tests\Util;

use IterTools\Tests\Fixture;
use IterTools\Tests\Fixture\EnumFixture;
use IterTools\Tests\Fixture\NonSerializableFixture;
use IterTools\Util\UniqueExtractor;

/**
 * Key strings are internal to UniqueExtractor: these tests only assert which values share a key
 * and which do not, never how a key is spelled.
 */
class UniqueExtractorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test strict keys are equal exactly when the values are identical (NAN equal to NAN)
     */
    public function testStrictOracle(): void
    {
        // Given
        $pool = $this->strictPool();

        // When + Then
        $this->assertStrictOracle($pool);
    }

    /**
     * @test strict keys are equal exactly when the values are identical, over a seeded random sample
     */
    public function testStrictOracleRandomSample(): void
    {
        // Given
        \mt_srand(12345);
        $values = [];
        for ($i = 0; $i < 60; $i++) {
            $values[] = $this->randomNumericValue();
        }

        // When + Then
        for ($i = 0; $i < 400; $i++) {
            $a = $values[\mt_rand(0, 59)];
            $b = $values[\mt_rand(0, 59)];

            $this->assertSame(
                $this->strictEquals($a, $b),
                UniqueExtractor::getString($a, true) === UniqueExtractor::getString($b, true),
                \sprintf('strict: %s vs %s', $this->describe($a), $this->describe($b)),
            );
        }
    }

    /**
     * @test values within one coercive equivalence class share a key
     * @dataProvider dataProviderForCoerciveEquivalenceClasses
     * @param list<mixed> $equivalenceClass
     */
    public function testCoerciveEquivalenceClassSharesKey(array $equivalenceClass): void
    {
        // When + Then
        foreach ($equivalenceClass as $a) {
            foreach ($equivalenceClass as $b) {
                $this->assertSame(
                    UniqueExtractor::getString($a, false),
                    UniqueExtractor::getString($b, false),
                    \sprintf('coercive: %s vs %s', $this->describe($a), $this->describe($b)),
                );
            }
        }
    }

    /**
     * @return list<array{list<mixed>}>
     */
    public static function dataProviderForCoerciveEquivalenceClasses(): array
    {
        return self::coerciveEquivalenceClassRows();
    }

    /**
     * @test values in different coercive equivalence classes never share a key
     */
    public function testCoerciveEquivalenceClassesAreDisjoint(): void
    {
        // Given
        $table = self::coerciveTable();

        // When + Then
        $this->assertCoerciveTable($table);
    }

    /**
     * @test keys do not depend on the precision and serialize_precision ini settings
     */
    public function testKeysAreIndependentOfIniPrecision(): void
    {
        // Given
        $precision = \ini_get('precision');
        $serializePrecision = \ini_get('serialize_precision');
        $pool = $this->strictPool();
        $table = self::coerciveTable();

        // When + Then
        try {
            foreach (['14', '17'] as $precisionSetting) {
                foreach (['-1', '17'] as $serializePrecisionSetting) {
                    \ini_set('precision', $precisionSetting);
                    \ini_set('serialize_precision', $serializePrecisionSetting);

                    $this->assertStrictOracle($pool);
                    $this->assertCoerciveTable($table);
                }
            }
        } finally {
            \ini_set('precision', $precision === false ? '14' : $precision);
            \ini_set('serialize_precision', $serializePrecision === false ? '-1' : $serializePrecision);
        }
    }

    /**
     * @test a resource keeps its key when it is closed
     */
    public function testClosedResourceKeepsItsKey(): void
    {
        // Given
        $resource = \fopen('php://memory', 'r');
        $strictWhileOpen = UniqueExtractor::getString($resource, true);
        $coerciveWhileOpen = UniqueExtractor::getString($resource, false);

        // When
        \fclose($resource);

        // Then
        $this->assertSame($strictWhileOpen, UniqueExtractor::getString($resource, true));
        $this->assertSame($coerciveWhileOpen, UniqueExtractor::getString($resource, false));
    }

    /**
     * @test two distinct closed resources have distinct keys
     */
    public function testDistinctClosedResourcesHaveDistinctKeys(): void
    {
        // Given
        $first = \fopen('php://memory', 'r');
        $second = \fopen('php://memory', 'r');

        // When
        \fclose($first);
        \fclose($second);

        // Then
        $this->assertNotSame(
            UniqueExtractor::getString($first, true),
            UniqueExtractor::getString($second, true),
        );
        $this->assertNotSame(
            UniqueExtractor::getString($first, false),
            UniqueExtractor::getString($second, false),
        );
    }

    /**
     * @test hex string is not treated as numeric in PHP 8 coercive mode
     */
    public function testHexStringNotNumericCoercive(): void
    {
        // When
        $hexString = UniqueExtractor::getString('0x1A', false);
        $decimal = UniqueExtractor::getString(26, false);

        // Then — "0x1A" is not numeric in PHP 8
        $this->assertNotSame($hexString, $decimal);
    }

    /**
     * @test non-serializable object hashes by instance in strict mode
     */
    public function testNonSerializableObjectStrictHashesByInstance(): void
    {
        // Given
        $object = new NonSerializableFixture(1);
        $otherWithSameState = new NonSerializableFixture(1);

        // When
        $key = UniqueExtractor::getString($object, true);

        // Then
        $this->assertSame($key, UniqueExtractor::getString($object, true));
        $this->assertNotSame($key, UniqueExtractor::getString($otherWithSameState, true));
    }

    /**
     * @test non-serializable object in coercive mode throws InvalidArgumentException
     */
    public function testNonSerializableObjectNonStrictThrowsException(): void
    {
        // Given
        $object = new NonSerializableFixture(1);

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('NonSerializableFixture');

        // When
        UniqueExtractor::getString($object, false);
    }

    /**
     * @test non-serializable object exception wraps original cause
     */
    public function testNonSerializableObjectExceptionWrapsOriginalCause(): void
    {
        // Given
        $object = new NonSerializableFixture(1);

        // When
        try {
            UniqueExtractor::getString($object, false);
            $this->fail('Expected InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertNotNull($e->getPrevious());
            $this->assertStringContainsString('cannot be serialized', $e->getPrevious()->getMessage());
        }
    }

    /**
     * Asserts the strict contract over every ordered pair of the pool: keys are equal iff the
     * values are identical, with NAN equal to NAN.
     *
     * @param list<mixed> $pool
     */
    private function assertStrictOracle(array $pool): void
    {
        foreach ($pool as $a) {
            foreach ($pool as $b) {
                $this->assertSame(
                    $this->strictEquals($a, $b),
                    UniqueExtractor::getString($a, true) === UniqueExtractor::getString($b, true),
                    \sprintf('strict: %s vs %s', $this->describe($a), $this->describe($b)),
                );
            }
        }
    }

    /**
     * Asserts the coercive contract over the equivalence table: two values share a key iff they
     * belong to the same class.
     *
     * @param list<list<mixed>> $table
     */
    private function assertCoerciveTable(array $table): void
    {
        foreach ($table as $indexA => $classA) {
            foreach ($table as $indexB => $classB) {
                foreach ($classA as $a) {
                    foreach ($classB as $b) {
                        $this->assertSame(
                            $indexA === $indexB,
                            UniqueExtractor::getString($a, false) === UniqueExtractor::getString($b, false),
                            \sprintf('coercive: %s vs %s', $this->describe($a), $this->describe($b)),
                        );
                    }
                }
            }
        }
    }

    /**
     * PHP's === extended so that NAN equals NAN, recursively through arrays.
     *
     * @param mixed $a
     * @param mixed $b
     */
    private function strictEquals($a, $b): bool
    {
        if (\is_float($a) && \is_float($b) && \is_nan($a) && \is_nan($b)) {
            return true;
        }

        if (\is_array($a) && \is_array($b)) {
            if (\count($a) !== \count($b)) {
                return false;
            }

            $keysOfB = \array_keys($b);
            $position = 0;
            foreach ($a as $key => $value) {
                if ($keysOfB[$position] !== $key) {
                    return false;
                }
                if (!$this->strictEquals($value, $b[$key])) {
                    return false;
                }
                $position++;
            }

            return true;
        }

        return $a === $b;
    }

    /**
     * Human-readable label for an assertion message.
     *
     * @param mixed $value
     */
    private function describe($value): string
    {
        return match (true) {
            \is_resource($value) => 'open resource #' . \intval($value),
            \gettype($value) === 'resource (closed)' => 'closed resource #' . \intval($value),
            $value instanceof \Closure => 'Closure #' . \spl_object_id($value),
            $value instanceof \Generator => 'Generator #' . \spl_object_id($value),
            $value instanceof \UnitEnum => $value::class . '::' . $value->name,
            \is_object($value) => $value::class . ' #' . \spl_object_id($value),
            default => \var_export($value, true),
        };
    }

    /**
     * Deterministic pool of edge values for the strict oracle.
     *
     * @return list<mixed>
     */
    private function strictPool(): array
    {
        $object1 = new \stdClass();
        $object2 = new \stdClass();
        $closure = static fn (int $x): int => $x + 1;
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $openResource1 = \fopen('php://memory', 'r');
        $openResource2 = \fopen('php://memory', 'r');
        $closedResource = \fopen('php://memory', 'r');
        \fclose($closedResource);

        $pool = [
            0,
            -0.0,
            0.0,
            0.1 + 0.2,
            0.3,
            1.000000000000001,
            1.000000000000002,
            \PHP_INT_MAX,
            (float) \PHP_INT_MAX,
            \PHP_INT_MIN,
            (float) \PHP_INT_MIN,
            1e308,
            -1e308,
            \PHP_FLOAT_MIN,
            \PHP_FLOAT_EPSILON,
            \INF,
            -\INF,
            \NAN,
            '',
            '0',
            ' 1',
            '1 ',
            '1e2',
            'abc',
            'INF',
            "a\0b",
            true,
            false,
            null,
            $object1,
            $object2,
            $object1,
            EnumFixture::One,
            $closure,
            $generator,
            $openResource1,
            $openResource2,
            $closedResource,
            $closedResource,
        ];

        if (\PHP_INT_SIZE === 8) {
            $pool[] = 9007199254740992;
            $pool[] = 9007199254740993;
        }

        return $pool;
    }

    /**
     * The coercive equivalence table: values share a key iff they are in the same class.
     *
     * @return list<list<mixed>>
     */
    private static function coerciveTable(): array
    {
        $object1 = new \stdClass();
        $object1->value = 1;
        $object2 = new \stdClass();
        $object2->value = 1;
        $closure1 = static fn (int $x): int => $x + 1;
        $closure2 = static fn (int $x): int => $x + 2;
        $openResource1 = \fopen('php://memory', 'r');
        $openResource2 = \fopen('php://memory', 'r');
        $closedResource = \fopen('php://memory', 'r');
        \fclose($closedResource);

        $table = [
            [0, 0.0, -0.0, '0', '0.0', ' 0', false, null, ''],
            [1, 1.0, '1', '1.0', '1e0', ' 1', '1 ', true],
            [100, 100.0, '1e2', '100'],
            [0.1 + 0.2],
            [0.3],
            [\PHP_INT_MAX, (string) \PHP_INT_MAX],
            [\PHP_INT_MIN, (string) \PHP_INT_MIN, (float) \PHP_INT_MIN],
            [\INF],
            [-\INF],
            ['INF'],
            ['abc'],
            ['ABC'],
            [\NAN],
            [$object1, $object2],
            [$closure1],
            [$closure2],
            [$openResource1],
            [$openResource2],
            [$closedResource, $closedResource],
        ];

        if (\PHP_INT_SIZE === 8) {
            $table[] = [9007199254740992, '9007199254740992', 9007199254740992.0, '9007199254740993.0'];
            $table[] = [9007199254740993, '9007199254740993'];
            $table[] = [(float) \PHP_INT_MAX, -(float) \PHP_INT_MIN];
        }

        return $table;
    }

    /**
     * The coercive equivalence table in data-provider row shape: one class per row.
     *
     * @return list<array{list<mixed>}>
     */
    private static function coerciveEquivalenceClassRows(): array
    {
        $rows = [];
        foreach (self::coerciveTable() as $equivalenceClass) {
            $rows[] = [$equivalenceClass];
        }

        return $rows;
    }

    /**
     * One reproducible random number or numeric string, drawn from the seeded mt_rand stream.
     *
     * @return int|float|string
     */
    private function randomNumericValue()
    {
        return match (\mt_rand(0, 5)) {
            0 => \mt_rand(-8, 8),
            1 => \PHP_INT_MAX - \mt_rand(0, 5),
            2 => \mt_rand(-8, 8) / 4.0,
            3 => \mt_rand(1, 1 << 20) * (2.0 ** \mt_rand(-30, 30)),
            4 => (string) \mt_rand(-8, 8),
            default => (string) (\mt_rand(-8, 8) / 4.0),
        };
    }
}
