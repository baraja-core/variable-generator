<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator\Strategy;


/**
 * The general formatting strategy that generates this format:
 *
 * YYXXXXXX
 * ^ ^
 * | \_ Generated number
 * \___ Year prefix
 *
 * Sample generated number can be 21000001 for first number in 2021.
 *
 * This strategy tries to maintain a defined number of characters.
 * The number of characters may overflow in case of a large number of orders.
 * The minimum length of the generated number is 3 characters.
 *
 * If there is a natural year change (during New Year's Eve),
 * the number series will automatically reset and the new number will be the first in the sequence of the new year.
 */
final class YearPrefixIncrementStrategy implements FormatStrategy
{
	public function __construct(
		private ?int $length = null,
		private int $preferredLength = 8,
	) {
	}


	public function generate(string $last): string
	{
		$year = date('y');
		if (preg_match('/^' . $year . '(?<count>\d+)$/', $last, $parser)) {
			$length = $this->length ?? strlen($parser['count']);
			$new = $year
				. str_pad(
					string: (string) ($parser['count'] + 1),
					length: $length,
					pad_string: '0',
					pad_type: STR_PAD_LEFT,
				);
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
