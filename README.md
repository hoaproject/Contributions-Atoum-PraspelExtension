![atoum](http://downloads.atoum.org/images/logo.png)
+
![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

[atoum](http://atoum.org/) is a **simple**, **modern** and **intuitive** unit
testing framework for PHP!

[Hoa](http://hoa-project.net/) is a **modular**, **extensible** and
**structured** set of PHP libraries.  Moreover, Hoa aims at being a bridge
between industrial and research worlds.

# Atoum\PraspelExtension

This extension introduces [Praspel](http://github.com/hoaproject/Praspel) inside
atoum.

## Install

All you need is [Composer](https://getcomposer.org):

```sh
$ composer require atoum/praspelextension 0.1
$ composer install
```

And to activate the extension, add this line on your `.atoum.php` configuration
file:

```php
$runner->addExtension(new \Atoum\PraspelExtension\Manifest());
```

## Quick usage

We are going to generate a data that matches a regular expression in order to
test it:

```php
<?php

namespace Test\Units;

class stdClass extends \atoum\test {

    public function testPraspel ( ) {

        $this->variable('i')->in = realdom()->regex('/foo[a-f]+/');

        $this->string($this->variable('i')->sample())
             ->contains('foo');
    }
}
```

## Documentation of Hoa

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa, along with this extension, is under the New BSD License (BSD-3-Clause).
Please, see [`LICENSE`](http://hoa-project.net/LICENSE).
