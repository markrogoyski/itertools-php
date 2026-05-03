# Бесконечное итерирование

[Вернуться к главной странице](README.md)

Инструменты для создания бесконечных итераторов.

---

### Count
Бесконечно перебирает последовательность целых чисел.

```Infinite::count(int $start = 1, int $step = 1)```

```php
use IterTools\Infinite;

$start = 1;
$step  = 1;

foreach (Infinite::count($start, $step) as $i) {
    print($i);
}
// 1, 2, 3, 4, 5 ...
```

### Cycle
Бесконечно зацикливает перебор коллекции.

```Infinite::cycle(iterable $iterable)```

```php
use IterTools\Infinite;

$hands = ['rock', 'paper', 'scissors'];

foreach (Infinite::cycle($hands) as $hand) {
    RockPaperScissors::playHand($hand);
}
// rock, paper, scissors, rock, paper, scissors, ...
```

### Iterate
Бесконечно итерируется, последовательно применяя функцию к её предыдущему результату.

Сначала генерирует `$initial`, затем `$function($initial)`, затем `$function($function($initial))` и так далее. Всегда используйте вместе с ограничителем, например [`Single::limit`](single-iteration.md#limit), чтобы получить конечное число элементов.

```Infinite::iterate(mixed $initial, callable $function)```

```php
use IterTools\Infinite;
use IterTools\Single;

// Степени двойки: 1, 2, 4, 8, 16, 32, 64, 128
$powersOfTwo = Infinite::iterate(1, fn (int $x) => $x * 2);

foreach (Single::limit($powersOfTwo, 8) as $power) {
    print("$power ");
}
// 1 2 4 8 16 32 64 128
```

```php
use IterTools\Infinite;
use IterTools\Single;

// Последовательность Коллатца, начиная с 27, первые 50 членов.
$collatz = fn (int $n) => $n % 2 === 0 ? \intdiv($n, 2) : 3 * $n + 1;

foreach (Single::limit(Infinite::iterate(27, $collatz), 50) as $term) {
    print("$term ");
}
// 27 82 41 124 62 31 94 47 142 71 214 107 322 161 484 ...
```

### Repeat (Infinite)
Бесконечно повторяет данное значение.

```Infinite::repeat(mixed $item)```

```php
use IterTools\Infinite;

$dialogue = 'Are we there yet?';

foreach (Infinite::repeat($dialogue) as $repeated) {
    print($repeated);
}
// 'Are we there yet?', 'Are we there yet?', 'Are we there yet?', ...
```

### Generate
Бесконечно отдаёт значения, возвращаемые поставщиком (функцией без аргументов).

```Infinite::generate(callable $supplier)```

* На каждой итерации вызывает поставщик и отдаёт его возвращаемое значение.
* Состояние, захваченное замыканием поставщика, сохраняется между вызовами.

```php
use IterTools\Infinite;
use IterTools\Single;

$counter = 0;
$next    = function () use (&$counter) {
    return ++$counter;
};

foreach (Single::limit(Infinite::generate($next), 5) as $n) {
    print($n);
}
// 1, 2, 3, 4, 5
```
