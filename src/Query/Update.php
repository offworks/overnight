<?php
namespace Overnight\Query;

class Update extends Base
{
	protected $data = array();

	public function setData(array $data)
	{
		foreach($data as $key => $value)
			$this->values[] = $value;

		$this->data = $data;

		return $this;
	}

	public function getRawSql()
	{
		$table = implode(', ', $this->tables);
		
		$wheres = count($this->wheres) > 0 ? 'WHERE '.implode('', $this->wheres) : '';

		$datas = array();

		foreach($this->data as $key => $value)
			$datas[] = $key.' = ?';

		$sql = 'UPDATE '.$table.' SET '.implode(', ', $datas).' '.$wheres;

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