<?php

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

use Symfony\Component\Finder\Finder;

/**
 *
 * @author nsaleh
 */
class Comvos_TYPO3_Filelist_SecurefolderReport implements tx_reports_StatusProvider {

    /**
     *
     * @var LanguageService 
     */
    protected $translationHandler = null;

    /**
     * Constructor for class tx_myext_report_MyStatus
     */
    public function __construct() {
        $this->translationHandler = $GLOBALS['LANG'];
        $this->translationHandler->includeLLFile('EXT:comvosfilelist/src/Comvos/TYPO3/Filelist/Report/locallang.xml');
    }

    /**
     * do custom check
     *
     * @see typo3/sysext/reports/interfaces/tx_reports_StatusProvider::getStatus()
     * @return array
     */
    public function getStatus() {

        return $this->validatePathsAndGetStatus();
    }

    protected function validatePathsAndGetStatus() {
        $extconf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['comvosfilelist']);
        $folders = array();
        if (!empty($extconf['observedProtectedFolders'])) {
            $folders = explode(',', $extconf['observedProtectedFolders']);
        }

        $reports = array();

        foreach ($folders as $folder) {
            $finder = new Finder();
            $finder->in(PATH_site . $folder)->files();
            $filetotest = null;
            $fileindex = rand(1, $finder->count() ? $finder->count() : 1);
            $nr = 0;
            foreach ($finder as $file) {
                $nr++;
                if ($nr == $fileindex) {
                    $filetotest = $file;
                    break;
                }
            }
            $dummyfile = '';
            if ($filetotest === null) {
                $dummyfile = PATH_site . $folder . '/comvosfilelist-testfile.jpg';
                file_put_contents($dummyfile, 'test');
                $filetotest = new Symfony\Component\Finder\SplFileInfo($dummyfile, '', 'comvosfilelist-testfile.jpg');
            }
            $reachabillityTest = get_headers('http://' . $extconf['filehostname'] . '/' . $folder . '/' . $filetotest->getRelativePathname());
            if (!strstr($reachabillityTest[0], '403')) {
                $status = t3lib_div::makeInstance('tx_reports_reports_status_Status', $this->translationHandler->getLL('securefoldercheck', 'Securefoldercheck'), '"' . $folder . '" ' . $this->translationHandler->getLL('isinsecure'), 'http://' . $extconf['filehostname'] . '/' . $folder . '/' . $filetotest->getRelativePathname() . ' ' . $this->translationHandler->getLL('isreachable') . ' (' . $reachabillityTest[0] . ')', tx_reports_reports_status_Status::ERROR);
            } else {
                $status = t3lib_div::makeInstance('tx_reports_reports_status_Status', $this->translationHandler->getLL('securefoldercheck', 'Securefoldercheck'), '"' . $folder . '" ' . $this->translationHandler->getLL('insecure'), 'http://' . $extconf['filehostname'] . '/' . $folder . '/' . $filetotest->getRelativePathname() . ' ' . $this->translationHandler->getLL('isnotreachable') . ' (' . $reachabillityTest[0] . ')', tx_reports_reports_status_Status::OK);
            }
            if ($dummyfile) {
                unlink($dummyfile);
            }




            $reports[] = $status;
        }

        $folder = 'fileadmin';
        $dummyfile = PATH_site . $folder . '/comvosfilelist-testfile.jpg';
        file_put_contents($dummyfile, 'test');
        $filetotest = new Symfony\Component\Finder\SplFileInfo($dummyfile, '', 'comvosfilelist-testfile.jpg');

        $reachabillityTest = get_headers('http://' . $extconf['filehostname'] . '/' . $folder . '/' . $filetotest->getRelativePathname());
        if (!strstr($reachabillityTest[0], '200')) {
            $status = t3lib_div::makeInstance('tx_reports_reports_status_Status', $this->translationHandler->getLL('securefoldercheck', 'Securefoldercheck'), '"' . $folder . '" ' . $this->translationHandler->getLL('isnotreachable'), 'http://' . $extconf['filehostname'] . '/' . $folder . '/' . $filetotest->getRelativePathname() . ' ' . $this->translationHandler->getLL('isnotreachablebutshouldbe') . ' (' . $reachabillityTest[0] . ') <br/> ' . $this->translationHandler->getLL('checkconfiguration'), tx_reports_reports_status_Status::ERROR);
        } else {
            $status = t3lib_div::makeInstance('tx_reports_reports_status_Status', $this->translationHandler->getLL('securefoldercheck', 'Securefoldercheck'), '"' . $folder . '" ' . $this->translationHandler->getLL('isreachable') . '', 'http://' . $extconf['filehostname'] . '/' . $folder . '/' . $filetotest->getRelativePathname() . ' ' . $this->translationHandler->getLL('isreachable') . ' (' . $reachabillityTest[0] . ')', tx_reports_reports_status_Status::OK);
        }
        if ($dummyfile) {
            unlink($dummyfile);
        }

        $reports[] = $status;

        if (!count($reports)) {
            $reports[] = t3lib_div::makeInstance('tx_reports_reports_status_Status', 'Ordnersicherheitsprüfung', 'Mögliches Sichererheitsrisiko', 'No securefolders are configured.', tx_reports_reports_status_Status::INFO);
        }
        return $reports;
    }

}

?>
