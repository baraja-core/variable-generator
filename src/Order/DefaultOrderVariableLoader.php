<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator\Order;


use Baraja\VariableGenerator\VariableLoader;
use Doctrine\ORM\EntityManagerInterface;

final class DefaultOrderVariableLoader implements VariableLoader
{
	private EntityManagerInterface $entityManager;

	private string $entityClassName;


	public function __construct(EntityManagerInterface $entityManager, string $entityClassName)
	{
		$this->entityManager = $entityManager;
		$this->entityClassName = $entityClassName;
	}


	public function getCurrent(): ?string
	{
		try {
			return (string) $this->entityManager->getRepository($this->entityClassName)
				->createQueryBuilder('o')
				->select('o.number')
				->orderBy('o.number', 'DESC')
				->setMaxResults(1)
				->getQuery()
				->getSingleScalarResult();
		} catch (\Throwable $e) {
		}

		return null;
	}
}