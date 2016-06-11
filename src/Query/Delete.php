<?php
namespace Overnight\Query;

class Delete extends Base
{
	protected $data = array();

	public function setData(array $data)
	{
		$this->data = $data;
	}

	public function prepareSql($bind = true)
	{
		$table = implode(', ', $this->tables);
		
		$wheres = count($this->wheres) > 0 ? 'WHERE  '.implode('', $this->prepareWhere($bind)) : '';

		$sql = 'DELETE FROM '.$table.' '.$wheres;

		return $sql;
	}
}
