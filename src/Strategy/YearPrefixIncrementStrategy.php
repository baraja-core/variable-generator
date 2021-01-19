<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator\Strategy;


final class YearPrefixIncrementStrategy implements FormatStrategy
{
	private ?int $length;

	private int $preferredLength;


	public function __construct(?int $length = null, int $preferredLength = 8)
	{
		$this->length = $length;
		$this->preferredLength = $preferredLength;
	}


	public function generate(string $last): string
	{
		if (preg_match('/^(?<year>\d{2})(?<count>\d+)$/', $last, $variableParser)) {
			if (($year = date('y')) === $variableParser['year']) {
				$length = $this->length ?? \strlen($variableParser['count']);
				$new = $variableParser['year'] . str_pad((string) ($variableParser['count'] + 1), $length, '0', STR_PAD_LEFT);
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
		return date('y') . str_repeat('0', ($this->length ?? $this->preferredLength) - 3) . '1';
	}
}
