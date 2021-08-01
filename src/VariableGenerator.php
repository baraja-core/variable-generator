<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator;


use Baraja\Lock\Lock;
use Baraja\VariableGenerator\Order\DefaultOrderVariableLoader;
use Baraja\VariableGenerator\Order\OrderEntity;
use Baraja\VariableGenerator\Strategy\FormatStrategy;
use Baraja\VariableGenerator\Strategy\YearPrefixIncrementStrategy;
use Doctrine\ORM\EntityManagerInterface;

/**
 * A generic service for generating a unique order number, variable symbol or other unique identifier.
 */
final class VariableGenerator
{
	private VariableLoader $variableLoader;

	private FormatStrategy $strategy;


	public function __construct(
		?VariableLoader $variableLoader = null,
		?FormatStrategy $strategy = null,
		?EntityManagerInterface $em = null
	) {
		$this->variableLoader = $this->resolveVariableLoader($variableLoader, $em);
		$this->strategy = $strategy ?? new YearPrefixIncrementStrategy;
	}


	/**
	 * Generate new variable symbol by last variable.
	 * In case of invalid last symbol or init, use first valid symbol by specific strategy.
	 */
	public function generate(?string $last = null, string $transactionName = 'variable-generator'): int
	{
		Lock::wait($transactionName);
		Lock::startTransaction($transactionName);
		$last ??= $this->variableLoader->getCurrent();
		$new = $last === null
			? $this->strategy->getFirst()
			: $this->strategy->generate((string) preg_replace('/\D+/', '', (string) $last));

		return (int) $new;
	}


	/**
	 * Returns the current latest number. For example, the order number.
	 * Warning: Never use this number for create a new entity, in this case use the generate() method.
	 */
	public function getCurrent(bool $findReal = true): int
	{
		$currentValue = $findReal === true
			? $this->variableLoader->getCurrent()
			: null;

		return (int) ($currentValue ?? $this->strategy->getFirst());
	}


	public function setStrategy(FormatStrategy $strategy): void
	{
		$this->strategy = $strategy;
	}


	/**
	 * Automatically finds a unique Doctrine entity that implements the OrderEntity interface.
	 * In most applications, there is only one unique entity for an order,
	 * so this interface can be used as the key for the search.
	 * If your application uses multiple entities for which you need to generate variable symbols,
	 * implement a custom service for VariableLoader.
	 */
	private function resolveVariableLoader(?VariableLoader $loader, ?EntityManagerInterface $em): VariableLoader
	{
		if ($loader !== null) {
			return $loader;
		}
		if ($em === null) {
			throw new \RuntimeException(
				'Service for VariableLoader not found. '
				. 'Please implement your own service that implements "' . VariableLoader::class . '" interface, '
				. 'or implement the "' . OrderEntity::class . '" interface for one of the Doctrine entities.',
			);
		}
		$canonicalEntity = null;
		foreach ($em->getMetadataFactory()->getAllMetadata() as $entity) {
			if ($entity->getReflectionClass()->implementsInterface(OrderEntity::class)) {
				if ($canonicalEntity !== null) {
					throw new \LogicException(
						'OrderEntity search error: Several entities implement the same "' . OrderEntity::class . '" interface.' . "\n"
						. 'Found entities: "' . $entity->getName() . '" and "' . $canonicalEntity . '"' . "\n"
						. 'To solve this issue: Set dependencies so that only one entity implements this general interface. '
						. 'If you need to keep the current definitions, implement your own service for VariableLoader.',
					);
				}
				$canonicalEntity = $entity->getName();
			}
		}
		if ($canonicalEntity === null) {
			throw new \LogicException('There is no Doctrine entity that implements the "' . OrderEntity::class . '" interface.');
		}

		return new DefaultOrderVariableLoader($em, $canonicalEntity);
	}
}
