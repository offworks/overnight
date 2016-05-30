<?php
namespace Overnight;

class Connection
{
	public function __construct(\Pdo $pdo)
	{
		$this->pdo = $pdo;
	}

	public static function create($host, $user, $pass, $db)
	{
		$connection = new static(new \Pdo('mysql:host='.$host.';dbname='.$db, $user, $pass));

		return $connection;
	}

	/**
	 * Get Pdo instance
	 * @return \Pdo
	 */
	public function getPdo()
	{
		return $this->pdo;
	}

	/**
	 * Create select query
	 * @return \Overnight\Query\Select
	 */
	public function select($columns = '*', $table = null)
	{
		$query = new Query\Select($this);

		$query->select($columns);

		if($table)
			$query->table($table);

		return $query;
	}

	/**
	 * Create select query
	 * with table name as the first argument
	 * @return \Overnight\Query\Select
	 */
	public function table($table)
	{
		$query = new \Query\Select($this);

		$query->table($table);

		return $query;
	}

	/**
	 * Alias to table()
	 * @return \Overnight\Query\Select
	 */
	public function from($table)
	{
		return $this->table($table);
	}

	/**
	 * Create insert query
	 * @return \Overnight\Query\Insert
	 */
	public function insert($table = null, array $data = array())
	{
		$insert = new Query\Insert($this);

		if($table)
			$insert->into($table);

		if(count($data) > 0)
			$insert->setData($data);

		return $insert;
	}

	/**
	 * Create update query
	 * @return \Overnight\Query\Update
	 */
	public function update($table = null)
	{
		$update = new Query\Update($this);

		if($table)
			$update->table($table);

		return $update;
	}

	protected function createResult($connection, $statement)
	{
		return new Result($connection, $statement);
	}

	public function execute($sql, array $values = array(), array $params = array(), $type = 'select')
	{
		$statement = $this->pdo->prepare($sql);

		foreach($params as $name => &$paramValue)
			$statement->bindParam(':'.$name, $paramValue);

		foreach($values as $no => &$value)
			$statement->bindParam($no+1, $value);

		if(!$statement->execute())
		{
			$error = $statement->errorInfo();

			throw new \Exception($error[2]);
		}

		if($type == 'select')
			return new Query\Result($this, $statement);
		else
			return true;
	}

	/**
	 * Get last insert id
	 * @return int
	 */
	public function lastInsertId()
	{
		return $this->pdo->lastInsertId();
	}
}

?>