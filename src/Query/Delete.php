<?php

namespace Overnight\Query;

class Delete extends Base
{
    protected function prepareSql($bind = true)
    {
        $table = implode(', ', $this->tables);

        $wheres = count($this->wheres) > 0 ? 'WHERE ' . implode('', $this->prepareWhere($bind)) : '';

        $sql = 'DELETE FROM ' . $table . ' ' . $wheres;

        return $sql;
    }
}
