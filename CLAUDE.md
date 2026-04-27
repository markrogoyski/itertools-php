# CLAUDE.md

## Project Overview

IterTools PHP is a library of iteration tools (inspired by Python itertools) providing both static functions and a fluent `Stream` API. Requires PHP 8.2+ with `ext-mbstring`.

**Static API classes:** `Single`, `Multi`, `Reduce`, `Summary`, `Set`, `Math`, `Infinite`, `Random`, `Sort`, `Transform`, `File`

**Fluent API:** `Stream::of($iterable)->filter(...)->map(...)->toArray()`

## Development Workflow

We practice **TDD**: write failing tests first, then implement the fix/feature, then verify.

```
make tests    # Run tests (TDD cycle)
make all      # Full suite: lint, tests, style, phpstan, psalm, composer-unused, composer-require-checker
```

## Coding Conventions

- `declare(strict_types=1)` in every file.
- Prefix root namespace PHP built-in functions with `\` (e.g., `\count()`, `\array_map()`, `\is_array()`).
- Use `(bool)` cast for predicate coercion, not strict `=== true`/`=== false` (see `exactlyN`, `isPartitioned`).
- Most iteration methods return `\Generator`, not arrays.

## Writing Tests

Tests live in `tests/` mirroring `src/` structure. Each test class extends `\PHPUnit\Framework\TestCase`.

### BDD Comments Style

Use `// Given`, `// When`, `// Then` comments to structure test methods:

```php
public function testExampleArray(array $data, callable $predicate): void
{
    // Given (setup — omit if no extra setup beyond parameters)

    // When
    $result = Summary::allMatch($data, $predicate);

    // Then
    $this->assertTrue($result);
}
```

### Data Providers and Iterable Types

Tests use `@dataProvider` annotations with static data provider methods. Test each function against multiple iterable types using fixture classes:

- **Array:** native `array`
- **Generator:** `Fixture\GeneratorFixture::getGenerator($array)`
- **Iterator:** `new Fixture\ArrayIteratorFixture($array)`
- **IteratorAggregate:** `new Fixture\IteratorAggregateFixture($array)`

The `DataProvider` trait (`tests/Fixture/DataProvider.php`) provides shared data like `dataProviderForEmptyIterable`.

### Test Naming

- Methods: `testDescriptiveNameArray`, `testDescriptiveNameGenerator`, etc.
- PHPDoc: `@test` annotation with a short description.

## Documentation

English docs in root `README.md` and `docs/*.md` are canonical. Translations live in `docs/{language-code}/` (e.g. `docs/ru/`, `docs/ja/`) and mirror canonical filenames where available. When adding or renaming a canonical English doc, update translations in place if possible; otherwise leave the canonical file as the source of truth and let translations catch up.

## Static Analysis

- **PHPStan:** level max (`tools/phpstan.neon`)
- **Psalm:** error level 1 (`tools/psalm.xml`)
- **PHPCS:** PSR-12 based (`tools/coding_standard.xml`)
