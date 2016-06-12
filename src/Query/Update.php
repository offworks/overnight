<?php
namespace Overnight\Query;

class Update extends Base
{
	protected $data = array();

	public function set($key, $value = null)
	{
		if(is_array($key))
			$this->data = $key;
		else
			$this->data[$key] = $value;

		return $this;
	}

	/**
	 * Prepare and bind values orderly
	 * @param boolean bind
	 */
	protected function prepareSql($bind = true)
	{
		$table = implode(', ', $this->tables);
		
		$datas = array();

		foreach($this->data as $key => $value)
			$datas[] = $key.' = ?';

		$datas = count($this->data) ? 'SET ' . implode(', ', $this->prepareData($bind)) : '';

		$wheres = count($this->wheres) ? 'WHERE ' . implode(', ', $this->prepareWhere($bind)) : '';

		$sql = 'UPDATE '.$table.' '.$datas.' '.$wheres;

		return $sql;
	}

	protected function prepareData($bind = true)
	{
		$datas = array();

		foreach($this->data as $key => $value)
		{
			if($bind)
				$this->values[] = $value;

			$datas[] = $key.' = ?';
		}

		return $datas;
	}
}