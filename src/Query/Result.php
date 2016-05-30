<?php
namespace Overnight\Query;

class Result
{
	public function __construct(\Overnight\Connection $connection, \PDOStatement $statement)
	{
		$this->statement = $statement;

		$this->connection = $connection;
	}

	public function all()
	{
		return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function first()
	{
		$all = $this->all();

		return isset($all[0]) ? $all[0] : false;
	}
}