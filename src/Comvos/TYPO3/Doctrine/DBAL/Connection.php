<?php

namespace Comvos\TYPO3\Doctrine\DBAL;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TYPO3Connection
 *
 * @author nsaleh
 */
class Connection extends \Doctrine\DBAL\Connection {

    public function __construct(array $params, \Doctrine\DBAL\Driver $driver, \Doctrine\DBAL\Configuration $config = null, \Doctrine\Common\EventManager $eventManager = null) {
        if (!isset($params['dbname'])) {
            $params = array_merge($params, array(
                'dbname' => TYPO3_db,
                'user' => TYPO3_db_username,
                'password' => TYPO3_db_password,
                'host' => 'localhost',
                'driverOptions' => array(
                    1002 => 'SET NAMES utf8'
                    )));
        }

        parent::__construct($params, $driver, $config, $eventManager);
    }

    /**
     * Create a new instance of a SQL query builder.
     *
     * @return \Comvos\TYPO3\Doctrine\DBAL\Query\QueryBuilder
     */
    public function createQueryBuilder() {
        return new Query\QueryBuilder($this);
    }

}

?>
