<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator;


interface CurrentVariableLoader
{
	public function getCurrent(): ?string;
}
