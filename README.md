Variable generator
======================

![Integrity check](https://github.com/baraja-core/variable-generator/workflows/Integrity%20check/badge.svg)

Generate new variable symbol by last variable and selected strategy.

Idea
----

A series of smart tools for generating variable symbols and order numbers in your e-shop.

Generating order numbers or other number series hides a number of complex problems. For example, adhering to the specified format according to the specification, handling transaction entries (to avoid duplication) and handling the case when the generated value overflows.

This package contains a set of algorithms and ready-made strategies to elegantly solve these problems. If any of the algorithms do not suit you, you can implement your own just by satisfying the defined interface.

📦 Installation
---------------

It's best to use [Composer](https://getcomposer.org) for installation, and you can also find the package on
[Packagist](https://packagist.org/packages/baraja-core/admin-bar) and
[GitHub](https://github.com/baraja-core/variable-generator).

To install, simply use the command:

```
$ composer require baraja-core/variable-generator
```

You can use the package manually by creating an instance of the internal classes, or register a DIC extension to link the services directly to the Nette Framework.

📄 License
-----------

`baraja-core/variable-generator` is licensed under the MIT license. See the [LICENSE](https://github.com/baraja-core/variable-generator/blob/master/LICENSE) file for more details.
