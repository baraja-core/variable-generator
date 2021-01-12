<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator\Strategy;


interface IVariableGeneratorStrategy
{
	public function generate(string $last): string;

	/**
	 * Generate first variable, if last does not exist.
	 */
	public function getFirst(): string;
}
