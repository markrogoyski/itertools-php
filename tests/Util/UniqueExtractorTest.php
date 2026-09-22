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
        $tableWithoutFloatStateObjects = self::coerciveTable(false);

        // When + Then
        try {
            foreach (['14', '17'] as $precisionSetting) {
                foreach (['-1', '17', '14'] as $serializePrecisionSetting) {
                    \ini_set('precision', $precisionSetting);
                    \ini_set('serialize_precision', $serializePrecisionSetting);

                    $this->assertStrictOracle($pool);

                    // serialize_precision = 14 rounds both 0.1 + 0.2 and 0.3 to "0.3", so the two
                    // objects whose only difference is a float property serialize identically and
                    // merge. That is the documented serialize() limitation for coercive objects
                    // (README, "Coercive mode", objects bullet), not a defect of the encoding, so
                    // that pair is left out of the table under this setting instead of asserted.
                    $this->assertCoerciveTable(
                        $serializePrecisionSetting === '14' ? $tableWithoutFloatStateObjects : $table,
                    );
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
     * @test a closed resource nested in an array keeps its identity
     */
    public function testNestedClosedResourceKeepsItsIdentity(): void
    {
        // Given
        $closed = \fopen('php://memory', 'r');
        $otherClosed = \fopen('php://memory', 'r');
        \fclose($closed);
        \fclose($otherClosed);

        // When
        $strictKey = UniqueExtractor::getString([$closed], true);
        $coerciveKey = UniqueExtractor::getString([$closed], false);

        // Then
        $this->assertSame($strictKey, UniqueExtractor::getString([$closed], true));
        $this->assertSame($coerciveKey, UniqueExtractor::getString([$closed], false));
        $this->assertNotSame($strictKey, UniqueExtractor::getString([$otherClosed], true));
        $this->assertNotSame($coerciveKey, UniqueExtractor::getString([$otherClosed], false));
    }

    /**
     * @test an array nested to the depth limit hashes by its contents
     */
    public function testArrayAtTheDepthLimitHashes(): void
    {
        // Given
        $one = 1;
        $two = 2;
        for ($i = 0; $i < UniqueExtractor::MAX_DEPTH; $i++) {
            $one = [$one];
            $two = [$two];
        }

        // When
        $key = UniqueExtractor::getString($one, true);

        // Then
        $this->assertSame($key, UniqueExtractor::getString($one, true));
        $this->assertNotSame($key, UniqueExtractor::getString($two, true));
    }

    /**
     * @test an array nested one level past the depth limit throws
     * @dataProvider dataProviderForStrictFlag
     * @param bool $strict
     */
    public function testArrayPastTheDepthLimitThrows(bool $strict): void
    {
        // Given
        $value = 1;
        for ($i = 0; $i <= UniqueExtractor::MAX_DEPTH; $i++) {
            $value = [$value];
        }

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage((string) UniqueExtractor::MAX_DEPTH);

        // When
        UniqueExtractor::getString($value, $strict);
    }

    /**
     * @test a self-referential array throws the depth-limit exception rather than crashing
     * @dataProvider dataProviderForStrictFlag
     * @param bool $strict
     */
    public function testSelfReferentialArrayThrowsTheDepthLimitException(bool $strict): void
    {
        // Given
        $array = [];
        $array[] = &$array;

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage((string) UniqueExtractor::MAX_DEPTH);

        // When
        UniqueExtractor::getString($array, $strict);
    }

    /**
     * @return list<array{bool}>
     */
    public static function dataProviderForStrictFlag(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @test a closure nested in an array hashes by instance in both modes
     * @dataProvider dataProviderForStrictFlag
     * @param bool $strict
     */
    public function testNestedClosureHashesByInstance(bool $strict): void
    {
        // Given
        $closure = static fn (int $x): int => $x + 1;
        $otherClosure = static fn (int $x): int => $x + 1;

        // When
        $key = UniqueExtractor::getString([[$closure]], $strict);

        // Then
        $this->assertSame($key, UniqueExtractor::getString([[$closure]], $strict));
        $this->assertNotSame($key, UniqueExtractor::getString([[$otherClosure]], $strict));
    }

    /**
     * @test a generator nested in an array hashes by instance in both modes
     * @dataProvider dataProviderForStrictFlag
     * @param bool $strict
     */
    public function testNestedGeneratorHashesByInstance(bool $strict): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $otherGenerator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);

        // When
        $key = UniqueExtractor::getString([[$generator]], $strict);

        // Then
        $this->assertSame($key, UniqueExtractor::getString([[$generator]], $strict));
        $this->assertNotSame($key, UniqueExtractor::getString([[$otherGenerator]], $strict));
    }

    /**
     * @test a non-serializable object nested in an array hashes by instance in strict mode
     */
    public function testNestedNonSerializableObjectStrictHashesByInstance(): void
    {
        // Given
        $object = new NonSerializableFixture(1);
        $otherWithSameState = new NonSerializableFixture(1);

        // When
        $key = UniqueExtractor::getString([$object], true);

        // Then
        $this->assertSame($key, UniqueExtractor::getString([$object], true));
        $this->assertNotSame($key, UniqueExtractor::getString([$otherWithSameState], true));
    }

    /**
     * @test a non-serializable object nested in an array throws in coercive mode
     */
    public function testNestedNonSerializableObjectNonStrictThrowsException(): void
    {
        // Given
        $object = new NonSerializableFixture(1);

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('NonSerializableFixture');

        // When
        UniqueExtractor::getString([$object], false);
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
     * @test strict mode anchors an ordinary object
     */
    public function testIdentifyStrictAnchorsObject(): void
    {
        // Given
        $object = new \stdClass();

        // When
        $identity = UniqueExtractor::identify($object, true);

        // Then
        $this->assertCount(1, $identity->anchors);
        $this->assertSame($object, $identity->anchors[0]);
    }

    /**
     * @test strict mode anchors a closure
     */
    public function testIdentifyStrictAnchorsClosure(): void
    {
        // Given
        $closure = static fn (int $x): int => $x + 1;

        // When
        $identity = UniqueExtractor::identify($closure, true);

        // Then
        $this->assertCount(1, $identity->anchors);
        $this->assertSame($closure, $identity->anchors[0]);
    }

    /**
     * @test strict mode anchors a generator
     */
    public function testIdentifyStrictAnchorsGenerator(): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);

        // When
        $identity = UniqueExtractor::identify($generator, true);

        // Then
        $this->assertCount(1, $identity->anchors);
        $this->assertSame($generator, $identity->anchors[0]);
    }

    /**
     * @test strict mode anchors an enum case
     */
    public function testIdentifyStrictAnchorsEnumCase(): void
    {
        // Given
        $case = EnumFixture::One;

        // When
        $identity = UniqueExtractor::identify($case, true);

        // Then
        $this->assertCount(1, $identity->anchors);
        $this->assertSame($case, $identity->anchors[0]);
    }

    /**
     * @test strict mode anchors an open resource
     */
    public function testIdentifyStrictAnchorsOpenResource(): void
    {
        // Given
        $resource = \fopen('php://memory', 'r');

        // When
        $identity = UniqueExtractor::identify($resource, true);

        // Then
        $this->assertCount(1, $identity->anchors);
        $this->assertSame($resource, $identity->anchors[0]);
    }

    /**
     * @test strict mode anchors a closed resource
     */
    public function testIdentifyStrictAnchorsClosedResource(): void
    {
        // Given
        $resource = \fopen('php://memory', 'r');
        \fclose($resource);

        // When
        $identity = UniqueExtractor::identify($resource, true);

        // Then
        $this->assertCount(1, $identity->anchors);
        $this->assertSame($resource, $identity->anchors[0]);
    }

    /**
     * @test strict mode anchors nothing for a scalar
     * @dataProvider dataProviderForScalars
     * @param mixed $value
     */
    public function testIdentifyStrictAnchorsNothingForScalar($value): void
    {
        // When
        $identity = UniqueExtractor::identify($value, true);

        // Then
        $this->assertSame([], $identity->anchors);
    }

    /**
     * @test coercive mode anchors nothing for an ordinary object
     */
    public function testIdentifyCoerciveAnchorsNothingForObject(): void
    {
        // Given
        $object = new \stdClass();

        // When
        $identity = UniqueExtractor::identify($object, false);

        // Then
        $this->assertSame([], $identity->anchors);
    }

    /**
     * @test coercive mode anchors a closure
     */
    public function testIdentifyCoerciveAnchorsClosure(): void
    {
        // Given
        $closure = static fn (int $x): int => $x + 1;

        // When
        $identity = UniqueExtractor::identify($closure, false);

        // Then
        $this->assertCount(1, $identity->anchors);
        $this->assertSame($closure, $identity->anchors[0]);
    }

    /**
     * @test coercive mode anchors a generator
     */
    public function testIdentifyCoerciveAnchorsGenerator(): void
    {
        // Given
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);

        // When
        $identity = UniqueExtractor::identify($generator, false);

        // Then
        $this->assertCount(1, $identity->anchors);
        $this->assertSame($generator, $identity->anchors[0]);
    }

    /**
     * @test coercive mode anchors a resource
     */
    public function testIdentifyCoerciveAnchorsResource(): void
    {
        // Given
        $resource = \fopen('php://memory', 'r');

        // When
        $identity = UniqueExtractor::identify($resource, false);

        // Then
        $this->assertCount(1, $identity->anchors);
        $this->assertSame($resource, $identity->anchors[0]);
    }

    /**
     * @test coercive mode anchors nothing for a scalar
     * @dataProvider dataProviderForScalars
     * @param mixed $value
     */
    public function testIdentifyCoerciveAnchorsNothingForScalar($value): void
    {
        // When
        $identity = UniqueExtractor::identify($value, false);

        // Then
        $this->assertSame([], $identity->anchors);
    }

    /**
     * @test strict mode anchors every identity nested in an array, in the order it met them
     */
    public function testIdentifyStrictAnchorsNestedIdentities(): void
    {
        // Given
        $object1 = new \stdClass();
        $object2 = new \stdClass();
        $resource = \fopen('php://memory', 'r');

        // When
        $identity = UniqueExtractor::identify([$object1, [$object2, $resource]], true);

        // Then
        $this->assertCount(3, $identity->anchors);
        $this->assertSame($object1, $identity->anchors[0]);
        $this->assertSame($object2, $identity->anchors[1]);
        $this->assertSame($resource, $identity->anchors[2]);
    }

    /**
     * @test coercive mode anchors only the nested resource, since ordinary objects hash by state
     */
    public function testIdentifyCoerciveAnchorsOnlyTheNestedResource(): void
    {
        // Given
        $object1 = new \stdClass();
        $object2 = new \stdClass();
        $resource = \fopen('php://memory', 'r');

        // When
        $identity = UniqueExtractor::identify([$object1, [$object2, $resource]], false);

        // Then
        $this->assertCount(1, $identity->anchors);
        $this->assertSame($resource, $identity->anchors[0]);
    }

    /**
     * @test coercive mode anchors a nested closure alongside a nested resource
     */
    public function testIdentifyCoerciveAnchorsNestedClosure(): void
    {
        // Given
        $object = new \stdClass();
        $closure = static fn (int $x): int => $x + 1;
        $resource = \fopen('php://memory', 'r');

        // When
        $identity = UniqueExtractor::identify([$object, [$closure, $resource]], false);

        // Then
        $this->assertCount(2, $identity->anchors);
        $this->assertSame($closure, $identity->anchors[0]);
        $this->assertSame($resource, $identity->anchors[1]);
    }

    /**
     * @test anchors are collected through the whole depth limit
     */
    public function testIdentifyAnchorsThroughTheDepthLimit(): void
    {
        // Given
        $object = new \stdClass();
        $value = $object;
        for ($i = 0; $i < UniqueExtractor::MAX_DEPTH; $i++) {
            $value = [$value];
        }

        // When
        $identity = UniqueExtractor::identify($value, true);

        // Then
        $this->assertCount(1, $identity->anchors);
        $this->assertSame($object, $identity->anchors[0]);
    }

    /**
     * @test an array of scalars anchors nothing
     * @dataProvider dataProviderForStrictFlag
     * @param bool $strict
     */
    public function testIdentifyAnchorsNothingForArrayOfScalars(bool $strict): void
    {
        // When
        $identity = UniqueExtractor::identify([1, 'a', null, [2.5, false], []], $strict);

        // Then
        $this->assertSame([], $identity->anchors);
    }

    /**
     * @return list<array{mixed}>
     */
    public static function dataProviderForScalars(): array
    {
        return [
            [0],
            [-1],
            [\PHP_INT_MAX],
            [0.0],
            [-0.0],
            [0.5],
            [\INF],
            [\NAN],
            ['abc'],
            ['1'],
            [''],
            [true],
            [false],
            [null],
        ];
    }

    /**
     * @test identify() returns the same key as getString()
     * @dataProvider dataProviderForIdentifyKeyMatchesGetString
     * @param mixed $value
     * @param bool $strict
     */
    public function testIdentifyKeyMatchesGetString($value, bool $strict): void
    {
        // When
        $identity = UniqueExtractor::identify($value, $strict);

        // Then
        $this->assertSame(UniqueExtractor::getString($value, $strict), $identity->key);
    }

    /**
     * @return list<array{mixed, bool}>
     */
    public static function dataProviderForIdentifyKeyMatchesGetString(): array
    {
        $object = new \stdClass();
        $object->value = 1;
        $closure = static fn (int $x): int => $x + 1;
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $resource = \fopen('php://memory', 'r');

        return [
            [0, true],
            [1.5, true],
            [\NAN, true],
            ['1', true],
            ['', true],
            [true, true],
            [null, true],
            [[1, 2, 3], true],
            [$object, true],
            [EnumFixture::One, true],
            [$closure, true],
            [$generator, true],
            [$resource, true],
            [0, false],
            [1.5, false],
            [\NAN, false],
            ['1', false],
            ['', false],
            [true, false],
            [null, false],
            [[1, 2, 3], false],
            [$object, false],
            [EnumFixture::One, false],
            [$closure, false],
            [$generator, false],
            [$resource, false],
        ];
    }

    /**
     * @test an identity keeps the object it hashed by instance alive
     */
    public function testIdentifyRetainsTheObjectItHashedByInstance(): void
    {
        // Given
        $object = new \stdClass();
        $weakReference = \WeakReference::create($object);

        // When
        $identity = UniqueExtractor::identify($object, true);
        unset($object);

        // Then
        $this->assertNotNull($weakReference->get());
        $this->assertSame($identity->key, UniqueExtractor::getString($weakReference->get(), true));
    }

    /**
     * @test an identity keeps the resource it hashed alive
     */
    public function testIdentifyRetainsTheResourceItHashed(): void
    {
        // Given
        $resource = \fopen('php://memory', 'r');
        $identity = UniqueExtractor::identify($resource, true);

        // When
        unset($resource);

        // Then
        $this->assertCount(1, $identity->anchors);
        $this->assertTrue(\is_resource($identity->anchors[0]));
    }

    /**
     * @test a key alone does not keep the object it describes alive
     */
    public function testKeyAloneDoesNotRetainTheObjectItDescribes(): void
    {
        // Given
        $object = new \stdClass();
        $weakReference = \WeakReference::create($object);

        // When
        UniqueExtractor::getString($object, true);
        unset($object);

        // Then
        $this->assertNull($weakReference->get());
    }

    /**
     * @test keys alone let PHP recycle a freed object id, retained anchors do not
     */
    public function testRetainedAnchorsKeepRecycledObjectIdsApart(): void
    {
        // Given
        $keys = [];
        foreach (self::freshObjectsDroppingEach(3) as $object) {
            $keys[] = UniqueExtractor::getString($object, true);
            unset($object);
        }

        if (\count(\array_unique($keys)) === 3) {
            $this->markTestSkipped('This PHP build did not recycle the freed object ids');
        }

        // When
        $identities = [];
        foreach (self::freshObjectsDroppingEach(3) as $object) {
            $identities[] = UniqueExtractor::identify($object, true);
            unset($object);
        }

        // Then
        $retainedKeys = [];
        foreach ($identities as $identity) {
            $retainedKeys[] = $identity->key;
        }

        $this->assertCount(3, \array_unique($retainedKeys));

        $laterObjects = [];
        for ($i = 0; $i < 5; $i++) {
            $laterObjects[] = new \stdClass();
        }

        $laterKeys = [];
        foreach ($laterObjects as $laterObject) {
            $laterKeys[] = UniqueExtractor::getString($laterObject, true);
        }

        $this->assertSame([], \array_intersect($retainedKeys, $laterKeys));
    }

    /**
     * Yields fresh objects, dropping each one before the next is created, so that PHP is free to
     * hand the freed spl_object_id to its successor.
     *
     * @param int $count
     *
     * @return \Generator<int, \stdClass>
     */
    private static function freshObjectsDroppingEach(int $count): \Generator
    {
        for ($i = 0; $i < $count; $i++) {
            $object = new \stdClass();
            yield $object;
            unset($object);
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
            \is_array($value) => $this->describeArray($value),
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
     * Human-readable label for an array, keys included: key order is part of array identity.
     *
     * @param array<array-key, mixed> $value
     */
    private function describeArray(array $value): string
    {
        $parts = [];
        foreach ($value as $key => $item) {
            $parts[] = \var_export($key, true) . ' => ' . $this->describe($item);
        }

        return '[' . \implode(', ', $parts) . ']';
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
            // Arrays nesting the same edge values: element-wise hashing has to reproduce the
            // whole contract one level down, so every identity and float edge case appears again
            // inside an array.
            [],
            [[]],
            [\NAN],
            [\NAN],
            [-0.0],
            [0.0],
            [0.1 + 0.2],
            [0.3],
            [0],
            [$object1],
            [$object2],
            [$object1],
            [EnumFixture::One],
            [EnumFixture::Two],
            [$closure],
            [$generator],
            [$openResource1],
            [$openResource2],
            [$closedResource],
            // Key order is part of array identity, and element boundaries must survive content
            // that looks like the encoding's own separators.
            [1 => 'a', 0 => 'b'],
            [0 => 'b', 1 => 'a'],
            ['a', 'b'],
            ['ab'],
            ['a;', 'b'],
            ['a', ';b'],
            // PHP normalizes the numeric string key to an int key, so these two are one array.
            ['1' => 'x'],
            [1 => 'x'],
            [[1], 2],
            [1, [2]],
            [[[1]]],
            [[[2]]],
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
     * @param bool $withFloatStateObjects whether to include the two objects whose only difference
     *        is a float property; see {@see self::testKeysAreIndependentOfIniPrecision()}
     *
     * @return list<list<mixed>>
     */
    private static function coerciveTable(bool $withFloatStateObjects = true): array
    {
        $object1 = new \stdClass();
        $object1->value = 1;
        $object2 = new \stdClass();
        $object2->value = 1;
        $closure1 = static fn (int $x): int => $x + 1;
        $closure2 = static fn (int $x): int => $x + 2;
        $generator = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $openResource1 = \fopen('php://memory', 'r');
        $openResource2 = \fopen('php://memory', 'r');
        $closedResource = \fopen('php://memory', 'r');
        \fclose($closedResource);
        $floatStateObject1 = new \stdClass();
        $floatStateObject1->float = 0.1 + 0.2;
        $floatStateObject2 = new \stdClass();
        $floatStateObject2->float = 0.3;

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
            [EnumFixture::One],
            [EnumFixture::Two],
            [$generator],
            // Arrays recurse under the coercive rules, so numeric equivalence applies to elements
            // while key order and key identity stay strict.
            [[1], ['1'], [1.0], [true]],
            [[0], [null], [''], [false]],
            [[1, 2]],
            [[2, 1]],
            [['a' => 1], ['a' => '1']],
            [[$object1], [$object2]],
            [[$closure1]],
            [[$openResource1]],
            [[$closedResource], [$closedResource]],
        ];

        if ($withFloatStateObjects) {
            $table[] = [$floatStateObject1];
            $table[] = [$floatStateObject2];
        }

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
     * One reproducible random value: a number, a numeric string, or a shallow array of those.
     *
     * @return int|float|string|array<int, int|float|string>
     */
    private function randomNumericValue()
    {
        if (\mt_rand(0, 3) === 0) {
            return [$this->randomNumericScalar(), $this->randomNumericScalar()];
        }

        return $this->randomNumericScalar();
    }

    /**
     * One reproducible random number or numeric string, drawn from the seeded mt_rand stream.
     *
     * @return int|float|string
     */
    private function randomNumericScalar()
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
