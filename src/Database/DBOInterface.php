<?php

namespace Dos0\Framework\Database;

/**
 * Interface DBOInterface
 * @package Dos0\Framework\Database
 */
interface DBOInterface
{
    /**
     * Tries to db connect
     */
    public function connect();

    /**
     * Gets PDO connection
     *
     * @return \PDO
     */
    public function getConnection();
}