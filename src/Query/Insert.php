<?php
namespace Overnight\Query;

class Insert extends Base
{
	protected $fields = array();

	public function into($table)
	{
		return $this->table($table);
	}

	public function setData(array $data)
	{
		foreach($data as $field => $value)
		{
			$this->fields[] = $field;
			
			$this->values[] = $value;
		}

		return $this;
	}

	public function getRawSql()
	{
		$table = implode(', ', $this->tables);

		$sql = 'INSERT INTO '.$table.' ('.implode(',', $this->fields).') VALUES ('.rtrim(str_repeat('?, ', count($this->fields)),', ').')';

		return $sql;
	}

	/**
	 * @return bool
	 */
	public function execute()
	{
		try
		{
			$result = $this->connection->execute($this->getRawSql(), $this->values, $this->params, 'insert');
		}
		catch(\Exception $e)
		{
			throw $e;
		}

		return $result;
	}
}