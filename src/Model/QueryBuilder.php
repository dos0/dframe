<?php

namespace Dos0\Framework\Model;

/**
 * Class QueryBuilder
 * @package Dos0\Framework\Model
 */
abstract class QueryBuilder
{
    /**
     * SQL mode
     *
     * @var string
     */
    protected $mode = 'select';

    /**
     * Array of columns or empty for "*"
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Model table name
     *
     * @var string
     */
    protected $table = '';

    /**
     * Array of string conditions
     *
     * @var string[]
     */
    protected $conditions = [];

    /**
     * Array of ordering conditions
     *
     * @var array
     */
    protected $ordering = [];

    /**
     * Limit query param
     *
     * @var int
     */
    protected $limit = 0;

    /**
     * Offset query param
     *
     * @var int
     */
    protected $offset = 0;

    // protected $join = false;



    // @todo Add mode: update, insert, join

    /**
     * Sets 'select' mode and columns
     *
     * @param array $columns
     * @return QueryBuilder
     */
    public function select($columns = []): self
    {
        $this->mode = 'select';
        $this->columns = $columns;

        return $this;
    }

    /**
     * Sets 'delete' mode
     *
     * @return QueryBuilder
     */
    public function delete(): self
    {
        $this->mode = 'delete';

        return $this;
    }

    /**
     * Sets query table
     *
     * @param $table
     * @return QueryBuilder
     */
    public function from($table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Sets where conditions array
     *
     * @param string[] $where
     * @return QueryBuilder
     */
    public function where($where): self
    {
        $this->conditions[] = $where;

        return $this;
    }

    /**
     * Sets 'order by' query
     *
     * @param $column
     * @param string $direction
     * @return QueryBuilder
     */
    public function orderBy($column, $direction = 'asc'): self
    {
        $this->ordering[$column] = $direction;

        return $this;
    }

    /**
     * Set 'limit' query
     *
     * @param int $limit
     * @param int $offset
     * @return QueryBuilder
     */
    public function limit($limit = 0, $offset = 0): self
    {
        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    /**
     * Builds sql-query string from prepared params
     *
     * @return string
     */
    protected function build(): string
    {
        switch ($this->mode) {
            case 'delete':
                $sql = 'DELETE FROM ' . $this->table .
                    $this->buildWhere();
                break;

            case 'select':
            default:
                $sql = 'SELECT ' . $this->buildColumns() .
                    ' FROM ' . $this->table .
                    $this->buildWhere() .
                    $this->buildOrder() .
                    $this->buildLimit();
        }

        return $sql;
    }


    /**
     * Builds substring column query
     *
     * @return string
     */
    protected function buildColumns(): string
    {
        if (!empty($this->columns)) {
            $sql = implode(', ', $this->columns);
        } else {
            $sql = '*';
        }

        return $sql;
    }


    /**
     * Builds substring wrere query
     *
     * @return string
     */
    protected function buildWhere(): string
    {
        $sql = '';
        if (!empty($this->conditions)) {
            $sql = ' WHERE ' . implode(' AND ', $this->conditions);
        }

        return $sql;
    }

    /**
     * Builds substring order query
     *
     * @return string
     */
    protected function buildOrder(): string
    {
        $sql = '';
        if (!empty($this->ordering)) {
            $buffer = [];
            foreach ($this->ordering as $key => $value) {
                $buffer[] = $key . ' ' . strtoupper($value);
            }
            $sql = ' ORDER BY ' . implode(', ', $buffer);
        }

        return $sql;
    }

    /**
     * Builds substring limit query
     *
     * @return string
     */
    protected function buildLimit(): string
    {
        $sql = '';
        if ($this->limit) {
            $buffer = $this->offset ? (int)$this->offset . ',' : '';
            $sql = ' LIMIT ' . $buffer . (int)$this->limit;
        }

        return $sql;
    }
}