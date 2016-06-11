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

	public function orderBy($column)
	{
		$this->orderBys[] = $column.($order ? ' '.strtoupper($order) : '');

		return $this;
	}

	public function groupBy($column)
	{
		$this->groupBy[] = is_array($column) ? implode(', ', $column) : $column;

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

	protected function genericJoin($type, $table, $condition, array $values = array())
	{
		$this->joins[] = $type.' JOIN '.$table.' ON '.$condition;

		foreach($values as $value)
			$this->values[] = $value;

		return $this;
	}

	public function limit($limit, $offset = null)
	{
		$this->limit = 'LIMIT '.(!$offset ? $limit : $offset.', '.$limit);

		return $this;
	}

	public function getRawSql()
	{
		$selects = count($this->selects) == 0 ? '*' : implode(', ', $this->selects);

		$tables = implode(', ', $this->tables);

		$joins = implode(', ', $this->joins);
		
		$wheres = count($this->wheres) > 0 ? 'WHERE '.implode('', $this->wheres) : '';
		
		$orderBys = count($this->orderBys) > 0 ? 'ORDER BY '.implode(', ',$this->orderBys) : '';

		$limit = $this->limit ? $this->limit : '';
		
		$groupBys = count($this->groupBys) > 0 ? 'GROUP BY '.implode(', ', $groupBy) : '';

		return 'SELECT '.$selects.' FROM '.$tables.' '.$joins.' '.$wheres.' '.$groupBys.' '.$orderBys.' '.$limit;
	}

	/**
	 * Execute the query
	 * @throws \Exception
	 */
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