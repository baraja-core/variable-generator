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
	/**
	 * Returns the currently most recent ID or entity number for which we will generate a new identifier.
	 * Always retrieve real data for generation (for example, by calling an SQL query) and do not use caching.
	 * Always try to optimize the data retrieval processing as much as possible,
	 * as this method cannot be called in parallel and is disk locked during processing
	 * preventing other processes from generating the new ID.
	 */
	public function getCurrent(): ?string;
}
