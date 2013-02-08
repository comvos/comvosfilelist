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

/**
 * Description of FilesystemAcessValidator
 *
 * @author nsaleh
 */
class Comvos_TYPO3_Filelist_AccessValidator_FilesystemAccessValidator implements Comvos_TYPO3_Filelist_AccessValidator {

    /**
     * list of allowed folders.
     * @var array 
     */
    protected $allowedFolders = array();

    public function __construct($allowedFolders = array()) {
        $this->allowedFolders = $allowedFolders;
    }

    protected static function isFirstPartOfStr($str, $partStr) {
        return $partStr != '' && strpos((string) $str, (string) $partStr, 0) === 0;
    }

    /**
     * 
     * check wether file is within allowed paths
     * @dependsOfTypo3
     * @param Symfony\Component\Finder\SplFileInfo $file
     * @return boolean
     */
    public function validateFile(Symfony\Component\Finder\SplFileInfo $file) {
        $filename = $file->getRelativePathname();
        if (substr($filename, 0, 1) == '/') {
            $filename = substr($filename, 1);
        }

        if ((t3lib_div::verifyFilenameAgainstDenyPattern($file->getRealPath())
                && t3lib_div::isAllowedAbsPath($file->getRealPath())
                && $file->isFile()
                && $file->isReadable()
                && $this->isWithinAllowedFolders($filename)
        )) {
            return true;
        }
        return false;
    }

    /**
     * Check wether file is in one of the allowed folders.
     * 
     * @param string $filename Pathname relative to document root
     * @return boolean
     */
    public function isWithinAllowedFolders($filename) {
        foreach ($this->allowedFolders as $allowedFolder) {
            if (self::isFirstPartOfStr($filename, $allowedFolder)) {
                return true;
            }
        }
        return false;
    }

}

?>
