<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator\Order;


interface OrderEntity
{
	public function getId();

	public function getNumber(): string;
}
