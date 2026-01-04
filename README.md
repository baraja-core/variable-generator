# Variable Generator

![Integrity check](https://github.com/baraja-core/variable-generator/workflows/Integrity%20check/badge.svg)

A smart PHP library for generating unique variable symbols, order numbers, and sequential identifiers in e-commerce applications. It handles complex problems like format specifications, transaction safety, duplicate prevention, and overflow management through pluggable strategies.

## :bulb: Key Principles

- **Automatic duplicate protection** - Uses locking mechanism to prevent concurrent generation of the same number
- **Pluggable strategies** - Choose from built-in strategies or implement your own formatting logic
- **Doctrine integration** - Automatic entity discovery for seamless database integration
- **Transaction safety** - Built-in lock management ensures data integrity in high-concurrency environments
- **Year-aware numbering** - Default strategy automatically resets sequences on year change
- **Zero configuration** - Works out of the box with sensible defaults

## :building_construction: Architecture Overview

The library is built around a modular architecture with clear separation of concerns:

```
┌─────────────────────────────────────────────────────────────────┐
│                      VariableGenerator                          │
│                    (Main Entry Point)                           │
├─────────────────────────────────────────────────────────────────┤
│                              │                                  │
│         ┌────────────────────┼────────────────────┐             │
│         │                    │                    │             │
│         ▼                    ▼                    ▼             │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐       │
│  │VariableLoader│    │FormatStrategy│    │     Lock     │       │
│  │  (Interface) │    │  (Interface) │    │  (External)  │       │
│  └──────┬───────┘    └──────┬───────┘    └──────────────┘       │
│         │                   │                                   │
│         ▼                   ▼                                   │
│  ┌──────────────┐    ┌─────────────────────────────────┐        │
│  │DefaultOrder  │    │ ┌─────────────────────────────┐ │        │
│  │VariableLoader│    │ │YearPrefixIncrementStrategy  │ │        │
│  │  (Doctrine)  │    │ ├─────────────────────────────┤ │        │
│  └──────────────┘    │ │SimpleIncrementStrategy      │ │        │
│                      │ ├─────────────────────────────┤ │        │
│                      │ │Custom Strategy (Your Own)   │ │        │
│                      │ └─────────────────────────────┘ │        │
│                      └─────────────────────────────────┘        │
└─────────────────────────────────────────────────────────────────┘
```

### :jigsaw: Main Components

| Component | Description |
|-----------|-------------|
| `VariableGenerator` | Main service class that orchestrates number generation with locking and strategy execution |
| `VariableLoader` | Interface for retrieving the last used number from your data source |
| `FormatStrategy` | Interface defining how the next number should be calculated |
| `OrderEntity` | Interface for Doctrine entities that enables automatic loader discovery |
| `VariableGeneratorExtension` | Nette DI extension for framework integration |
| `VariableGeneratorAccessor` | Accessor interface for lazy service injection |

### :gear: Built-in Strategies

#### YearPrefixIncrementStrategy (Default)

Generates numbers in format `YYXXXXXX` where `YY` is the current year and `XXXXXX` is an incrementing sequence:

```
Year: 2024
Format: 24000001, 24000002, 24000003, ...

Year: 2025 (automatic reset on year change)
Format: 25000001, 25000002, 25000003, ...
```

Features:
- Automatic year prefix based on current date
- Automatic sequence reset on year change
- Configurable total length (default: 8 characters)
- Overflow protection (expands length if needed)

#### SimpleIncrementStrategy

A straightforward incrementing strategy that adds one to the previous number:

```
Input:  21000034
Output: 21000035
```

Features:
- Maintains consistent number length with zero-padding
- Configurable length (minimum: 4 characters)
- Falls back to year-prefixed first number if no previous exists

## :rocket: Basic Usage

### Creating the Generator

```php
use Baraja\VariableGenerator\VariableGenerator;
use Baraja\VariableGenerator\Strategy\YearPrefixIncrementStrategy;

// With Doctrine EntityManager (automatic entity discovery)
$generator = new VariableGenerator(
    variableLoader: null,  // Auto-discovered from Doctrine
    strategy: null,        // Uses YearPrefixIncrementStrategy by default
    em: $entityManager,
);

// With custom variable loader
$generator = new VariableGenerator(
    variableLoader: new MyCustomVariableLoader(),
    strategy: new YearPrefixIncrementStrategy(length: 6),
);
```

### Generating Numbers

```php
// Generate next number (automatically retrieves last used number)
$newOrderNumber = $generator->generate();
// Result: 24000001 (if first order in 2024)

// Generate next number based on specific previous value
$newNumber = $generator->generate('24000034');
// Result: 24000035

// Get current (last used) number without generating new one
$current = $generator->getCurrent();
// Result: 24000034
```

### Using Custom Strategies

```php
use Baraja\VariableGenerator\Strategy\SimpleIncrementStrategy;

// Switch to simple increment strategy
$generator->setStrategy(new SimpleIncrementStrategy(length: 10));

// Or use custom strategy at initialization
$generator = new VariableGenerator(
    variableLoader: $loader,
    strategy: new SimpleIncrementStrategy(length: 8),
);
```

## :closed_lock_with_key: Duplicate Prevention

This library automatically protects against generating duplicate numbers in high-concurrency environments. The protection mechanism works as follows:

1. **Wait for existing transactions** - Before generating, the system waits if another process is currently generating
2. **Lock acquisition** - A 15-second transaction lock is acquired for the generation process
3. **Number generation** - The new number is calculated using the selected strategy
4. **Short protection window** - A 1-second lock remains to allow saving the new entity to database

```php
// The generate() method handles all locking automatically
$number = $generator->generate();
// IMPORTANT: Save your entity immediately after generation!

// Custom transaction name for different entity types
$orderNumber = $generator->generate(transactionName: 'order-generator');
$invoiceNumber = $generator->generate(transactionName: 'invoice-generator');
```

> **Warning:** You must save the generated number to your database within 1 second. After that, the lock is released and another process may generate the same number.

## :wrench: Custom Implementations

### Custom Variable Loader

Implement the `VariableLoader` interface to retrieve the last number from your data source:

```php
use Baraja\VariableGenerator\VariableLoader;

final class MyCustomVariableLoader implements VariableLoader
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function getCurrent(): ?string
    {
        $stmt = $this->pdo->query(
            'SELECT order_number FROM orders ORDER BY id DESC LIMIT 1'
        );
        $result = $stmt->fetchColumn();

        return $result !== false ? (string) $result : null;
    }
}
```

### Custom Format Strategy

Implement the `FormatStrategy` interface for custom number formatting:

```php
use Baraja\VariableGenerator\Strategy\FormatStrategy;

final class MonthlyResetStrategy implements FormatStrategy
{
    public function generate(string $last): string
    {
        $prefix = date('ym'); // e.g., "2401" for January 2024

        if (str_starts_with($last, $prefix)) {
            $sequence = (int) substr($last, 4);
            return $prefix . str_pad((string) ($sequence + 1), 4, '0', STR_PAD_LEFT);
        }

        return $this->getFirst();
    }

    public function getFirst(): string
    {
        return date('ym') . '0001';
    }
}
```

### Doctrine Entity Integration

Implement the `OrderEntity` interface on your Doctrine entity for automatic discovery:

```php
use Baraja\VariableGenerator\Order\OrderEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Order implements OrderEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', unique: true)]
    private string $number;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $insertedDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getInsertedDate(): \DateTime
    {
        return $this->insertedDate;
    }
}
```

> **Note:** If your entity has `getInsertedDate()` method, the `DefaultOrderVariableLoader` will automatically filter orders from the last year when searching for the latest number.

## :zap: Nette Framework Integration

Register the extension in your configuration:

```neon
extensions:
    variableGenerator: Baraja\VariableGenerator\VariableGeneratorExtension
```

Then inject the generator into your services:

```php
final class OrderFacade
{
    public function __construct(
        private VariableGenerator $generator,
    ) {
    }

    public function createOrder(array $data): Order
    {
        $order = new Order();
        $order->setNumber((string) $this->generator->generate());
        // ... save order

        return $order;
    }
}
```

For lazy loading, use the accessor:

```php
public function __construct(
    private VariableGeneratorAccessor $generatorAccessor,
) {
}

public function process(): void
{
    $generator = $this->generatorAccessor->get();
    // ...
}
```

## :warning: Important Considerations

1. **Save immediately** - Always save the generated number to your database immediately after calling `generate()`. The lock protection lasts only 1 second.

2. **Single entity per interface** - If using automatic Doctrine discovery, only one entity can implement `OrderEntity`. For multiple entities, implement custom `VariableLoader` services.

3. **No caching** - The `VariableLoader::getCurrent()` method should always fetch real data from the database. Never use cached values.

4. **Transaction safety** - The generator uses the `baraja-core/lock` library for thread safety. Make sure this dependency is properly installed.

## :package: Installation

It's best to use [Composer](https://getcomposer.org) for installation, and you can also find the package on
[Packagist](https://packagist.org/packages/baraja-core/variable-generator) and
[GitHub](https://github.com/baraja-core/variable-generator).

To install, simply use the command:

```shell
$ composer require baraja-core/variable-generator
```

You can use the package manually by creating an instance of the internal classes, or register a DIC extension to link the services directly to the Nette Framework.

### Requirements

- PHP 8.0 or higher
- `baraja-core/lock` package (installed automatically)
- Doctrine ORM (optional, for automatic entity discovery)
- Nette DI (optional, for framework integration)

## :bust_in_silhouette: Author

**Jan Barášek** - [https://baraja.cz](https://baraja.cz)

## :page_facing_up: License

`baraja-core/variable-generator` is licensed under the MIT license. See the [LICENSE](https://github.com/baraja-core/variable-generator/blob/master/LICENSE) file for more details.
