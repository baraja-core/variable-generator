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

How to use
----------

At the beginning, create an instance of the Generator or get it from the DIC. If you are using Doctrine entities, there is an autoconfiguration that will automatically find your entity with an order (must meet the `OrderEntity` interface) and you can start generating numbers.

Example:

```php
$generator = new VariableGenerator(
	variableLoader, // last used variable loader, default is DefaultOrderVariableLoader
	strategy, // generator strategy, default is YearPrefixIncrementStrategy
	entityManager, // if you want use default variable loader by Doctrine entity
);
```

The generator is easy to use.

Retrieve the next free number (using it without an argument automatically retrieves the last used number based on the variableLoader service).

```php
echo $generator->generate();
```

Getting the next available number based on the user's choice:

```php
echo $generator->generate(21010034); // next will be 21010035
```

Retrieving the last generated number:

```php
echo $generator->getCurrent();
```

You can always choose your own strategy for generating numbers:

```php
$generator->setStrategy();
```

Protection duplicate number generation
--------------------------------------

This tool automatically protects you from generating a duplicate number. To protect you, an automatic lock (see the `baraja-core/lock` library for more information) is used, which allows only one number to be generated at a time, while competing processes in other threads are suspended in the meantime.

📄 License
-----------

`baraja-core/variable-generator` is licensed under the MIT license. See the [LICENSE](https://github.com/baraja-core/variable-generator/blob/master/LICENSE) file for more details.
