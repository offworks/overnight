<?php

class UpdateTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        require_once __DIR__ . '/../src/autoload.php';

        $this->connection = new \Overnight\Connection(new \FakePdo);
    }

    public function testUpdate()
    {
        $query = $this->connection
            ->update('foo_table')
            ->where('foo_column = ?', array('bar'))
            ->andWhere('foo_column2 = ? AND foo_column3 = ?', array('baz', 'ban'))
            ->set(array(
                'bar_column' => 'baz',
                'baz_column' => 'qux'
            ));

        $this->assertEquals('UPDATE foo_table SET bar_column = ?, baz_column = ? WHERE (foo_column = ?) AND (foo_column2 = ? AND foo_column3 = ?)', $query->getRawSql());

        $query->set('qux_column', 'tux');

        $this->assertEquals('UPDATE foo_table SET bar_column = ?, baz_column = ?, qux_column = ? WHERE (foo_column = ?) AND (foo_column2 = ? AND foo_column3 = ?)', $query->getRawSql());
    }
}