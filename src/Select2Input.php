<?php

declare(strict_types = 1);

namespace TomasKarlik\Select2Input;

use InvalidArgumentException;


final class Select2Input extends AbstractInput
{

	/**
	 * @var ISelect2DataSourceMultiple
	 */
	private $dataSource;

	/**
	 * @var Select2ResultEntity|NULL
	 */
	private $selectedValue = NULL;


	public function __construct(
		ISelect2DataSourceMultiple $dataSource,
		string $label = NULL
	) {
		parent::__construct($label);
		$this->setOption('type', 'select');
		$this->dataSource = new Select2DataSource($dataSource);
	}


	/**
	 * {@inheritdoc}
	 */
	public function setValue($value)
	{
		$this->selectedValue = NULL;
		if ($value !== NULL) {
			$item = $this->dataSource->findByKey($value);
			if ( ! $item) {
				throw new InvalidArgumentException(sprintf('Value "%s" is not allowed!', $value));
			}

			$item->setSelected(TRUE);
			$this->selectedValue = $item;
		}

		return parent::setValue($value);
	}


	protected function getDataSource(): ISelect2DataSearch
	{
		return $this->dataSource;
	}


	protected function getSelectedItems(): array
	{
		return $this->selectedValue !== NULL ? [$this->selectedValue->getId() => (string) $this->selectedValue] : [];
	}

}
