<?php

declare(strict_types = 1);

namespace TomasKarlik\Select2Input;

use InvalidArgumentException;
use Nette\Forms\Form;
use Nette\Utils\Html;


final class Select2InputMultiple extends AbstractInput
{

	/**
	 * @var ISelect2DataSourceMultiple
	 */
	private $dataSource;

	/**
	 * @var Select2ResultEntity[]
	 */
	private $selectedValues = [];


	public function __construct(
		ISelect2DataSourceMultiple $dataSource,
		string $label = NULL
	) {
		parent::__construct($label);
		$this->setOption('type', 'select');
		$this->dataSource = $dataSource;
	}


	public function getControl(): Html
	{
		$control = parent::getControl();
		$control->multiple(TRUE);
		return $control;
	}


	/**
	 * {@inheritdoc}
	 */
	public function loadHttpData()
	{
		$this->setValue(array_keys(array_flip($this->getHttpData(Form::DATA_TEXT))));
	}


	/**
	 * {@inheritdoc}
	 */
	public function setValue($value)
	{
		$this->selectedValues = [];

		if (is_scalar($value) || $value === NULL) {
			$value = (array) $value;

		} elseif ( ! is_array($value)) {
			throw new InvalidArgumentException(sprintf('Value must be array or NULL, %s given in field "%s".', gettype($values), $this->name));
		}

		if (count($value)) {
			$items = $this->dataSource->findByKeys($value);
			if ( ! $items) {
				throw new InvalidArgumentException('Unexpected values!');
			}

			foreach ($items as $item) {
				$this->selectedValues[$item->getId()] = (string) $item;
			}
		}

		parent::setValue($value);
		return $this;
	}


	public function getRawValue()
	{
		return $this->value;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getHtmlName(): string
	{
		return parent::getHtmlName() . '[]';
	}


	protected function getDataSource(): ISelect2DataSearch
	{
		return $this->dataSource;
	}


	protected function getSelectedItems(): array
	{
		return $this->selectedValues;
	}

}
