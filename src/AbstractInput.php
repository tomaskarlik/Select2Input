<?php declare(strict_types=1);

namespace TomasKarlik\Select2Input;

use Nette\Application\IPresenter;
use Nette\Application\UI\BadSignalException;
use Nette\Application\UI\Component;
use Nette\Application\UI\Control;
use Nette\Application\UI\SignalReceiver;
use Nette\Application\UI\Presenter;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Helpers;
use Nette\Utils\Html;
use stdClass;

abstract class AbstractInput extends BaseControl implements SignalReceiver
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
		return $this->lookupPath(Presenter::class);
	}


	public function getControl(): Html
	{
		$control = parent::getControl();
		assert($control instanceof Html);

		$items = $this->getSelectedItems();

		$path = $this->lookupPath(IPresenter::class);

		return Helpers::createSelectBox($items, null, array_keys($items))
			->addAttributes($control->attrs + [
					'data-select2-url' => $this->getPresenter()->link($path . ':autocomplete!'),
					'class' => 'select2',
				]);
	}


	public function handleAutocomplete(): void
	{
		$presenter = $this->getPresenter();

		$query = $presenter->getParameter($this->queryParamName, '');
		assert(is_string($query));
		$page = $presenter->getParameter($this->pageParamName, '1');
		assert(is_string($page));
		$page = max((int) $page, 1);

		$return = [
			'results' => [],
			'total_count' => 0,
			'pagination' => [
				'more' => false,
			],
		];

		if ($query === '') {
			$presenter->sendJson($return);
		}

		$offsetStart = ($page - 1) * $this->resultsPerPage;
		$limitForCount = $this->resultsPerPage + 1;

		$results = $this->getDataSource()->searchTerm($query, $limitForCount, $offsetStart);
		$count = count($results);
		if ($count >= $limitForCount) {
			$return['pagination']['more'] = true;
			array_pop($results);
		}
		$return['total_count'] = $count;

		foreach ($results as $result) {
			$return['results'][] = $this->formatResult($result);
		}

		$presenter->sendJson($return);
	}


	public function signalReceived(string $signal): void
	{
		$method = self::formatSignalMethod($signal);

		if ($method === null || !method_exists($this, $method)) {
			throw new BadSignalException(sprintf('There is no handler for signal "%s"', $signal));
		}
		$this->{$method}();
	}


	public static function formatSignalMethod(string $signal): string
	{
		return Component::formatSignalMethod($signal);
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


	/**
	 * @return array<string|int, string>
	 */
	protected abstract function getSelectedItems(): array;


	private function getPresenter(bool $need = true): Presenter
	{
		$presenter = $this->lookup(Presenter::class, $need);
		assert($presenter instanceof Presenter);

		return $presenter;
	}


	private function formatResult(Select2ResultEntity $result): stdClass
	{
		$row = [
			'id' => $result->getId(),
			'text' => (string) $result,
		];

		if ($result->isSelected()) {
			$row['selected'] = true;
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
