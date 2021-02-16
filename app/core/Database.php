<?php

namespace Core;

use Exception;
use PDO;

class Database
{
    private $connection = null;
    private $statement = null;

    /**
     * loads necessary classes
     */
    public function __construct()
    {
        $this->connection = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Prepare a DB query
     * 
     * @param string $query
     * 
     * @return object $this
     */
    public function query($query)
    {
        if (!$query) {
            throw new Exception("Invalid Data Pattern!");
        }

        $this->statement = $this->connection->prepare($query);

        return $this;
    }

    /**
     * Bind parameters for a prepared statement
     * 
     * @param array $params
     * 
     * @return object $this
     */
    public function bindParams(array $params)
    {
        foreach ($params as $key => $param) {
            $this->statement->bindValue($key, $param);
        }

        return $this;
    }

    /**
     * Executes a prepared statement
     *
     * @return mixed
     */
    public function execute()
    {
        $this->statement->execute();
    }

    /**
     * Get all data for a prepared statement
     *
     * @return object
     */
    public function findAll()
    {
        $this->execute();
        return $this->statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get single data for a prepared statement
     *
     * @return object
     */
    public function find()
    {
        $this->execute();
        return $this->statement->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Fetch last inserted id
     *
     * @return int
     */
    public function getLastInsertedId()
    {
        return $this->connection->lastInsertId();
    }
}
