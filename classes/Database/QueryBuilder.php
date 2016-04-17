<?php

namespace Database;

/**
 * Use named placeholders (':name') to bind data into queries;
 *   do not use positional placeholders ('?');
 *   placeholders starting with underscore are reserved (':_name').
 * Use whitelists for identifiers (like column names) and keywords ('AND', 'LIKE', etc.) that come from users, e.g. GET 'page/?order=name&dir=desc'.
 * Names of tables/databases must be defined directly in code.
 *
 * Please specify 'return' type in phpDocs properly for all public methods with 'return' statement.
 */
class QueryBuilder
{
    /**
     * @var Connection
     */
    protected $connection;
    /**
     * @var Grammar
     */
    protected $grammar;

    public function __construct(Connection $connection, Grammar $grammar)
    {
        $this->connection = $connection;
        $this->grammar = $grammar;
    }

    public function __clone()
    {
        $this->grammar = clone $this->grammar;
    }

    /**
     * @param array|string $columns
     * @return $this
     */
    public function select($columns = ['*'])
    {
        $this->grammar->columns = (is_array($columns)) ? $columns : func_get_args();
        return $this;
    }

    /**
     * @return $this
     */
    public function distinct()
    {
        $this->grammar->distinct = true;
        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function from($table)
    {
        $this->grammar->from = $table;
        return $this;
    }

    /**
     * @param array|string $column
     * @param string|mixed $operator
     * @param mixed $value
     * @return $this
     */
    public function where($column, $operator = '', $value = null)
    {
        if (func_num_args() == 1) {
            $this->grammar->wheres[] = $column;
            return $this;
        }

        if (func_num_args() == 2) {
            list($operator, $value) = ['=', $operator]; // shortcut
            if (is_array($value)) {
                $operator = 'in';
            }
        }
        return $this->where([$column, $operator, $value]);
    }

    /**
     * @param array|string $column
     * @return $this
     */
    public function groupBy($column)
    {
        $columns = (is_array($column)) ? $column : func_get_args();
        $this->grammar->groups = array_merge((array)$this->grammar->groups, $columns);
        return $this;
    }

    /**
     * @param array|string $column
     * @param string|mixed $operator
     * @param mixed $value
     * @return $this
     */
    public function having($column, $operator = '', $value = null)
    {
        if (func_num_args() == 1) {
            $this->grammar->havings[] = $column;
            return $this;
        }

        if (func_num_args() == 2) {
            list($operator, $value) = ['=', $operator];
            if (is_array($value)) {
                $operator = 'in';
            }
        }
        return $this->having([$column, $operator, $value]);
    }

    /**
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $direction = (strtolower($direction) === 'desc') ? 'desc' : 'asc';
        $this->grammar->orders[] = compact('column', 'direction');
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function limit($value)
    {
        $value = (int)$value;
        if ($value > 0) {
            $this->grammar->limit = $value;
        }
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function take($value)
    {
        return $this->limit($value);
    }

    /**
     * @param int $value
     * @return $this
     */
    public function offset($value)
    {
        $this->grammar->offset = max(0, (int)$value);
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function skip($value)
    {
        return $this->offset($value);
    }

    /**
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function bind($key, $value = null)
    {
        $bindings = (is_array($key) && is_null($value)) ? $key : [$key => $value];
        $this->grammar->manualBindings = array_merge($this->grammar->manualBindings, $bindings);
        return $this;
    }

    /**
     * @param array|string $columns
     * @return array
     */
    public function get($columns = ['*'])
    {
        if (is_null($this->grammar->columns)) {
            $this->select((is_array($columns)) ? $columns : func_get_args());
        }

        $sql = $this->grammar->compileSelect($this);
        return $this->connection->select($sql, $this->grammar->bindings);
    }

    /**
     * @param array $values
     * @return string
     */
    public function insert(array $values)
    {
        if (!is_array(reset($values))) {
            $values = [$values]; // treat every INSERT as BULK INSERT
        } else {
            foreach ($values as $k => $v) {
                ksort($v);
                $values[$k] = $v;
            }
        }

        $bindings = [];
        foreach ($values as $record) {
            $bindings = array_merge($bindings, array_values($record));
        }

        $sql = $this->grammar->compileInsert($values);
        return $this->connection->insert($sql, $bindings);
    }

    /**
     * @param array $values
     * @return int
     */
    public function update(array $values)
    {
        $sql = $this->grammar->compileUpdate($values);
        return $this->connection->update($sql, $this->grammar->bindings);
    }

    /**
     * @return int
     */
    public function delete()
    {
        $sql = $this->grammar->compileDelete($this);
        return $this->connection->delete($sql, $this->grammar->bindings);
    }

    /**
     * @param string $sql
     * @param array $bindings
     * @throws \InvalidArgumentException
     * @return array
     */
    public function rawSelect($sql, array $bindings = [])
    {
        if (mb_strtolower(mb_substr($sql, 0, 6)) !== 'select') throw new \InvalidArgumentException('Query string must start with "select"');

        $bindings = array_merge($this->grammar->manualBindings, $bindings);
        return $this->connection->select($sql, $bindings);
    }

    /**
     * @param array|string $columns
     * @return mixed
     */
    public function first($columns = ['*'])
    {
        $rows = $this->limit(1)->get((is_array($columns)) ? $columns : func_get_args());
        return (!empty($rows)) ? $rows[0] : null;
    }

    /**
     * @param string $column
     * @return mixed
     */
    public function pluck($column)
    {
        $row = $this->first([$column]);
        return (!empty($row)) ? $this->value($row, $column) : null;
    }

    /**
     * @param string $column
     * @param string $key
     * @return array
     */
    public function lists($column, $key = null)
    {
        $columns = (is_null($key)) ? [$column] : [$column, $key];
        $rows = $this->get($columns);

        $res = [];
        foreach ($rows as $row) {
            $value = ($column == '*') ? $row : $this->value($row, $column);
            if (is_null($key)) {
                $res[] = $value;
            } else {
                $key_value = $this->value($row, $key);
                $res[$key_value] = $value;
            }
        }
        return $res;
    }

    /**
     * @param mixed $id
     * @param array|string $columns
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        return $this->where('id', '=', $id)->first($columns);
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->count() > 0;
    }

    /**
     * @param string $column
     * @return int
     */
    public function count($column = '*')
    {
        return (int)$this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * @param string $column
     * @return mixed
     */
    public function min($column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * @param string $column
     * @return mixed
     */
    public function max($column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * @param string $column
     * @return mixed
     */
    public function sum($column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * @param string $column
     * @return mixed
     */
    public function avg($column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * @param string $function
     * @param array $columns
     * @return mixed
     */
    protected function aggregate($function, $columns = ['*'])
    {
        $this->grammar->aggregate = compact('function', 'columns');
        $res = $this->get($columns);

        $this->grammar->aggregate = null;
        $this->grammar->columns = null;

        return (!empty($res)) ? $this->value($res[0], 'aggregate') : null;
    }

    /**
     * @param mixed $row
     * @param string $column
     * @throws \DomainException
     * @return mixed
     */
    protected function value($row, $column)
    {
        $fetch_style = $this->connection->getFetchStyle();
        switch ($fetch_style) {
            case \PDO::FETCH_ASSOC:
                return $row[$column];
            case \PDO::FETCH_OBJ:
                return $row->$column;
            default: throw new \DomainException('Unknown fetch style: '.$fetch_style);
        }
    }
}
