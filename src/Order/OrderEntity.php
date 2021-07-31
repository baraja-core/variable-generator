<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator\Order;


/**
 * A generic definition of an entity (most often an entity representing an order) in your database schema.
 * Each variant entity has a unique identifier (often a row ID) and a unique human-friendly generated number.
 * This data serves as the basis for generating a new number.
 */
interface OrderEntity
{
	public function getId(): string|int|null;

	public function getNumber(): string;
}
