<?php
namespace Framework\Database;

class Query
{
    private $select = null;

    private $from;

    private $where;

    private $group;

    private $order;

    private $limit;

    private $pdo;

    private $params;

    /**
     * Query constructor.
     * @param $pdo
     */
    public function __construct(?\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function from(string $table, ?string $alias = null): self
    {
        if ($alias) {
            $this->from[$alias] = $table;
        } else {
            $this->from[] = $table;
        }
        return $this;
    }

    /**
     * @param string ...$fields
     * @return $this
     */
    public function select(string ...$fields): self
    {
        $this->select = $fields;
        return $this;
    }

    public function where(string ...$condition): self
    {
        $this->where = $condition;
        return $this;
    }

    public function count(): int
    {
        $this->select("COUNT(id)");
        return $this->execute()->fetchColumn();
    }

    private function buildFrom(): string
    {
        $from = [];
        foreach ($this->from as $key => $value) {
            if (is_string($key)) {
                $from[] = "$value as $key";
            } else {
                $from[] = $value;
            }
        }
        return join(', ', $from);
    }

    public function params(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    public function __toString()
    {
        $parts[] = 'SELECT';
        if ($this->select) {
            $parts[] = join(', ', $this->select);
        } else {
            $parts[] = '*';
        }
        $parts[] = 'FROM';
        $parts[] = $this->buildFrom();
        if (!empty($this->where)) {
            $parts[] = "WHERE";
            $parts[] = "(" . join(') AND (', $this->where) . ')';
        }
        return join(' ', $parts);
    }

    private function execute()
    {
        $query = $this->__toString();
        if ($this->params) {
            $statement = $this->pdo->prepare($query);
            $statement->execute($this->params);
            return $statement;
        }
        return $this->pdo->query($query);
    }
}
