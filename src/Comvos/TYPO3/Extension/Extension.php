<?php

use Doctrine\DBAL\DriverManager;
use Symfony\Component\HttpFoundation\Request;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2013 comvos online medien GmbH, Nabil Saleh <saleh@comvos.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 * Description of Comvos_TYPO3_Extension
 *
 * @author nsaleh
 */
class Comvos_TYPO3_Extension_Extension extends tslib_pibase {

    /**
     * @var Twig_Environment
     */
    protected $twig = null;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection = null;

    /**
     * @var 
     */
    protected $request = null;
    /**
     * - Initialize configuration
     * - setup doctrine and twig
     * @param mixed $typoScriptConf
     */
    protected function initConf($typoScriptConf, $configurationDefaults = array()) {
        
        $this->request = Request::createFromGlobals();
        
        $this->extconf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);

        $this->conf = array_merge($configurationDefaults, $typoScriptConf);
        
        $this->pi_setPiVarDefaults();
        //Load Lang
        $this->pi_loadLL();

        $this->pi_initPIflexform();

        $this->ffConf = self::getFFSheetvalues($this->cObj->data['pi_flexform'], 'sDEF');
        
    }

    protected function initObjects() {

        //init doctrine DBAL
        $config = new \Doctrine\DBAL\Configuration();

        $connectionParams = array(
            'driver' => 'pdo_mysql',
            'wrapperClass' => 'Comvos\TYPO3\Doctrine\DBAL\Connection'
        );
        $this->connection = DriverManager::getConnection($connectionParams, $config);
        
        //init twig
        $templateFolder = '';
        if (isset($this->conf['templateFolders.'][$this->conf['template']])) {
            $temporaryTemplateFolder = t3lib_div::getFileAbsFileName($this->conf['templateFolders.'][$this->conf['template']]);
            if (file_exists($temporaryTemplateFolder)) {
                $templateFolder = $temporaryTemplateFolder;
            }
        } else {
            if ($this->conf['template']) {
                throw new Exception('Template not configured in TS "templateFolders": "' . $this->conf['template'] . '"');
            }
        }

        //init view
        $cachefolder = PATH_site . 'typo3temp/' . $this->extKey . '/twigcache';
        if (!file_exists($cachefolder)) {
            mkdir($cachefolder, $TYPO3_CONF_VARS['BE']['folderCreateMask'] ? $TYPO3_CONF_VARS['BE']['folderCreateMask'] : 0775);
        }
        
        $loader = new Twig_Loader_Filesystem($templateFolder);
        $this->twig = new Twig_Environment($loader, array(
                    'cache' => $this->conf['cacheTwig'] ? $cachefolder : false,
                ));
        $this->twig->addGlobal('conf', $this->conf);
        $this->twig->addGlobal('tsfe', $GLOBALS['TSFE']);

        $this->twig->addExtension(new Comvos_TYPO3_Twig_Extension($this));
    }

    static protected function getFFSheetvalues($flex, $sheetTitle) {
        if (!isset($flex['data'][$sheetTitle])) {
            return array();
        }
        $sheetData = array();
        foreach ($flex['data'][$sheetTitle] as $sheetField) {
            foreach ($sheetField as $fname => $fvalue) {
                $sheetData[$fname] = $fvalue['vDEF'];
            }
        }
        return $sheetData;
    }
    public function getTwig() {
        return $this->twig;
    }

    public function setTwig($twig) {
        $this->twig = $twig;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function setConnection($connection) {
        $this->connection = $connection;
    }

    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) {
        $this->request = $request;
    }


}

?>
