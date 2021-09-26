<?php

declare(strict_types = 1);

namespace TomasKarlik\Select2Input;


class Select2DataSource implements ISelect2DataSearch
{

	/**
	 * @var ISelect2DataSourceMultiple
	 */
	private $select2DataSourceMultiple;


	public function __construct(ISelect2DataSourceMultiple $select2DataSourceMultiple)
	{
		$this->select2DataSourceMultiple = $select2DataSourceMultiple;
	}


	public function searchTerm(string $query, int $limit, int $offset): array
	{
		return $this->select2DataSourceMultiple->searchTerm($query, $limit, $offset);
	}


	/**
	 * @param mixed $key
	 */
	public function findByKey($key): ?Select2ResultEntity
	{
		$results = $this->select2DataSourceMultiple->findByKeys([$key]);
		$first = reset($results);
		return $first === FALSE ? NULL : $first;
	}

}
