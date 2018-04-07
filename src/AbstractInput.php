<?php

declare(strict_types = 1);

namespace TomasKarlik\Select2Input;

use InvalidArgumentException;
use Nette\Application\UI\BadSignalException;
use Nette\Application\UI\ISignalReceiver;
use Nette\Application\UI\Presenter;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Helpers;
use Nette\Reflection\ClassType;
use Nette\Reflection\Method;
use Nette\Utils\Html;
use stdClass;


abstract class AbstractInput extends BaseControl implements ISignalReceiver
{

	/**
	 * @var string
	 */
	private $pageParamName = 'page';

	/**
	 * @var string
	 */
	private $queryParamName = 'q';

	/**
	 * @var int
	 */
	private $resultsPerPage = 25;


	public function getParameterId(string $name): string
	{
		$uid = $this->getUniqueId();
		return $uid === '' ? $name : $uid . self::NAME_SEPARATOR . $name;
	}


	public function getUniqueId(): ?string
	{
		return $this->lookupPath(Presenter::class, TRUE);
	}


	public function getControl(): Html
	{
		$attributes = parent::getControl()->attrs;
		$attributes['data-select2-url'] = $this->link('autocomplete!');

		$items = $this->getSelectedItems();
		return Helpers::createSelectBox($items, NULL, array_keys($items))
			->addAttributes($attributes)
			->addClass('select2');
	}


	public function handleAutocomplete(): void
	{
		$presenter = $this->getPresenter();

		$query = $presenter->getParam($this->queryParamName);
		$page = max((int) $presenter->getParam($this->pageParamName, 1), 1);

		$return = [
			'results' => [],
			'total_count' => 0,
			'pagination' => [
				'more' => FALSE
			]
		];

		if (empty($query)) {
			$presenter->sendJson($return);
			return;
		}

		$count = $this->getDataSource()->searchTermCount($query);
		if ( ! $count) {
			$presenter->sendJson($return);
			return;
		}

		$offsetStart = ($page - 1) * $this->resultsPerPage;
		$offsetEnd = $offsetStart + $this->resultsPerPage;

		if ($offsetEnd < $count) {
			$return['pagination']['more'] = TRUE;
		}
		$return['total_count'] = $count;

		$results = $this->getDataSource()->searchTerm($query, $this->resultsPerPage, $offsetStart);
		foreach ($results as $result) {
			$return['results'][] = $this->formatResult($result);
		}

		$presenter->sendJson($return);
	}


	/**
	 * {@inheritdoc}
	 */
	public function signalReceived($signal): void
	{
		$method = $this->formatSignalMethod($signal);
		$reflection = new ClassType($this);
		if ( ! $reflection->hasMethod($method)) {
			throw new BadSignalException(sprintf('There is no handler for signal "%s"', $signal));
		}
		$reflectionMethod = $reflection->getMethod($method);
		$reflectionMethod->invoke($this);
	}


	public static function formatSignalMethod(?string $signal): ?string
	{
		return $signal == NULL ? NULL : 'handle' . $signal;
	}


	public function getPageParamName(): string
	{
		return $this->pageParamName;
	}


	public function setPageParamName(string $pageParamName): AbstractInput
	{
		$this->pageParamName = $pageParamName;
		return $this;
	}


	public function getQueryParamName(): string
	{
		return $this->queryParamName;
	}


	public function setQueryParamName(string $queryParamName): AbstractInput
	{
		$this->queryParamName = $queryParamName;
		return $this;
	}


	public function getResultsPerPage(): int
	{
		return $this->resultsPerPage;
	}


	public function setResultsPerPage(int $resultsPerPage): AbstractInput
	{
		$this->resultsPerPage = $resultsPerPage;
		return $this;
	}


	protected abstract function getDataSource(): ISelect2DataSearch;


	protected abstract function getSelectedItems(): array;


	private function getPresenter(bool $need = TRUE): ?Presenter
	{
		return $this->lookup(Presenter::class, $need);
	}


	private function link(string $destination, array $params = []): string
	{
		if (substr($destination, -1) !== '!') { // only signals
			throw new InvalidArgumentException;
		}
		$reflectionMethod = new Method(Presenter::class, 'createRequest');
		$reflectionMethod->setAccessible(TRUE); // @TODO better way?
		return $reflectionMethod->invoke($this->getPresenter(), $this, $destination, $params, 'link');
	}


	private function formatResult(Select2ResultEntity $result): stdClass
	{
		$row = [
			'id' => $result->getId(),
			'text' => (string) $result
		];

		if ($result->isSelected()) {
			$row['selected'] = TRUE;
		}

		if ($result->hashChilds()) {
			$row['children'] = [];
			foreach ($result->getChilds() as $child) {
				$row['children'][] = $this->formatResult($child);
			}
		}

		if ($result->hashCustomParams()) {
			foreach ($result->getCustomParams() as $key => $value) {
				$row[$key] = $value;
			}
		}

		return (object) $row;
	}

}
