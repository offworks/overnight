<?php
namespace Overnight\Query;

class Select extends Base
{
	protected $tables = array();

	protected $selects = array();

	protected $joins = array();

	protected $orderBys = array();

	protected $groupBys = array();

	protected $limit = null;

	/**
	 * @param string|array table
	 * @return self
	 */
	public function from($table)
	{
		$this->tables[] = is_array($table) ? implode(',', $table) : $table;

		return $this;
	}

	/**
	 * @param string|array column
	 * @return self
	 */
	public function select($column)
	{
		$this->selects[] = is_array($column) ? implode(', ', $column) : $column;

		return $this;
	}

	public function orderBy($order)
	{
		$this->orderBys[] = $order;

		return $this;
	}

	public function groupBy($column)
	{
		$this->groupBys[] = $column;

		return $this;
	}

	public function innerJoin($table, $condition, array $values = array())
	{
		return $this->genericJoin('INNER', $table, $condition, $values);
	}

	public function leftJoin($table, $condition, array $values = array())
	{
		return $this->genericJoin('LEFT', $table, $condition, $values);
	}

	public function rightJoin($table, $condition, array $values = array())
	{
		return $this->genericJoin('RIGHT', $table, $condition, $values);
	}

	protected function genericJoin($type, $table, $condition, array $values = array())
	{
		$this->joins[] = array($type.' JOIN '.$table.' ON '.$condition, $values);

		return $this;
	}

	public function limit($limit, $offset = null)
	{
		$this->limit = array((!$offset ? '?' : '?, ?'), (!$offset ? array($limit) : array($offset, $limit)));

		return $this;
	}

	/**
	 * @return string
	 */
	public function prepareLimit($bind = true)
	{
		if($bind)
		{
			foreach($this->limit[1] as $value)
				$this->values[] = $value;

			$this->connection->setPdoAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
		}

		return $this->limit[0];
	}

	/**
	 * Get list of joins
	 * @return array
	 */
	protected function prepareJoin($bind = true)
	{
		$joins = array();

		foreach($this->joins as $join)
		{
			if($bind)
			{
				foreach($join[1] as $value)
					$this->values[] = $value;
			}

			$joins[] = $join[0];
		}

		return $joins;
	}

	public function prepareSql($bind = true)
	{
		$selects = count($this->selects) == 0 ? '*' : implode(', ', $this->selects);

		$tables = implode(', ', $this->tables);

		$joins = count($this->joins) > 0 ? implode(', ', $this->prepareJoin($bind)) : '';
		
		$wheres = count($this->wheres) > 0 ? 'WHERE '.implode('', $this->prepareWhere($bind)) : '';
		
		$orderBys = count($this->orderBys) > 0 ? 'ORDER BY '.implode(', ',$this->orderBys) : '';

		$limit = $this->limit ? 'LIMIT '.$this->prepareLimit($bind) : '';

		$groupBys = count($this->groupBys) > 0 ? 'GROUP BY '.implode(', ', $this->groupBys) : '';

		return 'SELECT '.$selects.' FROM '.$tables.' '.$joins.' '.$wheres.' '.$groupBys.' '.$orderBys.' '.$limit;
	}
}