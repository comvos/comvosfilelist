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

use Symfony\Component\Finder\Finder;


/**
 * Description of DAMFilelist
 *
 * @author nsaleh
 */
class Comvos_TYPO3_Filelist_FilesystemFilelist extends Comvos_TYPO3_Filelist_List {

    /**
     * Directory containing files to be listed
     * @var string 
     */
    protected $directory = '';

    /**
     * 
     * @param type $configuration
     */
    public function __construct($configuration) {

        parent::__construct($configuration);

        
        if (!empty($configuration['directory'])) {
            $this->setDirectory($configuration['directory']);
        } else {
            throw Comvos_TYPO3_Filelist_FilelistException::noDirectorySet();
        }

        $this->initFileListFromfilesystem();
    }

    protected function initFileListFromfilesystem() {

        $finder = new Finder();
        
        $finder->files()->in(PATH_site . $this->getDirectory());
        $finder->sortByName();
        $files = $finder;

        $this->setCount($files->count());

        $this->setFiles(array_slice(iterator_to_array($files->getIterator()), ( $this->getPage() - 1) * $this->getEntriesPerPage(), $this->getEntriesPerPage()));
        $this->setLastpage(max(1, ceil($files->count() / $this->getEntriesPerPage())));
        if ($this->getPage() >= $this->getLastpage()) {
            $this->setPage($this->getLastpage());
        }
    }

    public function getFileForSingleview($filename) {
        $file = new Symfony\Component\Finder\SplFileInfo(PATH_site . $filename, preg_replace('/[^\/]+$/', '', $filename), $filename);

        if (!$this->getFileAccessValidator()->validateFile($file)) {
            throw Comvos_TYPO3_Filelist_FilelistException::fileAccessForbidden();
        }
        return $file;
    }

    public function getDirectory() {
        return $this->directory;
    }

    public function setDirectory($directory) {
        $this->directory = $directory;
    }

}

?>
