<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator;


interface VariableLoader
{
	public function getCurrent(): ?string;
}
