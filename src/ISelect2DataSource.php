<?php

declare(strict_types = 1);

namespace TomasKarlik\Select2Input;


interface ISelect2DataSource extends ISelect2DataSearch
{

	/**
	 * @param mixed $key
	 * @return Select2ResultEntity|NULL
	 */
	function findByKey($key): ?Select2ResultEntity;

}
