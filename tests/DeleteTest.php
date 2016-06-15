<?php
class DeleteTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		require_once __DIR__.'/../src/autoload.php';

		$this->connection = new \Overnight\Connection(new \FakePdo);
	}

	public function testDelete()
	{
		$query = $this->connection->delete('foo_table')
		->where('foo_column = ?', array('bar'));

		$this->assertEquals('DELETE FROM foo_table WHERE (foo_column = ?)', $query->getRawSql());
	}
}