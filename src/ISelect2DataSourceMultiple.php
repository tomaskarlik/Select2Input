<?php

declare(strict_types = 1);

namespace TomasKarlik\Select2Input;


interface ISelect2DataSourceMultiple extends ISelect2DataSearch
{

	/**
	 * @param array<mixed> $keys
	 * @return array<Select2ResultEntity>
	 */
	function findByKeys(array $keys): array;

}
