<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator\Strategy;


final class SimpleIncrementStrategy implements FormatStrategy
{
	public function __construct(
		private int $length = 8,
	) {
		if ($length < 4) {
			throw new \InvalidArgumentException('Minimal length is 4, but "' . $length . '" given.');
		}
	}


	public function generate(string $last): string
	{
		return strlen($last) > 2
			? str_pad((string) (((int) $last) + 1), $this->length, '0', STR_PAD_LEFT)
			: $this->getFirst();
	}


	public function getFirst(): string
	{
		return date('y') . str_repeat('0', $this->length - 3) . '1';
	}
}
