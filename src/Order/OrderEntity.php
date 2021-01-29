<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator\Order;


interface OrderEntity
{
	/** @return string|int|null */
	public function getId();

	public function getNumber(): string;
}
