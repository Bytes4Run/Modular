<?php

/**
 * Connection Class to consume the server local database
 * @description This class is the base class for all database connections
 * @author Jorge Echeverria <jecheverria@bytes4run.com> 
 * @category Class 
 * @package B4R\Kernel\classes\Connection
 * @version 1.7.0 
 * @date 2023-01-10 - 2024-03-11
 * @time 15:00:00
 * @copyright (c) 2024 Bytes4Run 
 */

declare(strict_types=1);

namespace B4R\Kernel\classes;

use PDO;
use PDOException;
use B4R\Kernel\helpers\Config;

 class Connection {
    /**
     * Connection to the database
     * @var PDO|array
     */
    private PDO|array $connection;
    /**
     * Error message
     * @var array
     */
    private ?array $error;
    /**
     * Server host
     * @var string
     */
    private string $host;
    /**
     * Database name
     * @var string
     */
    private string $database;
    /**
     * Database user
     * @var string
     */
    private string $user;
    /**
     * Database password
     * @var string
     */
    private string $password;
    /**
     * Database port
     * @var string
     */
    private string $port;
    /**
     * Database charset
     * @var string
     */
    private string $charset;
    /**
     * Function to get database response after connection
     * 
     * @param string $type Type of statement to execute
     * @param array $query Query to execute
     * @param string $dbName Database name
     * @return array
     */
    protected function getResponse (string $type, array $query,string $dbName) {
        return $this->getDbResponse($type, $query, $dbName);
    }
    /**
     * Function to stablish the connection to the database
     * @return PDO|bool
     */
    private function stablishConnection (): PDO|bool {
        $this->error = null;
        try {
            $this->connection = new PDO("mysql:host=$this->host;port=$this->port;dbname=$this->database;charset=$this->charset", $this->user, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->connection;
        } catch (PDOException $e) {
            $this->error = ['code'=>$e->getCode(),'message' => $e->getMessage()];
            return false;
        }
    }
    /**
     * Function to get application config variables
     * @param string|null $dbName Database name
     * @return bool
     */
    private function getConfig(?string $dbName = null): bool
    {
        $this->database = $dbName;
        if (is_null($dbName) || $dbName == "default") {
            $this->host = $_ENV['APP_DB_HOST'];
            $this->user = $_ENV['APP_DB_USER'];
            $this->port = $_ENV['APP_DB_PORT'];
            $this->charset = $_ENV['APP_DB_CHARSET'];
            $this->password = $_ENV['APP_DB_PASS'];
            $this->database = $_ENV['APP_DB_NAME'];
            return true;
        } else {
            $conf = new Config();
            $dbConfig = $conf->get($dbName, 'json');
            if ($dbConfig['type'] == "error") {
                $this->error = $dbConfig;
                return false;
            } else {
                $this->host = $dbConfig['dbhost'];
                $this->user = $dbConfig['dbuser'];
                $this->port = $dbConfig['dbport'];
                $this->charset = $dbConfig['dbcharset'];
                $this->password = $dbConfig['dbpassword'];
                return true;
            }
        }
    }
    /**
     * Function to get database response after connection
     * 
     * @param string $type Type of statement to execute
     * @param array $query Query to execute
     * @param string $dbName Database name
     * @return array|null
     */
    private function getDbResponse(string $type, array $query, string $dbName): array|null
    {
        $this->error = null;
        $id = null;
        $rows = null;
        $affected = null;
        if ($this->getConfig($dbName)) {
            if ($this->stablishConnection()) {
                try {
                    $stmt = $this->connection->prepare($query['string']);
                    $stmt->execute($query['params']);
                    if ($type == "insert") {
                        $id = $this->connection->lastInsertId();
                    }
                    if ($type == "update" || $type == "delete") {
                        $affected = $stmt->rowCount();
                    }
                    if ($type == "select") {
                        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                    return [
                        'status' => 'success',
                        'message' => 'Query executed successfully',
                        'data' => [
                            'id' => $id,
                            'rows' => $rows,
                            'affected' => $affected,
                        ],
                    ];
                } catch (PDOException $e) {
                    $this->error = ['code' => $e->getCode(), 'message' => $e->getMessage()];
                    return null;
                }
            } else {
                $this->error = ['code' => 500, 'message' => 'Error connecting to the database'];
                return null;
            }
        } else {
            $this->error = ['code' => 500, 'message' => 'Error getting database configuration'];
            return null;
        }
    }
}