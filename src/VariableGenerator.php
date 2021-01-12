<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator;


use Baraja\VariableGenerator\Strategy\IVariableGeneratorStrategy;
use Baraja\VariableGenerator\Strategy\YearPrefixIncrementStrategy;

final class VariableGenerator
{
	private CurrentVariableLoader $variableLoader;

	private IVariableGeneratorStrategy $strategy;


	public function __construct(CurrentVariableLoader $variableLoader, ?IVariableGeneratorStrategy $strategy = null)
	{
		$this->variableLoader = $variableLoader;
		$this->strategy = $strategy ?? new YearPrefixIncrementStrategy;
	}


	/**
	 * Generate new variable symbol by last variable.
	 * In case of invalid last symbol or init, use first valid symbol by specific strategy.
	 */
	public function generate(?string $last = null): int
	{
		$new = (($last = $last ?? $this->variableLoader->getCurrent()) === null)
			? $this->strategy->getFirst()
			: $this->strategy->generate((string) preg_replace('/\D+/', '', (string) $last));

		return (int) $new;
	}


	public function getCurrent(bool $useConstant = true): int
	{
		return (int) (($useConstant === true ? $this->variableLoader->getCurrent() : null) ?? $this->strategy->getFirst());
	}


	public function setStrategy(IVariableGeneratorStrategy $strategy): void
	{
		$this->strategy = $strategy;
	}
}
