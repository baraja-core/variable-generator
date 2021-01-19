<?php

declare(strict_types=1);

namespace Baraja\VariableGenerator;


use Nette\DI\CompilerExtension;

final class VariableGeneratorExtension extends CompilerExtension
{
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('variableGenerator'))
			->setFactory(VariableGenerator::class);
	}
}
