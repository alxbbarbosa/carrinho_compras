<?php

namespace Abbarbosa\Gadgets\CarrinhoCompras\Classes;

use Abbarbosa\Gadgets\CarrinhoCompras\Contracts\iModel;

abstract class Model implements iModel
{
    protected $table;
    protected $table_id = 'id';
    protected $data = [];
    protected static $connection;

    public function __construct($data = [])
    {
        if (count($data) > 0) {
            $this->fromArray($data);
        }
    }

    public static function setConnection(\PDO $connection)
    {
        self::$connection = $connection;
    }
    

    public function _create(array $array = []): ?iModel
    {
        if (count($array) > 0) {
            return $this->fromArray($array)->save();
        }
    }

    public function _all(array $criteria = []): array
    {
        $criteria = $this->dumpCriteria($criteria);
        $sql = "SELECT * FROM {$this->table}";
        if (isset($criteria->values)) {
            $sql .= ' WHERE ' . $criteria->fields;
        }
        $sql .= ';';
        $stmt = self::$connection->prepare($sql);
        if (isset($criteria->values)) {
            $result = $stmt->execute((array) $criteria->values);
        } else {
            $result = $stmt->execute();
        }
        if ($result) {
            return $stmt->fetchAll(\PDO::FETCH_CLASS, get_class($this));
        }
        return [];
    }

    public function _findFirst(array $criteria = []): array
    {
        $criteria = $this->dumpCriteria($criteria);
        $sql = "SELECT * FROM {$this->table}";
        if (isset($criteria->values)) {
            $sql .= ' WHERE ' . $criteria->fields;
        }
        $sql .= ' LIMIT 1;';
        $stmt = self::$connection->prepare($sql);
        if (isset($criteria->values)) {
            $result = $stmt->execute((array) $criteria->values);
        } else {
            $result = $stmt->execute();
        }
        if ($result) {
            return $stmt->fetchAll(\PDO::FETCH_CLASS, get_class($this));
        }
        return [];
    }

    protected function dumpCriteria(array $criteria): \stdClass
    {
        if (count($criteria) == 2 && !is_array($criteria[0]) && !is_array($criteria[1])) {
            $base = $criteria[0] . ' = :' . $criteria[0];
            $value = $criteria[1];
        } elseif (count($criteria) == 3 && !is_array($criteria[0]) && !is_array($criteria[2])) {
            $base = $criteria[0] . ' ' . $criteria[1] . ' :' . $criteria[0];
            $value = $criteria[2];
        } elseif (count($criteria) == 2 && !is_array($criteria[0]) && is_array($criteria[2])) {
            $subquery = $this->dumpCriteria($criteria2[1]);
            $base = $criteria[0] .' in (' . $subquery->fields . ')';
            $value = $subquery->value;
        } elseif (count($criteria) == 3 && !is_array($criteria[0]) && is_array($criteria[2])) {
            $subquery = $this->dumpCriteria($criteria2[2]);
            $base = $criteria[0] . ' ' . $criteria[1] . ' (' . $subquery->fields . ')';
            $value = $subquery->value;
        } elseif (count($criteria) > 1 && is_array($criteria[0])) {
            $queries = [];
            $value = [];
            foreach ($criteria as $key => $element) {
                $query = $this->dumpCriteria($element);
                $subquery[] = $query->fields;
                $value[] = $query->values;
            }
            $base = implode(' AND ', array_filter($subquery));
        }

        if (isset($base) && isset($value)) {
            return (object) ['fields' => $base, 'values' => $value];
        }
        return new \stdClass;
    }

    public function _find(int $id): ?iModel
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->table_id} = ?;";
        $stmt = self::$connection->prepare($sql);

        if ($stmt->execute([$id])) {
            $result = $stmt->fetchObject(get_class($this));
            return $result ? $result : null;
        }
        return null;
    }

    public static function __callStatic(string $method, $arguments = null)
    {
        $obj = new static;
        switch (strtolower($method)) {
            case 'all':
            return call_user_func_array(array($obj, '_all'), $arguments);
            case 'findfirst':
            return call_user_func_array(array($obj, '_findFirst'), $arguments);
            case 'find':
            return call_user_func_array(array($obj, '_find'), $arguments);
            case 'create':
            return call_user_func_array(array($obj, '_create'), $arguments);
            case 'destroy':
            case 'delete':
            $pre = call_user_func_array(array($obj, '_find'), $arguments);
            return $pre->delete();
            case 'truncate':
            return call_user_func_array(array($obj, '_truncate'), $arguments);
        }
    }

    public function __call($method, $arguments = null)
    {
        switch (strtolower($method)) {
            case 'all':
            return $this->_all($arguments);
            case 'find':
            return $this->_find($arguments[0]);
            case 'findfirst':
            return $this->_findFirst($arguments);
            case 'create':
            return $this->_create($arguments);
            case 'destroy':
            return $pre->delete();
            case 'truncate':
            return $this->_truncate();
        }
    }

    public function save(): ?iModel
    {
        if (isset($this->data[$this->table_id])) {
            $sql = "UPDATE {$this->table} SET ";
            $sql .= implode(', ', array_filter(array_map(function ($e) {
                if ($e === $this->table_id) {
                    return;
                }
                return "{$e} = :{$e}";
            }, array_keys($this->data))));
            $sql .= " WHERE {$this->table_id} = :{$this->table_id};";
        } else {
            $sql = "INSERT INTO {$this->table} (" . implode(', ', array_keys($this->data));
            $sql .= ') VALUES (:' . implode(', :', array_keys($this->data)) . ');';
        }
        if (!empty(self::$connection)) {
            $stmt = self::$connection->prepare($sql);
            $result = $stmt->execute($this->data);
            if ($result) {
                if (!isset($this->data[$this->table_id])) {
                    $this->data[$this->table_id] = self::$connection->lastInsertId();
                }
                return $this;
            }
        }
    }

    public function delete(): bool
    {
        if (isset($this->data[$this->table_id]) && !is_null($this->data[$this->table_id])) {
            $sql = "DELETE FROM {$this->table} WHERE {$this->table_id} = ?;";
            $stmt = self::$connection->prepare($sql);
            $id = $this->data[$this->table_id];
            $stmt->bindParam(1, $id, \PDO::PARAM_INT);
            return $stmt->execute() ? true : false ;
        }
        return false;
    }

    public function _truncate()
    {
        $sql1 = "TRUNCATE public.{$this->table} RESTART IDENTITY CASCADE;";
        $stmt = self::$connection->prepare($sql1);
        if ($stmt->execute()) {
            $sql2 = "TRUNCATE TABLE {$this->table} CASCADE;";
            $stmt = self::$connection->prepare($sql2);

            return $stmt->execute() ? true : false;
        }
    }

    public function __isset($property): bool
    {
        return isset($this->data[$property]);
    }

    public function __get($property)
    {
        return $this->data[$property] ?? null;
    }

    public function __set($property, $value)
    {
        $this->data[$property] = $value;
    }

    public function fromArray(array $array): iModel
    {
        $this->data = $array;
        return $this;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
