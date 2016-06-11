<?php
namespace Overnight\Query;

class Delete extends Base
{
	protected $data = array();

	public function setData(array $data)
	{
		$this->data = $data;
	}

	public function getRawSql()
	{
		$table = implode(', ', $this->tables);
		
		$wheres = count($this->wheres) > 0 ? 'WHERE '.implode('', $this->wheres) : '';

		$sql = 'DELETE FROM '.$table.' '.$wheres;

		return $sql;
	}

	public function execute()
	{
		try
		{
			$result = $this->connection->execute($this->getRawSql(), $this->values, $this->params);
		}
		catch(\Exception $e)
		{
			throw $e;
		}

		return $result;
	}
}
