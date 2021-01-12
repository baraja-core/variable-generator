<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator;


interface VariableGeneratorAccessor
{
	public function get(): VariableGenerator;
}
