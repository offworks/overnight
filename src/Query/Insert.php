<?php
namespace Overnight\Query;

class Insert extends Base
{
	protected $fields = array();

	public function into($table)
	{
		return $this->table($table);
	}

	public function values(array $data)
	{
		$this->data = $data;

		return $this;
	}

	protected function prepareFields($bind = true)
	{
		$fields = array();

		foreach($this->data as $key => $value)
		{
			if($bind)
				$this->values[] = $value;

			$fields[] = $key;
		}

		return $fields;
	}

	public function prepareSql($bind = true)
	{
		$table = implode(', ', $this->tables);

		$fields = $this->prepareFields($bind);

		$sql = 'INSERT INTO '.$table.' ('.implode(', ', $fields).') VALUES ('.rtrim(str_repeat('?, ', count($fields)),', ').')';

		return $sql;
	}
}