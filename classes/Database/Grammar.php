<?php

namespace Database;

class Grammar
{
    public $manualBindings = [];
    public $bindings = [];

    /**
     * @var array
     */
    public $aggregate;
    /**
     * @var array
     */
    public $columns;
    public $distinct = false;
    /**
     * @var string
     */
    public $from;
    /**
     * @var array
     */
    public $wheres;
    /**
     * @var array
     */
    public $groups;
    /**
     * @var array
     */
    public $havings;
    /**
     * @var array
     */
    public $orders;
    /**
     * @var int
     */
    public $limit;
    /**
     * @var int
     */
    public $offset;

    protected $selectComponents = [
        'aggregate',
        'columns',
        'from',
        'wheres',
        'groups',
        'havings',
        'orders',
        'limit',
        'offset',
    ];

    protected $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'not like',
        'in', 'not in',
        'between', 'not between',
    ];

    protected $clauseOperators = [
        'and', 'or',
        'and not', 'or not',
    ];

    protected $placeholderCounter = 0;

    public function compileSelect()
    {
        $this->resetBindings();
        if (is_null($this->columns)) {
            $this->columns = ['*'];
        }
        return trim($this->concatenate($this->compileSelectComponents()));
    }

    protected function compileSelectComponents()
    {
        $sql = [];
        foreach ($this->selectComponents as $component) {
            if (is_null($this->$component)) continue;

            $method = 'compile'.ucfirst($component);
            $sql[$component] = $this->$method($this->$component);
        }
        return $sql;
    }

    protected function compileAggregate($aggregate)
    {
        $column = $this->columnize($aggregate['columns']);
        if ($this->distinct && ($column !== '*')) {
            $column = 'distinct '.$column;
        }
        return 'select '.$aggregate['function'].'('.$column.') as aggregate';
    }

    protected function compileColumns($columns)
    {
        if (!is_null($this->aggregate)) return null;

        return ($this->distinct ? 'select distinct ' : 'select ').$this->columnize($columns);
    }

    protected function compileFrom($table)
    {
        return 'from '.$table;
    }

    protected function compileWheres($wheres)
    {
        if (is_null($wheres)) return '';

        $sql = [];
        foreach ($wheres as $where) {
            $sql[] = $this->compileGeneralWhere($where);
        }
        return 'where '.implode(' and ', $sql);
    }

    protected function compileGeneralWhere($where)
    {
        if (is_array($where) && is_string(array_keys($where)[0])) {
            return $this->compileAssocWhere($where);
        }
        return $this->compileWhere($where);
    }

    protected function compileAssocWhere($where)
    {
        $sql = [];
        foreach ($where as $k => $v) {
            $ph = $this->placeholder('whereAssoc');

            if (is_array($v)) {
                $v = array_values($v);
                $args = [];
                foreach ($v as $kk => $vv) {
                    $args[] = $ph.$kk;
                    $this->bindings[$ph.$kk] = $vv;
                }
                $sql[] = $k.' in ('.implode(', ', $args).')';
            } else {
                $sql[] = $k.' = '.$ph;
                $this->bindings[$ph] = $v;
            }
        }
        return implode(' and ', $sql);
    }

    protected function compileWhere($where)
    {
        if (!is_array($where)) return $where;
        if (empty($where)) return '';
        if (count($where) == 1) return $this->compileWhere($where[0]);

        if (count($where) == 2) {
            $where = [$where[0], '=', $where[1]]; // shortcut
            if (is_array($where[2])) {
                $where[1] = 'in';
            }
        }
        if (count($where) == 3) {
            list($column, $operator, $value) = $where;
            $operator = strtolower($operator);

            if (in_array($operator, $this->operators)) {
                $ph = $this->placeholder('whereOp');

                if (in_array($operator, ['like', 'not like'])) {
                    $this->bindings[$ph.'0'] = $value;
                    $this->bindings[$ph.'1'] = '=';
                    return "$column $operator {$ph}0 escape {$ph}1";
                } elseif (in_array($operator, ['in', 'not in']) && is_array($value)) {
                    if (empty($value)) {
                        if ($operator == 'in') {
                            return 'false';
                        } elseif ($operator == 'not in') {
                            return 'true';
                        }
                    }

                    $value = array_values($value);
                    $args = [];
                    foreach ($value as $k => $v) {
                        $args[] = $ph.$k;
                        $this->bindings[$ph.$k] = $v;
                    }
                    return "$column $operator (".implode(', ', $args).")";
                } elseif (in_array($operator, ['between', 'not between']) && is_array($value)) {
                    list($v1, $v2) = $value;
                    $this->bindings[$ph.'0'] = $v1;
                    $this->bindings[$ph.'1'] = $v2;
                    return "$column $operator {$ph}0 and {$ph}1";
                } else {
                    $this->bindings[$ph] = $value;
                    return "$column $operator $ph";
                }
            }
        }

        $first_operand = array_shift($where);
        if (count($where) % 2 != 0) throw new \UnexpectedValueException('Invalid number of elements in "where" argument');

        $sql = [$this->compileWhere($first_operand)];
        for ($i = 0; $i < count($where); $i += 2) {
            $operator = strtolower($where[$i]);
            $second_operand = $where[$i + 1];
            if (!in_array($operator, $this->clauseOperators)) throw new \UnexpectedValueException('Unknown operator: '.$operator);

            $sql[] = $operator.' '.$this->compileWhere($second_operand);
        }
        return '('.implode(' ', $sql).')';
    }

    protected function compileGroups($groups)
    {
        return 'group by '.$this->columnize($groups);
    }

    protected function compileHavings($havings)
    {
        $sql = [];
        foreach ($havings as $having) {
            $sql[] = $this->compileGeneralWhere($having);
        }
        return 'having '.implode(' and ', $sql);
    }

    protected function compileOrders($orders)
    {
        $sql = [];
        foreach ($orders as $order) {
            $sql[] = $order['column'].' '.$order['direction'];
        }
        return 'order by '.implode(', ', $sql);
    }

    protected function compileLimit($limit)
    {
        return 'limit '.(int)$limit;
    }

    protected function compileOffset($offset)
    {
        return 'offset '.(int)$offset;
    }

    public function compileInsert(array $values)
    {
        $table = $this->from;

        if (!is_array(reset($values))) {
            $values = [$values];
        }

        $columns = $this->columnize(array_keys(reset($values)));
        $parameters = $this->parameterize(reset($values));

        $value = array_fill(0, count($values), "($parameters)");
        $parameters = implode(', ', $value);

        return "insert into $table ($columns) values $parameters";
    }

    public function compileUpdate(array $values)
    {
        $this->resetBindings();
        $table = $this->from;

        $columns = [];
        foreach ($values as $k => $v) {
            $ph = $this->placeholder('update');

            $this->bindings[$ph] = $v;
            $columns[] = $k.' = '.$ph;
        }
        $columns = implode(', ', $columns);

        $where = $this->compileWheres($this->wheres);

        return trim("update $table set $columns $where");
    }

    public function compileDelete()
    {
        $this->resetBindings();
        $table = $this->from;
        $where = $this->compileWheres($this->wheres);
        return trim("delete from $table $where");
    }

    protected function columnize(array $columns)
    {
        return implode(', ', $columns);
    }

    protected function parameterize(array $values)
    {
        return implode(', ', array_map([$this, 'parameter'], $values));
    }

    protected function parameter($value)
    {
        return '?';
    }

    protected function concatenate(array $segments)
    {
        return implode(' ', array_filter($segments, function ($value) {
            return (string)$value !== '';
        }));
    }

    protected function placeholder($prefix)
    {
        return ':_'.$prefix.($this->placeholderCounter++);
    }

    protected function resetBindings()
    {
        $this->bindings = $this->manualBindings;
        $this->placeholderCounter = 0;
    }
}
