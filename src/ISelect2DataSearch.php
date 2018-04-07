<?php

declare(strict_types = 1);

namespace TomasKarlik\Select2Input;


interface ISelect2DataSearch
{

	/**
	 * @param string $query
	 * @param int $limit
	 * @param int $offset
	 * @return Select2ResultEntity[]
	 */
	function searchTerm(string $query, int $limit, int $offset): array;


	function searchTermCount(string $query): int;

}
