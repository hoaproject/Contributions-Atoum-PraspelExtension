# atoum/praspel-extension

This extension introduces
[Praspel](https://central.hoa-project.net/Resource/Library/Praspel) inside atoum.

[atoum](http://atoum.org/) is a **simple**, **modern** and **intuitive** unit
testing framework for PHP!

[Hoa](https://hoa-project.net/) is a **modular**, **extensible** and
**structured** set of PHP libraries.  Moreover, Hoa aims at being a bridge
between industrial and research worlds.

## Installation

With [Composer](http://getcomposer.org/), to include this library into your
dependencies, you need to require
[`atoum/praspel-extension`](https://packagist.org/packages/atoum/praspel-extension):

```
composer require --dev atoum/praspel-extension
```

Please, read the website to [get more informations about how to
install](https://hoa-project.net/Source.html).

And to activate the extension, add this line on your `.atoum.php` configuration
file:

```php
$runner->addExtension(new \Atoum\PraspelExtension\Manifest());
```

## Quick usage

This extension brings two aspects into atoum: automatic test data generation
(from [`Hoa\Realdom`](https://central.hoa-project.net/Resource/Library/Realdom))
and automatic test suite generation (from
[`Hoa\Praspel`](https://central.hoa-project.net/Resource/Library/Praspel), which
relies on `Hoa\Realdom`).

### Automatic test data generation

[more explications needed]
We will use three asserters to generate data and one to validate data:

  1. `realdom` to create a realistic domains disjunction,
  2. `sample` to generate one data from a realistic domains disjunction,
  3. `sampleMany` to generate several data,
  4. `predicate` to validate a data against a realistic domains disjunction.

As an example, we are going to generate an integer defined by: [7; 13] ∪ [42;
153]:

```php
$this->sample($this->realdom->boundinteger(7, 13)->or->boundinteger(42, 153))
```

We can obviously use the classical asserters from atoum:

```php
foreach ($this->sampleMany($this->realdom->boundinteger(-5, 5), 1024) as $i) {
    $this->integer($i)->isGreaterThan(0);
}
```

(this example is a little dummy ;-)).

We can generate more sophisticated data (please, see the standard realistic
domain library in
[`Hoa\Realdom`](https://central.hoa-project.net/Resource/Library/Realdom)), such
as strings based on regular expressions (and also grammars):

```php
$data = $this->realdom->regex('/[\w\-_]+(\.[\w\-\_]+)*@\w\.(net|org)/');
$this->string($this->sample($data))
     ->contains(…)->…;
```

Or even dates:

```php
$data = $this->realdom->date(
    'd/m H:i',
    $this->realdom->boundinteger(
        $this->realdom->timestamp('yesterday'),
        $this->realdom->timestamp('next Monday')
    )
);

foreach ($this->sampleMany($data, 10) as $date) {
    var_dump($date);
}
```

### Automatic test suite generation

We will use the `Bin/praspel` binary script.
[TODO]

## Documentation of Hoa

Different documentations can be found on the website:
[https://hoa-project.net/](https://hoa-project.net/).

## Links

* [atoum](http://atoum.org),
* [atoum's documentation](http://docs.atoum.org),
* [Hoa](http://hoa-project.net).

## License

Hoa, along with this extension, is under the New BSD License (BSD-3-Clause).
Please, see [`LICENSE`](http://hoa-project.net/LICENSE).

![atoum](http://atoum.org/images/logo/atoum.png)
+
![Hoa](https://static.hoa-project.net/Image/Hoa_small.png)
