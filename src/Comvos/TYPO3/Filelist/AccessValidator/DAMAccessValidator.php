<?php
/***************************************************************
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
 ***************************************************************/



/**
 * Description of FilesystemAcessValidator
 *
 * @author nsaleh
 */
class Comvos_TYPO3_Filelist_AccessValidator_DAMAccessValidator extends Comvos_TYPO3_Filelist_AccessValidator_FilesystemAccessValidator {

    /**
     * QueryBuilder preconfigured for listviewselect
     * @var \Doctrine\DBAL\Query\QueryBuilder
     */
    protected $commonQueryBuilder = null;
    
    public function setCommonQueryBuilder(\Doctrine\DBAL\Query\QueryBuilder $commonQueryBuilder) {
        $this->commonQueryBuilder = $commonQueryBuilder;
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

        if(!self::isFirstPartOfStr($filename, 'typo3temp/comvosfilelist')){
            $damfileIsInListAndIndexed = $this->commonQueryBuilder
                    ->andWhere("file_path like :validatefile_path")
                    ->andWhere("file_name like :file_name")
                    ->setParameter('validatefile_path', $file->getRelativePath())
                    ->setParameter('file_name', $file->getFilename())
                    ->execute()
                    ->rowCount();
            
            if (!$damfileIsInListAndIndexed) {
                return false;
            }
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

}

?>
