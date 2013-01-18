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
 * 
 * @author nsaleh
 */
abstract class Comvos_TYPO3_Filelist_List {

    /**
     * Total filecount
     * @var int
     */
    protected $count = 0;
    
    /**
     * Files per page
     * @var int
     */
    protected $entriesPerPage = 10;
    
    /**
     * Page to be fetched
     * @var int
     */
    protected $page=1;
    
    protected $files = array();
    
    protected $lastpage = null;
    /**
     *
     * @var Comvos_TYPO3_Filelist_AccessValidator
     */
    protected $fileAccessValidator=null;
    
    public function __construct( $configuration ) {
        
        $this->setEntriesPerPage($configuration['entriesPerPage']);
        
        if (!empty($configuration['page'])) {
            $this->setPage($configuration['page']);
        }
        
        if (!empty($configuration['fileAccessValidator'])){
            $this->setFileAccessValidator($configuration['fileAccessValidator']);
        }else{
            throw Comvos_TYPO3_Filelist_FilelistException::noAccessValidator();
        }
    }
    public function getFileAccessValidator() {
        return $this->fileAccessValidator;
    }

    public function setFileAccessValidator($fileAccessValidator) {
        $this->fileAccessValidator = $fileAccessValidator;
    }

        
    /**
     * Get SplFileInfo Object from filename after validating, the file is within
     * the filelist (allowed to be accessed)
     * 
     * @var string $filename
     * @return Symfony\Component\Finder\SplFileInfo
     */
    abstract function getFileForSingleview($filename);
    
    public function getCount() {
        return $this->count;
    }

    public function setCount( $count ) {
        $this->count = $count;
    }

    public function getEntriesPerPage() {
        return $this->entriesPerPage;
    }

    public function setEntriesPerPage( $entriesPerPage ) {
        $this->entriesPerPage = $entriesPerPage;
    }

    public function getPage() {
        return $this->page;
    }

    public function setPage( $page ) {
        $this->page = $page;
    }

    public function getFiles() {
        return $this->files;
    }

    public function setFiles( $files ) {
        $this->files = $files;
    }
    public function getLastpage() {
        return $this->lastpage;
    }

    public function setLastpage($lastpage) {
        $this->lastpage = $lastpage;
    }




}

?>
