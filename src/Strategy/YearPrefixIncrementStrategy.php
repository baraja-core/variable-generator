<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator\Strategy;


final class YearPrefixIncrementStrategy implements IVariableGeneratorStrategy
{
	private int $length;


	public function __construct(int $length = 8)
	{
		$this->length = $length;
	}


	public function generate(string $last): string
	{
		if (preg_match('/^(?<year>\d{2})(?<count>\d+)$/', $last, $variableParser)) {
			if (($year = date('y')) === $variableParser['year']) {
				$new = $variableParser['year'] . str_pad((string) ($variableParser['count'] + 1), $this->length - 2, '0', STR_PAD_LEFT);
			} else {
				$new = $year . str_repeat('0', \strlen($variableParser['count']) - 1) . '1';
			}
		} else {
			$new = $this->getFirst();
		}

		return $new;
	}


	public function getFirst(): string
	{
		return date('y') . str_repeat('0', $this->length - 3) . '1';
	}
}
