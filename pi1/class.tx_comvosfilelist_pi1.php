<?php

use Doctrine\DBAL\DriverManager;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2012 comvos online medien GmbH, Nabil Saleh <saleh@comvos.de>
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
 * Plugin 'Extended Filelist' for the 'comvosfilelist' extension.
 *
 * @author	comvos online medien GmbH, Nabil Saleh <saleh@comvos.de>
 * @package	TYPO3
 * @subpackage	tx_comvosfilelist
 */
class tx_comvosfilelist_pi1 extends tslib_pibase {

    public $prefixId = 'tx_comvosfilelist_pi1';  // Same as class name
    public $scriptRelPath = 'pi1/class.tx_comvosfilelist_pi1.php'; // Path to this script relative to the extension dir.
    public $extKey = 'comvosfilelist'; // The extension key.
    public $pi_checkCHash = TRUE;

    /**
     *
     * @var Comvos_TYPO3_Filelist_EncryptionTool
     */
    protected $encryptionTool = null;

    /**
     * @var Twig_Environment
     */
    public $twig = null;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    public $connection = null;

    /**
     * The main method of the Plugin.
     *
     * @param string $content The Plugin content
     * @param array $conf The Plugin configuration
     * @return string The content that is displayed on the website
     */
    public function main($content, array $conf) {

        $this->initConf($conf);

        $content = '';

        $action = $this->piVars['action'];

        switch ($action) {
            case 'stream':
                $this->streamFile($this->piVars['file']);
                break;
            case 'show':
                return $this->singleView($this->piVars['file']);
                break;
            case 'check':
                if ($GLOBALS['TSFE']->beUserLogin) {
                    return $this->checkFileList();
                } else {
                    return $this->listView();
                }
                break;
            default:
                return $this->listView();
                break;
        }
    }

    /**
     * 
     * 
     * 
     * 
     */
    protected function checkFileList() {
        $this->conf['entriesPerPage'] = 100000;
        $fileListInfo = $this->getFileList();
        $fileList = array();
        foreach ($fileListInfo['files'] as $file) {
            $fileList [] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $file->getRelativePathname();
        }
        header('Content-type: application/json');
        echo json_encode($fileList);
        exit;
    }

    protected function decodeFileName($filename) {
        return $this->getEncryptionTool()->decrypt($filename);
    }

    protected function streamFile($filename) {
        $filelist = $this->getFileList();

        $filename = $this->decodeFileName($filename);

        $file = $filelist->getFileForSingleview($filename);

        if (!empty($this->conf['checkFileProtection'])) {
            $reachabillityTest = get_headers('http://' . $_SERVER['HTTP_HOST'] . '/' . $filename);
            if (strstr($reachabillityTest[0], '200')) {
                error_log('comvosfilelist: "http://' . $_SERVER['HTTP_HOST'] . '/' . $filename . '" is reachable but should not be.');
            }
        }

        $filenameForStreaming = $file->getFilename();

        //when using DAM check for dam download filename
        if (isset($this->conf['useDAM']) && $file->meta && $file->meta['file_dl_name']) {
            $filenameForStreaming = $file->meta['file_dl_name'];
        }

        if (strpos($filename, '/typo3temp/comvosfilelist/') === 0) {
            header('Content-type: image/jpeg');
        } else {
            header("Content-disposition: attachment; filename=" . $filenameForStreaming);
            header('Content-type: application/octet-stream'); //Force browser to download
        }

        //prevent browser from caching as far as possible
        header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0, proxy-revalidate, no-transform");
        header("Pragma: no-cache");

        readfile($file->getRealPath());

        exit;
    }

    /**
     * Single view throws Exception on access forbidden
     * @param string $filename
     * @return string
     * @throws Exception
     */
    public function singleView($filename) {
        $filelist = $this->getFileList();

        $filename = $this->decodeFileName($filename);

        $file = $filelist->getFileForSingleview($filename);


        return $this->twig->render('single.html.twig', array(
                    'file' => $file,
                    'pageid' => $GLOBALS['TSFE']->id));
    }

    /**
     * List view action
     * @return string
     */
    public function listView() {
        $content = '';
        try {
            $content .= $this->twig->render('list.html.twig', array(
                'pageid' => $GLOBALS['TSFE']->id,
                'filelist' => $this->getFileList()
                    )
            );
        } catch (Comvos_TYPO3_Filelist_FilelistException $e) {
            $content .= '<b>' . $e->getMessage() . '</b>';
        }
        return $content;
    }

    protected function getFileList() {

        if ($this->conf['useDAM']) {
            return $this->getFileListFromDam();
        } else {
            return $this->getFileListFromDirectory();
        }
    }

    protected function getFileListFromDirectory() {
        return new Comvos_TYPO3_Filelist_FilesystemFilelist(array_merge($this->conf, array('page' => (int) $this->piVars['page'], 'fileAccessValidator' => new Comvos_TYPO3_Filelist_AccessValidator_FilesystemAccessValidator(array($this->conf['directory'], 'typo3temp/comvosfilelist/' . $GLOBALS['TSFE']->id)))));
    }

    protected function getFileListFromDam() {

        return new Comvos_TYPO3_Filelist_DAMFilelist(array_merge($this->conf, array('page' => (int) $this->piVars['page'], 'fileAccessValidator' => new Comvos_TYPO3_Filelist_AccessValidator_DAMAccessValidator(array('fileadmin', 'typo3temp/comvosfilelist/' . $GLOBALS['TSFE']->id)))), $this->connection);
    }

    /**
     * check wether file is within allowed paths
     * 
     * @param Symfony\Component\Finder\SplFileInfo $file
     * @return boolean
     */
    protected function validatePath(Symfony\Component\Finder\SplFileInfo $file) {
        $filename = $file->getRelativePathname();
        if (substr($filename, 0, 1) == '/') {
            $filename = substr($filename, 1);
        }

        $isThumb = false;
        if (strpos($filename, 'typo3temp/comvosfilelist/') === 0) {
            $pid = (int) preg_replace('/(typo3temp\/comvosfilelist\/)([0-9]+)(\/.*)/', '$2', $filename);
            if ($pid == $GLOBALS['TSFE']->id) {
                $isThumb = true;
            }
        }
        return (t3lib_div::verifyFilenameAgainstDenyPattern($file->getRealPath())
                && t3lib_div::isAllowedAbsPath($file->getRealPath())
                && ($isThumb || !$this->conf['directory']) || t3lib_div::isFirstPartOfStr($filename, $this->conf['directory'])
                && $file->isFile()
                && $file->isReadable()
                );
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

    /**
     * - Initialize configuration with defaults, typoscriptconf and flexform 
     * - setup autoloading and doctrine and twig
     * @param mixed $typoScriptConf
     */
    protected function initConf($typoScriptConf) {
        $configurationDefaults = array(
            'singlePageId' => $GLOBALS['TSFE']->id,
            'listPageId' => $GLOBALS['TSFE']->id,
            'entriesPerPage' => 30,
            'useDAM' => true,
            'directory' => '',
            'template' => 'default',
            'cacheTwig' => false
        );

        $this->conf = array_merge($configurationDefaults, $typoScriptConf);
        $this->pi_initPIflexform();
        $ffConf = self::getFFSheetvalues($this->cObj->data['pi_flexform'], 'sDEF');


        // plugin is mm_dam_filelist replacement, try to merge config as far as possible
        $mmDamFilelistSheets = array('sMAIN', 'sCATEGORIES', 'sLISTVIEW', 'sTREEVIEW', 'sSINGLEVIEW', 'sSINGLEVIEW', 'sADDRESS', 'sVIDEO');

        foreach ($mmDamFilelistSheets as $sheetTitle) {
            $ffConfMMDamFilelist[$sheetTitle] = self::getFFSheetvalues($this->cObj->data['pi_flexform'], $sheetTitle);
        }
        $this->conf['isDamFilelistCE'] = false;

        if (count($ffConfMMDamFilelist['sCATEGORIES'])) {

            $this->conf['isDamFilelistCE'] = true;

            $this->conf['mm_dam_filelist'] = $ffConfMMDamFilelist;

            $ffConf['category'] = $ffConfMMDamFilelist['sCATEGORIES']['category'];
            $ffConf['entriesPerPage'] = $ffConfMMDamFilelist['sLISTVIEW']['results_at_a_time'];
            $ffConf['template'] = 'default';
            if ($ffConfMMDamFilelist['sLISTVIEW']['templatefile']) {
                //generate templateIdentifier to allow replacement of old templates
                //all chars, that are not letters or numbers are converted to "_" "mytemplate.html" => "mytemplate_html"
                //check "uploads/tx_mmdamfilelist" for old templates!
                $templateIdentifier = preg_replace('/[^a-zA-Z0-9]/', '_', $ffConfMMDamFilelist['sLISTVIEW']['templatefile']);
                $ffConf['template'] = $templateIdentifier;
            }
        }

        //overwrite TS-conf with FF values
        if ($ffConf['entriesPerPage']) {
            $this->conf['entriesPerPage'] = $ffConf['entriesPerPage'];
        }
        if ($ffConf['directory']) {
            $this->conf['directory'] = $ffConf['directory'];
        }
        if ($ffConf['category']) {
            $this->conf['category'] = $ffConf['category'];
        }
        if ($ffConf['useDAM']) {
            if ($ffConf['useDAM'] == 'true') {
                $this->conf['useDAM'] = true;
            } else {
                $this->conf['useDAM'] = false;
            }
        }

        $this->conf['template'] = $ffConf['template'];


        $this->pi_setPiVarDefaults();

        //Load Lang
        $this->pi_loadLL();

        $extconf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['comvosfilelist']);

        if (!$extconf['enryptionKey']) {
            throw new Exception('comvosfilelist: You MUST set an enryptionKey in extension configuration (extensionmanager).');
        }

        $this->encryptionTool = new Comvos_TYPO3_Filelist_EncryptionTool($extconf['enryptionKey']);

        //init doctrine DBAL
        $config = new \Doctrine\DBAL\Configuration();

        $connectionParams = array(
            'driver' => 'pdo_mysql',
            'wrapperClass' => 'Comvos\TYPO3\Doctrine\DBAL\Connection'
        );
        $this->connection = DriverManager::getConnection($connectionParams, $config);


        //init twig
        $templateFolder = t3lib_extMgm::extPath('comvosfilelist') . 'templates/secure';
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
        $cachefolder = PATH_site . 'typo3temp/comvosfilelist/twigcache';
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

        $this->twig->addExtension(new Comvos_TYPO3_Filelist_Twig_Extension($this));
    }

    public function getEncryptionTool() {
        return $this->encryptionTool;
    }

}

if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/comvosfilelist/pi1/class.tx_comvosfilelist_pi1.php'])) {
    include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/comvosfilelist/pi1/class.tx_comvosfilelist_pi1.php']);
}
?>