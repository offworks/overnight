<?php

namespace Overnight\Query;

class Select extends Base
{
    protected $tables = array();

    protected $selects = array();

    protected $joins = array();

    protected $orderBys = array();

    protected $groupBys = array();

    protected $havings = array();

    protected $limit = null;

    /**
     * @param string|array table
     * @return self
     */
    public function from($table)
    {
        $this->tables[] = is_array($table) ? implode(', ', $table) : $table;

        return $this;
    }

    /**
     * @param string|array column
     * @return self
     */
    public function select($column)
    {
        $this->selects[] = is_array($column) ? implode(', ', $column) : $column;

        return $this;
    }

    public function orderBy($order)
    {
        $this->orderBys[] = $order;

        return $this;
    }

    public function groupBy($column)
    {
        $this->groupBys[] = $column;

        return $this;
    }

    public function innerJoin($condition, array $values = array())
    {
        return $this->genericJoin('INNER', $condition, $values);
    }

    public function leftJoin($condition, array $values = array())
    {
        return $this->genericJoin('LEFT', $condition, $values);
    }

    public function rightJoin($condition, array $values = array())
    {
        return $this->genericJoin('RIGHT', $condition, $values);
    }

    protected function genericJoin($type, $condition, array $values = array())
    {
        $this->joins[] = array($type . ' JOIN ' . $condition, $values);

        return $this;
    }

    public function join($type, $table, $condition, array $values = array())
    {
        return $this->genericJoin($type, $table, $condition, $values);
    }

    public function limit($limit, $offset = null)
    {
        $this->limit = array(($offset === null ? '?' : '?, ?'), ($offset === null ? array($limit) : array($offset, $limit)));

        return $this;
    }

    public function having($condition, array $values = array())
    {
        return $this->genericHaving($condition, $values);
    }

    public function andHaving()
    {
        return $this->genericHaving($condition, $values, 'AND');
    }

    public function orHaving()
    {
        return $this->genericHaving($condition, $values, 'OR');
    }

    protected function genericHaving($condition, array $values = array(), $limiter = 'AND')
    {
        $prefix = count($this->havings) > 0 ? ' ' . $limiter . ' ' : '';

        $this->havings[] = array($prefix . trim($condition), $values);

        return $this;
    }

    /**
     * @return array
     */
    protected function prepareHaving($bind = true)
    {
        $havings = array();

        foreach ($this->havings as $having) {
            if ($bind) {
                foreach ($having[1] as $value)
                    $this->values[] = $value;
            }

            $havings[] = $having[0];
        }

        return $havings;
    }

    /**
     * @return string
     */
    protected function prepareLimit($bind = true)
    {
        if ($bind) {
            foreach ($this->limit[1] as $value)
                $this->values[] = $value;

            $this->connection->setPdoAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        }

        return $this->limit[0];
    }

    /**
     * Get list of joins
     * @return array
     */
    protected function prepareJoin($bind = true)
    {
        $joins = array();

        foreach ($this->joins as $join) {
            if ($bind) {
                foreach ($join[1] as $value)
                    $this->values[] = $value;
            }

            $joins[] = $join[0];
        }

        return $joins;
    }

    protected function prepareSql($bind = true)
    {
        $sql = 'SELECT ' . (count($this->selects) == 0 ? '*' : implode(', ', $this->selects)) . ' FROM ' . implode(', ', $this->tables);

        if (count($this->joins) > 0)
            $sql .= ' ' . implode(' ', $this->prepareJoin($bind));

        if (count($this->wheres) > 0)
            $sql .= ' WHERE ' . implode('', $this->prepareWhere($bind));

        if (count($this->groupBys) > 0)
            $sql .= ' GROUP BY ' . implode(', ', $this->groupBys);

        if (count($this->havings) > 0)
            $sql .= ' HAVING ' . implode('', $this->prepareHaving($bind));

        if (count($this->orderBys) > 0)
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBys);

        if ($this->limit)
            $sql .= ' LIMIT ' . $this->prepareLimit($bind);

        return $sql;
    }
}