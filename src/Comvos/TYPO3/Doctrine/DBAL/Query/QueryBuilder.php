<?php

namespace Comvos\TYPO3\Doctrine\DBAL\Query;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of QueryBuilder
 *
 * @author nsaleh
 */
class QueryBuilder extends \Doctrine\DBAL\Query\QueryBuilder {

    /**
     * flag wether to use enablefields constraints or not
     * @var boolean
     */
    protected $useEnableFields = true;

    public function getUseEnableFields() {
        return $this->useEnableFields;
    }

    public function setUseEnableFields($useEnableFields) {
        $this->useEnableFields = $useEnableFields;
    }

    /**
     * Add enablefield conditions to doctrine query depending on from clause
     * 
     */
    public function typo3EnableFields() {

        $cObj = \t3lib_div::makeInstance('tslib_cObj');
        $from = $this->getQueryPart('from');

        foreach ($from as $selectTable) {

            $andWhere = ' 1 ' . $cObj->enableFields($selectTable['table']);
            if (isset($selectTable['alias'])) {
                $andWhere = str_replace($selectTable['table'], $selectTable['alias'], $andWhere);
            }

            $this->andWhere($andWhere);
        }

        return $this;
    }

    /**
     * Execute this query using the bound parameters and their types.
     *
     * Uses {@see Connection::executeQuery} for select statements and {@see Connection::executeUpdate}
     * for insert, update and delete statements.
     *
     * @return mixed
     */
    public function execute() {
        if ($this->getUseEnableFields()) {
            $this->typo3EnableFields();
        }
        return parent::execute();
    }

}

?>
