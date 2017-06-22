<?php

namespace Dos0\Framework\Database;

/**
 * Class Database
 * @package Dos0\Framework\Database
 */
class Database implements DBOInterface
{
    /**
     * @var \PDO
     */
    protected $connection;

    /**
     * PDO driver
     *
     * @var string
     */
    protected $driver;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * Database name
     *
     * @var string
     */
    protected $name;

    /**
     * Database user
     *
     * @var string
     */
    protected $user;

    /**
     * Database password
     *
     * @var string
     */
    protected $pass;

    /**
     * Database constructor.
     * @param string $driver
     * @param string $host
     * @param int $port
     * @param string $name
     * @param string $user
     * @param string $pass
     */
    public function __construct(
        string $driver = 'mysql',
        string $host = 'localhost',
        int $port = 3306,
        string $name = '',
        string $user = '',
        string $pass = '')
    {
        $this->driver = $driver;
        $this->host = $host;
        $this->port = (int)$port;
        $this->name = $name;
        $this->user = $user;
        $this->pass = $pass;

        $this->connect();
    }

    /**
     * @inheritdoc
     */
    public function connect()
    {
        $dsn = sprintf('%s:host=%s;port=%s;dbname=%s',
            $this->driver,
            $this->host,
            $this->port,
            $this->name);

        try {
            $this->connection = new \PDO($dsn, $this->user, $this->pass);
        } catch (\PDOException $e) {
           throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function getConnection(): \PDO
    {
        return $this->connection;
    }

}