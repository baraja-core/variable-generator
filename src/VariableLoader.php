<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator;


/**
 * A generic interface that gets the number of the last stored entity of the type.
 * It is most commonly used as a DIC service to retrieve the last order from the database.
 * This service also solves the loading problem about database transactions.
 */
interface VariableLoader
{
	public function getCurrent(): ?string;
}
