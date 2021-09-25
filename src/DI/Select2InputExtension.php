<?php

declare(strict_types = 1);

namespace TomasKarlik\Select2Input\DI;

use Nette\DI\CompilerExtension;
use Nette\Forms\Container;
use Nette\PhpGenerator\ClassType;
use TomasKarlik\Select2Input\ISelect2DataSource;
use TomasKarlik\Select2Input\ISelect2DataSourceMultiple;
use TomasKarlik\Select2Input\Select2Input;
use TomasKarlik\Select2Input\Select2InputMultiple;


final class Select2InputExtension extends CompilerExtension
{

	public function afterCompile(ClassType $class): void
	{
		$initializeMethod = $class->getMethod('initialize');
		$initializeMethod->addBody(__CLASS__ . '::registerControls();');
	}


	public static function registerControls(): void
	{
		// addSelect2()
		Container::extensionMethod('addSelect2', function (
			Container $container,
			string $name,
			ISelect2DataSource $dataSource,
			?string $label = NULL
		) {
			return $container[$name] = new Select2Input($dataSource, $label);
		});

		// addSelect2Multiple()
		Container::extensionMethod('addSelect2Multiple', function (
			Container $container,
			string $name,
			ISelect2DataSourceMultiple $dataSource,
			?string $label = NULL
		) {
			return $container[$name] = new Select2InputMultiple($dataSource, $label);
		});
	}

}
