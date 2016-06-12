<?php
class InsertTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		require_once __DIR__.'/../src/autoload.php';

		$this->connection = \Overnight\Connection::create('localhost', 'root', '', 'persona');
	}

	public function testInsert()
	{
		
	}
}