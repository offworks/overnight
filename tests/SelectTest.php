<?php
require_once 'FakePdo.php';

class SelectTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		require_once __DIR__.'/../src/autoload.php';
		
		$this->connection = new \Overnight\Connection(new \FakePdo);
	}

	public function testSelect()
	{
		$query = $this->connection
		->from('foo_table');

		$this->assertEquals('SELECT * FROM foo_table', $query->getRawSql());
	}

	public function testWhere()
	{
		$query = $this->connection->from('foo_table')->where('foo_column = ?', array('bar'));

		$this->assertEquals('SELECT * FROM foo_table WHERE foo_column = ?', $query->getRawSql());

		$query->where('bar_column = ?', array('baz'));

		$this->assertEquals('SELECT * FROM foo_table WHERE foo_column = ? AND bar_column = ?', $query->getRawSql());

		$query->orWhere('baz_column = ?', array('qux'));

		$this->assertEquals('SELECT * FROM foo_table WHERE foo_column = ? AND bar_column = ? OR baz_column = ?', $query->getRawSql());
	}

	public function testOrderBy()
	{
		$query = $this->connection->from('foo_table')->where('foo_column = ?', array('bar'))->orderBy('bar_column DESC');

		$this->assertEquals('SELECT * FROM foo_table WHERE foo_column = ? ORDER BY bar_column DESC', $query->getRawSql());

		$query->orderBy('baz_column ASC');

		$this->assertEquals('SELECT * FROM foo_table WHERE foo_column = ? ORDER BY bar_column DESC, baz_column ASC', $query->getRawSql());
	}

	public function testLimit()
	{
		$query = $this->connection->from('foo_table')->where('foo_column = ?', array('bar'))->orderBy('bar_column')->limit(1);

		$this->assertEquals('SELECT * FROM foo_table WHERE foo_column = ? ORDER BY bar_column LIMIT ?', $query->getRawSql());

		$query->limit(1, 0);

		$this->assertEquals('SELECT * FROM foo_table WHERE foo_column = ? ORDER BY bar_column LIMIT ?, ?', $query->getRawSql());
	}

	public function testJoin()
	{
		$query = $this->connection->from('foo_table')
		->where('foo_column = ?', array('bar'))
		->orderBy('baz_column')
		->limit(1, 0)
		->leftJoin('bar_table', 'foo_table.foo_column = bar_table.qux_column');

		$this->assertEquals('SELECT * FROM foo_table LEFT JOIN bar_table ON foo_table.foo_column = bar_table.qux_column WHERE foo_column = ? ORDER BY baz_column LIMIT ?, ?', $query->getRawSql());

		$query->innerJoin('baz_table', 'baz_table.foo_column = ?', array('qux'));

		$this->assertEquals('SELECT * FROM foo_table LEFT JOIN bar_table ON foo_table.foo_column = bar_table.qux_column INNER JOIN baz_table ON baz_table.foo_column = ? WHERE foo_column = ? ORDER BY baz_column LIMIT ?, ?', $query->getRawSql());
	}

	public function testGroupBy()
	{
		$query = $this->connection->from('foo_table')
		->groupBy('foo_column')
		->where('foo_column = ?', array('bar'))
		->limit(1, 0)
		->orderBy('baz_column')
		->leftJoin('bar_table', 'foo_table.foo_column = bar_table.qux_column');

		$this->assertEquals('SELECT * FROM foo_table LEFT JOIN bar_table ON foo_table.foo_column = bar_table.qux_column WHERE foo_column = ? GROUP BY foo_column ORDER BY baz_column LIMIT ?, ?', $query->getRawSql());
	}
}