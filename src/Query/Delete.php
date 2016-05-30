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

		$datas = array();
		
		foreach($this->data as $key => $value)
		{
			$datas = $key.' = ?';
			$this->values[] = $value;
		}

		$sql = 'DELETE FROM '.$table.' SET '.implode(', ', $datas).' '.$wheres;

		return $sql;
	}

	public function execute()
	{
		try
		{
			$result = $this->connection->execute($this->getRawSql(), $this->values, 'delete');
		}
		catch(\Exception $e)
		{
			throw $e;
		}

		return $result;
	}
}
