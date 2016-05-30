<?php
namespace Overnight\Query;

class Base
{
	protected $tables = array();

	protected $wheres = array();

	protected $values = array();

	protected $params = array();

	public function __construct(\Overnight\Connection $connection)
	{
		$this->connection = $connection;
	}

	public function table($table)
	{
		$this->tables[] = is_array($table) ? implode(',', $table) : $table;

		return $this;
	}

	public function bind(array $params)
	{
		foreach($params as $key => $value)
			$this->params[$key] = $value;

		return $this;
	}

	/**
	 * @param string condition
	 * @param array values
	 */
	public function where($condition, array $values = array())
	{
		return $this->genericWhere($condition, $values, 'AND');
	}

	public function andWhere($condition, array $values = array())
	{
		return $this->genericWhere($condition, $values, 'AND');
	}

	public function orWhere($condition, array $values = array())
	{
		return $this->genericWhere($condition, $values, 'OR');
	}

	protected function genericWhere($condition, array $values = array(), $limiter = 'AND')
	{
		$prefix = count($this->wheres) > 0 ? ' '.$limiter.' ' : '';

		$this->wheres[] = trim($condition);

		foreach($values as $value)
			$this->values[] = $value;

		return $this;
	}
}

?>