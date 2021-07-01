# IterTools PHP
### Iteration Tools Library for PHP


Quick Refernce
-----------

#### Multi Iteration
| Iterator      | Description | Code Snippet |
| ----------- | ----------- | ----------- |
| [`Multi::zip`](#Zip) | Iterate multiple iterable collections simultaneously | `Multi::zip($collection1, $collection2)` |

Setup
-----

 Add the library to your `composer.json` file in your project:

```javascript
{
  "require": {
      "markrogoyski/itertools-php": "0.*"
  }
}
```

Use [composer](http://getcomposer.org) to install the library:

```bash
$ php composer.phar install
```

Composer will install IterTools inside your vendor folder. Then you can add the following to your
.php files to use the library with Autoloading.

```php
require_once __DIR__ . '/vendor/autoload.php';
```

Alternatively, use composer on the command line to require and install IterTools:

```
$ php composer.phar require markrogoyski/itertools-php:0.*
```

#### Minimum Requirements
 * PHP 7.4



Usage
-----

### Zip
Iterate multiple iterable collections simultaneously.
```php
use IterTools\Multi;

$languages = ['PHP', 'Python', 'Java', 'Go'];
$mascots   = ['elephant', 'snake', 'bean', 'gopher'];

foreach (Multi::zip($languages, $mascots) as [$language, $mascot]) {
    print("The {$mascot} is the mascot of the {$language} language.");
}
// The elephant is the mascot of the PHP language.
// ...
```

Zip works with multiple iterable inputs--not limited to just two.
```php
$names         = ['Ryu', 'Ken', 'Chun Li', 'Guile'];
$country       = ['Japan', 'USA', 'China', 'USA'];
$signatureMove = ['hadouken', 'shoryuken', 'spinning bird kick', 'sonic boom'];

foreach (Multi::zip($names, $country, $signatureMove) as [$name, $country, $signatureMove]) {
    $streetFighter = new StreetFighter($name, $country, $signatureMove);
}
```