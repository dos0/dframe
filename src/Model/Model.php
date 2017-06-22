<?php

namespace Dos0\Framework\Model;

use Dos0\Framework\DI\DIInjector;
use Dos0\Framework\Database\Database;
use Dos0\Framework\Model\Exception\ModelBadQueryException;

/**
 * Class Model
 * @package Dos0\Framework\Model
 */
abstract class Model extends QueryBuilder
{
    protected $table = '';

    /**
     * @var Database $dbo
     */
    protected $dbo;

    /**
     * Model constructor.
     */
    public function __construct(){
        $this->dbo = DIInjector::get('Database');
    }

    /**
     * Gets all models
     *
     * @return array|mixed
     */
    public function findAll()
    {
        $sql = $this
            ->select()
            ->build();

        return $this->executeQuery($sql, true, true);
    }

    /**
     * Gets model by PK
     *
     * @param int $id
     * @return array|mixed
     */
    public function findByPk(int $id)
    {
        $sql = $this
            ->select()
            ->where('id =' . $id)
            ->limit(1)
            ->build();

        return $this->executeQuery($sql, true);
    }

    /**
     * Executes the prepared query
     *
     * @param string $sql
     * @param bool $isReturn
     * @param bool $isAll
     * @return array|mixed
     */
    public function executeQuery(string $sql = '', bool $isReturn = false, bool $isAll = false)
    {
        try {
            $PDOStatement = $this->dbo->getConnection()->prepare($sql);
            $PDOStatement->execute();

            if ($PDOStatement->errorInfo()[0] != '00000') {
                throw new ModelBadQueryException(implode(': ', $PDOStatement->errorInfo()));
            }
        } catch (ModelBadQueryException $e) {
            echo $e->getMessage();
        }
        if ($isReturn) {
            return ($isAll)
                ? $PDOStatement->fetchAll(\PDO::FETCH_ASSOC)
                : $PDOStatement->fetch(\PDO::FETCH_ASSOC);
        }
    }
}