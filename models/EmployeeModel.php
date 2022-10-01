<?php

namespace Models;

use PDOException;

class EmployeeModel extends Model
{
    public function __construct()
    {
        parent::__construct("employee");
    }

    public function update(array $updateValues, array $conditions): bool
    {
        if (
            array_key_exists('id', $conditions) &&
            array_key_exists('userId', $conditions)
        ) {
            $updateConditions = [
                'id' => $conditions['id'],
                'userId' => $conditions['userId']
            ];

            try {
                return parent::update($updateValues, $updateConditions);
            } catch (PDOException $e) {
                throw $e;
            }
        }
        return false;
    }
}