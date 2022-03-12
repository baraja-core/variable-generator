<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator\Strategy;


/**
 * Generates the next number in the sequence based on the implemented strategy.
 * The next number is always determined by a deterministic algorithm based on the defined input.
 * If you are servicing a large number of orders in parallel,
 * you can request a number of numbers in advance and cache the results.
 *
 * Each strategy guarantees that a valid number in the defined range
 * (or the first number if none existed before) will always be returned.
 */
interface FormatStrategy
{
	/**
	 * Returns the next number in sequence based on the input.
	 * The generation is always done by a deterministic algorithm.
	 */
	public function generate(string $last): string;

	/**
	 * Generate the first variable, if last does not exist.
	 */
	public function getFirst(): string;
}
