<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator\Strategy;


final class SimpleIncrementStrategy implements IVariableGeneratorStrategy
{
	public function generate(string $last): string
	{
		return \strlen($last) > 2
			? str_pad((string) (((int) $last) + 1), 8, '0', STR_PAD_LEFT)
			: $this->getFirst();
	}


	public function getFirst(): string
	{
		return date('y') . '000001';
	}
}
