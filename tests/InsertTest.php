<?php

class InsertTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        require_once __DIR__ . '/../src/autoload.php';

        $this->connection = new \Overnight\Connection(new \FakePdo);
    }

    public function testInsert()
    {
        $query = $this->connection->insert('foo_table')->values(array(
            'foo_column' => 123,
            'bar_column' => 234
        ));

        $this->assertEquals('INSERT INTO foo_table (foo_column, bar_column) VALUES (?, ?)', $query->getRawSql());
    }
}