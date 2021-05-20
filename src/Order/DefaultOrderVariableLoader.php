<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator\Order;


use Baraja\VariableGenerator\VariableLoader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class DefaultOrderVariableLoader implements VariableLoader
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private string $entityClassName,
	) {
	}


	public function getCurrent(?\DateTime $findFromDate = null): ?string
	{
		$selector = (new EntityRepository(
			$this->entityManager,
			$this->entityManager->getClassMetadata($this->entityClassName),
		))
			->createQueryBuilder('o')
			->select('o.number')
			->orderBy('o.number', 'DESC')
			->setMaxResults(1);

		if (method_exists($this->entityClassName, 'getInsertedDate')) {
			$selector->andWhere('o.insertedDate > :preferenceInsertedDateFrom')
				->setParameter(
					'preferenceInsertedDateFrom',
					$findFromDate === null
						? (date('Y') - 1) . '-' . date('m-d')
						: $findFromDate->format('Y-m-d'),
				);
		}

		try {
			return (string) $selector->getQuery()->getSingleScalarResult();
		} catch (\Throwable) {
			// Silence is golden.
		}

		return null;
	}
}
