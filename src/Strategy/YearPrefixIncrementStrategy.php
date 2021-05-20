<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator\Strategy;


final class YearPrefixIncrementStrategy implements FormatStrategy
{
	public function __construct(
		private ?int $length = null,
		private int $preferredLength = 8,
	) {
	}


	public function generate(string $last): string
	{
		if (preg_match('/^(?<year>\d{2})(?<count>\d+)$/', $last, $variableParser)) {
			$year = date('y');
			if ($year === $variableParser['year']) {
				$length = $this->length ?? strlen($variableParser['count']);
				$new = $variableParser['year']
					. str_pad(
						string: (string) ($variableParser['count'] + 1),
						length: $length,
						pad_string: '0',
						pad_type: STR_PAD_LEFT,
					);
			} else {
				$new = $year . str_repeat('0', strlen($variableParser['count']) - 1) . '1';
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
