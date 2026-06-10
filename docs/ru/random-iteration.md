# Итерирование случайных значений

[Вернуться к главной странице](README.md)

Инструменты для генерации последовательностей случайных значений.

---

### Choice
Генерирует случайные выборы вариантов из списка.

```Random::choice(array $items, int $repetitions)```

```php
use IterTools\Random;

$cards       = ['Ace', 'King', 'Queen', 'Jack', 'Joker'];
$repetitions = 10;

foreach (Random::choice($cards, $repetitions) as $card) {
    print($card);
}
// 'King', 'Jack', 'King', 'Ace', ... [random]
```

### CoinFlip
Генерирует случайные броски монеты (0 или 1).

```Random::coinFlip(int $repetitions)```

```php
use IterTools\Random;

$repetitions = 10;

foreach (Random::coinFlip($repetitions) as $coinFlip) {
    print($coinFlip);
}
// 1, 0, 1, 1, 0, ... [random]
```

### Number
Генерирует случайные целые числа.

```Random::number(int $min, int $max, int $repetitions)```

```php
use IterTools\Random;

$min         = 1;
$max         = 4;
$repetitions = 10;

foreach (Random::number($min, $max, $repetitions) as $number) {
    print($number);
}
// 3, 2, 5, 5, 1, 2, ... [random]
```

### Percentage
Генерирует случайные вещественные числа между 0 и 1.

```Random::percentage(int $repetitions)```

```php
use IterTools\Random;

$repetitions = 10;

foreach (Random::percentage($repetitions) as $percentage) {
    print($percentage);
}
// 0.30205562629132, 0.59648594775233, ... [random]
```

### RockPaperScissors
Случайный выбор "камень-ножницы-бумага".

```Random::rockPaperScissors(int $repetitions)```

```php
use IterTools\Random;

$repetitions = 10;

foreach (Random::rockPaperScissors($repetitions) as $rpsHand) {
    print($rpsHand);
}
// 'paper', 'rock', 'rock', 'scissors', ... [random]
```

### Sample
Возвращает выборку из `$size` элементов из коллекции без повторений.

```Random::sample(iterable $data, int $size, ?\Random\Engine $engine = null)```

* Каждая позиция исходной коллекции используется не более одного раза; одинаковые значения в источнике допустимы.
* Материализует входные данные. Ключи результата — последовательные, начиная с 0.
* Бросает `\InvalidArgumentException`, если `$size` отрицателен.
* Бросает `\LengthException`, если `$size` превышает размер исходной коллекции.

```php
use IterTools\Random;

$population = ['a', 'b', 'c', 'd', 'e'];

foreach (Random::sample($population, 3) as $item) {
    print($item);
}
// например: c, a, e [случайно, без повторов]
```

### Reservoir Sample
Возвращает равномерно случайную выборку до `$size` элементов за один проход (алгоритм R).

```Random::reservoirSample(iterable $data, int $size, ?\Random\Engine $engine = null): array```

* Не материализует все входные данные — в памяти удерживается резервуар не более чем из `$size` элементов, поэтому метод безопасен для очень больших (но конечных) источников. Бесконечный источник недопустим: алгоритм R должен прочитать весь вход.
* Возвращает до `$size` элементов (меньше, если вход короче). Когда `$size >= количества` элементов входа, весь вход возвращается в исходном порядке и **ни одна** случайная выборка не происходит — это намеренно отличается от [`sample`](#sample), который бросает `\LengthException` при слишком большом `$size`.
* Ключи результата — последовательные, начиная с 0.
* Бросает `\InvalidArgumentException`, если `$size` отрицателен.

```php
use IterTools\Random;

$logLines = $hugeLogFileLineGenerator; // миллионы строк, никогда не материализуются полностью

$reservoir = Random::reservoirSample($logLines, 5);
// например: 5 строк, выбранных равномерно случайно за один проход
```
