# Select2Input

Install
-------
Download package
```console
composer require tomaskarlik/select2input
````

Register extension in ```config.neon```
```neon
select2: TomasKarlik\Select2Input\DI\Select2InputExtension
```

Download [Select2](https://select2.org/) or add to ```bower.json``` and include JS to your project
```json
"dependencies": {
	"select2": "~4.0"
}
```

```js
$(document).ready(function() {
	$('.select2').each(function() {
		$(this).select2({
			ajax: {
				url: $(this).data('select2-url'),
				dataType: 'json',
				// custom parameters
			}
		  });
	});
});
```

Form usage
```php
$form = new Form;
$form->addSelect2('client_id', $clientRepository, 'Klient:')
	->setRequired('Vyberte klienta!')
	->setResultsPerPage(15);
$form->addSelect2Multiple('clients', $clientRepository, 'Klienti:')
```

Datasource example
```php
namespace App\Model\Repository;

use TomasKarlik\Select2Input\ISelect2DataSource;
use TomasKarlik\Select2Input\Select2ResultEntity;


class ClientRepository implements ISelect2DataSource
{

	/**
	 * @param string $query
	 * @param int $limit
	 * @param int $offset
	 * @return Select2ResultEntity[]
	 */
	public function searchTerm(string $query, int $limit, int $offset): array
	{
		$return = [];
		$selection = $this->getClientTable()
			->where(
				'company ILIKE ?', '%' . $query . '%'
			)
			->order('company')
			->select('client.id, client.company')
			->limit($limit, $offset);

		while ($row = $selection->fetch()) {
			$result = new Select2ResultEntity;
			$result->setId($row->id);
			$result->setText($row->company);
			$return[] = $result;
		}
		return $return;
	}


	/**
	 * @param string $query
	 * @return int
	 */
	public function searchTermCount(string $query): int
	{
		return $this->getClientTable()
			->where(
				'company ILIKE ?', '%' . $query . '%'
			)
			->count('*');
	}


	/**
	 * @param mixed $key
	 * @return Select2ResultEntity|NULL
	 */
	public function findByKey($key): ?Select2ResultEntity
	{
		if ( ! is_numeric($key)) {
			return NULL;
		}

		$client = $this->getClientTable()->wherePrimary($key)->fetch();
		if ( ! $client) {
			return NULL;
		}

		$result = new Select2ResultEntity;
		$result->setId($client->id);
		$result->setText($client->company);
		return $result;
	}

}
```
