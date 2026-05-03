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
