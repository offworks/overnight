<?php
namespace Overnight\Query;

abstract class Base
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

		$this->wheres[] = array($prefix.trim($condition), $values);

		return $this;
	}

	/**
	 * Get list of condition and bind values
	 * @return array
	 */
	protected function prepareWhere($bind = true)
	{
		$wheres = array();

		foreach($this->wheres as $where)
		{
			if($bind)
			{
				foreach($where[1] as $value)
					$this->values[] = $value;
			}

			$wheres[] = $where[0];
		}

		return $wheres;
	}

	abstract protected function prepareSql();

	public function getRawSql()
	{
		return trim($this->prepareSql(false));
	}

	/**
	 * Alias to getRawSql()
	 * @return string
	 */
	public function sql()
	{
		return $this->getRawSql();
	}

	/**
	 * Execute the query
	 * @throws \Exception
	 */
	public function execute()
	{
		try
		{
			$result = $this->connection->execute($this->prepareSql(), $this->values, $this->params);
		}
		catch(\Exception $e)
		{
			throw $e;
		}

		return $result;
	}
}

?>