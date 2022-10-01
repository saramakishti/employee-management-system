<?php

namespace Models;

use PDO;
use PDOException;

class Model extends Database
{
    protected string $tableName;
    protected array $fields;

    public function __construct(string $tableName)
    {
        parent::__construct();
        $this->tableName = $tableName;

        $statement = $this->db->prepare("DESCRIBE $this->tableName");
        try {
            $statement->execute();
        } catch (PDOException $e) {}
        $this->fields = $statement->fetchAll(PDO::FETCH_COLUMN);
        $statement->closeCursor();
    }

    public function fetchPaginatedBy(int $limit, int $offset, array $conditions): array
    {
        $fieldsConditions = [];
        $query = "SELECT * FROM $this->tableName WHERE";
        foreach ($conditions as $key=>$value) {
            if (in_array($key, $this->fields)) {
                $fieldsConditions[$key] = $value;
                $query .= " $key=:$key AND";
            }
        }
        $query = substr($query, 0, strlen($query) - 3);
        $query .= "LIMIT :limit OFFSET :offset";

        $statement = $this->db->prepare($query);
        foreach ($fieldsConditions as $key=>$value) {
            $statement->bindValue(":$key", $value);
        }
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);

        try {
            $statement->execute();
        } catch (PDOException $e) {
            throw $e;
        }

        $results = $statement->fetchAll();
        $statement->closeCursor();
        return $results;
    }

    public function fetchBy(array $conditions): array
    {
        $fieldsConditions = [];
        $query = "SELECT * FROM $this->tableName WHERE";
        foreach ($conditions as $key=>$value) {
            if (in_array($key, $this->fields)) {
                $fieldsConditions[$key] = $value;
                $query .= " $key=:$key AND";
            }
        }
        $query = substr($query, 0, strlen($query) - 4);

        $statement = $this->db->prepare($query);

        try {
            $statement->execute($fieldsConditions);
        } catch (PDOException $e) {
            throw $e;
        }

        $results =  $statement->fetchAll();
        $statement->closeCursor();
        return $results;
    }

    public function insert(array $insertValues) {
        $fieldsValues = [];
        $query = "INSERT INTO $this->tableName (";
        foreach ($insertValues as $key=>$value) {
            if (in_array($key, $this->fields)) {
                $fieldsValues[$key] = $value;
                $query .= "$key,";
            }
        }
        $query = substr($query, 0, strlen($query) - 1);
        $query .= " ) VALUES (";
        foreach ($fieldsValues as $key=>$value) {
            $query .= " :$key,";
        }
        $query = substr($query, 0, strlen($query) - 1);
        $query .= " )";

        $statement = $this->db->prepare($query);
        try {
            $statement->execute($fieldsValues);
        } catch (PDOException $e) {
            throw $e;
        }
        $statement->closeCursor();
        return $this->db->lastInsertId();
    }

    public function update(array $updateValues, array $conditions)
    {
        $fieldConditions = [];
        $query = "UPDATE $this->tableName SET";
        foreach ($updateValues as $key=>$value) {
            $query .=" `$key`=:$key,";
        }
        $query = substr($query, 0, strlen($query) - 1);
        $query .= " WHERE";
        foreach ($conditions as $key=>$value) {
            if (in_array($key, $this->fields)) {
                $fieldConditions[$key] = $value;
                $query .=" $key=:$key AND";
            }
        }
        $query = substr($query, 0, strlen($query) - 4);

        $statement = $this->db->prepare($query);
        foreach ($updateValues as $key=>$value) {
            $statement->bindValue(":$key", $value);
        }
        foreach ($fieldConditions as $key=>$value) {
            $statement->bindValue(":$key", $value);
        }

        try {
            $statement->execute();
        } catch (PDOException $e) {
            throw $e;
        }
        $statement->closeCursor();
        return true;
    }

    public function delete(array $conditions) {
        $fieldsConditions = [];
        $query = "DELETE FROM $this->tableName WHERE";
        foreach ($conditions as $key=>$value) {
            if (in_array($key, $this->fields)) {
                $fieldsConditions[$key] = $value;
                $query .= " $key=:$key AND";
            }
        }
        $query = substr($query, 0, strlen($query) - 4);

        $statement = $this->db->prepare($query);
        try {
            $statement->execute($fieldsConditions);
        } catch (PDOException $e) {
            throw $e;
        }
        $statement->closeCursor();
        return true;
    }
}