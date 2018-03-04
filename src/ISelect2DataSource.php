<?php

declare(strict_types = 1);

namespace TomasKarlik\Select2Input;


interface ISelect2DataSource
{

	/**
	 * @param mixed $key
	 * @return Select2ResultEntity|NULL
	 */
	function findByKey($key): ?Select2ResultEntity;


	/**
	 * @param string $query
	 * @param int $limit
	 * @param int $offset
	 * @return Select2ResultEntity[]
	 */
	function searchTerm(string $query, int $limit, int $offset): array;


	function searchTermCount(string $query): int;

}
