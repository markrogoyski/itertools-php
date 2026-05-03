# Random Iteration

[Back to main README](../README.md)

Tools for generating random iteration sequences.

---

### Choice
Generate random selections from an array of values.

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
Generate random coin flips (0 or 1).

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
Generate random numbers (integers).

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
Generate a random percentage between 0 and 1.

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
Generate random rock-paper-scissors hands.

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
Sample `$size` elements from the population without replacement.

```Random::sample(iterable $data, int $size, ?\Random\Engine $engine = null)```

* Every input position is used at most once; duplicate values in the population are valid.
* Materializes the input. Output keys are sequential 0-indexed.
* Throws `\InvalidArgumentException` if `$size` is negative.
* Throws `\LengthException` if `$size` exceeds the population size.

```php
use IterTools\Random;

$population = ['a', 'b', 'c', 'd', 'e'];

foreach (Random::sample($population, 3) as $item) {
    print($item);
}
// e.g.: c, a, e [random, no repeats]
```
