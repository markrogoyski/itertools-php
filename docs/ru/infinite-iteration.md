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
