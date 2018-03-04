<?php

declare(strict_types = 1);

namespace TomasKarlik\Select2Input\DI;

use Nette\DI\CompilerExtension;
use Nette\Forms\Container;
use Nette\PhpGenerator\ClassType;
use Nette\Utils\ObjectMixin;
use TomasKarlik\Select2Input\ISelect2DataSource;
use TomasKarlik\Select2Input\Select2Input;


final class Select2InputExtension extends CompilerExtension
{

	public function afterCompile(ClassType $class): void
	{
		$initializeMethod = $class->getMethod('initialize');
		$initializeMethod->addBody(__CLASS__ . '::registerControl();');
	}


	public static function registerControl(): void
	{
		ObjectMixin::setExtensionMethod(Container::class, 'addSelect2', function (
			Container $container,
			string $name,
			ISelect2DataSource $dataSource,
			?string $label = NULL
		) {
			return $container[$name] = new Select2Input($dataSource, $label);
		});
	}

}
