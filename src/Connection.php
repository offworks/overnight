<?php
namespace Overnight;

class Connection
{
	/**
	 * PDO Instance
	 * @var \PDO pdo
	 */
	protected $pdo;

	/**
	 * Overrideable classes
	 * @var array classes
	 */
	protected $classes = array(
		'select' => '\Overnight\Query\Select',
		'update' => '\Overnight\Query\Update',
		'insert' => '\Overnight\Query\Insert',
		'delete' => '\Overnight\Query\Delete',
		'result' => '\Overnight\Query\Result'
	);

	public function __construct(\Pdo $pdo, $classes = array())
	{
		$this->pdo = $pdo;

		foreach($classes as $type => $class)
			$this->classes[$type] = $class;
	}

	public function setPdoAttribute($attr, $value)
	{
		$this->pdo->setAttribute($attr, $value);
	}

	public static function create($host, $user, $pass, $db, array $classes = array())
	{
		$pdo = new \Pdo('mysql:host='.$host.';dbname='.$db, $user, $pass, array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));

		$connection = new static($pdo, $classes);

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
		$class = $this->classes['select'];

		$query = new $class($this);

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
		$class = $this->classes['select'];

		$query = new $class($this);

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
	public function insert($table = null, array $values = array())
	{
		$class = $this->classes['insert'];

		$insert = new $class($this);

		if($table)
			$insert->into($table);

		if(count($values) > 0)
			$insert->values($values);

		return $insert;
	}

	/**
	 * Create update query
	 * @return \Overnight\Query\Update
	 */
	public function update($table = null)
	{
		$class = $this->classes['update'];

		$update = new $class($this);

		if($table)
			$update->table($table);

		return $update;
	}

	/**
	 * Create delete query
	 * @return \Overnight\Query\Delete
	 */
	public function delete($table = null)
	{
		$class = $this->classes['delete'];

		$delete = new $class($this);

		if($table)
			$delete->table($table);

		return $delete;
	}

	public function execute($sql, array $values = array(), array $params = array())
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

		$class = $this->classes['result'];

		return new $class($this, $statement);
	}

	/**
	 * Get last insert id
	 * @return int
	 */
	public function getLastInsertId()
	{
		return $this->pdo->lastInsertId();
	}

	/**
	 * Alias to getLastInsertId()
	 * @return int
	 */
	public function lastInsertId()
	{
		return $this->pdo->lastInsertId();
	}
}
