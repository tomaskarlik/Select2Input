<?php

declare(strict_types=1);

namespace TomasKarlik\Select2Input;

use InvalidArgumentException;

final class Select2Input extends AbstractInput
{
	private Select2DataSource $dataSource;

	private ?Select2ResultEntity $selectedValue = null;


	public function __construct(
		ISelect2DataSourceMultiple $dataSource,
		string $label = null
	)
	{
		parent::__construct($label);
		$this->setOption('type', 'select');
		$this->dataSource = new Select2DataSource($dataSource);
	}


	/**
	 * @param int|string|null $value
	 * @return static
	 */
	public function setValue($value)
	{
		$this->selectedValue = null;
		if ($value !== null) {
			$item = $this->dataSource->findByKey($value);
			if ($item === null) {
				throw new InvalidArgumentException(sprintf('Value "%s" is not allowed!', (string) $value));
			}

			$item->setSelected(true);
			$this->selectedValue = $item;
		}

		return parent::setValue($value);
	}


	protected function getDataSource(): ISelect2DataSearch
	{
		return $this->dataSource;
	}


	/**
	 * @return array<string|int, string>
	 */
	protected function getSelectedItems(): array
	{
		return $this->selectedValue !== null ? [$this->selectedValue->getId() => $this->selectedValue->getText()] : [];
	}

}
