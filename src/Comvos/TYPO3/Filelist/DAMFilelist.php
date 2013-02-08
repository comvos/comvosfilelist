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
 * Description of DAMFilelist
 *
 * @author nsaleh
 */
class Comvos_TYPO3_Filelist_DAMFilelist extends Comvos_TYPO3_Filelist_List {

    /**
     * doctrine database connection
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection = null;

    /**
     * Directory containing files to be listed
     * @var string 
     */
    protected $directory = '';

    /**
     * Dam category ID
     * @var string
     */
    protected $category = '';

    /**
     * Indicates in which mode listing is working 
     * @var int 
     */
    protected $mode = self::MODE_DIRECTORY;

    /**
     * List files in directory
     */

    const MODE_DIRECTORY = 1;
    /**
     * List files with dam category(@see category)
     */
    const MODE_CATEGORY = 2;

    /**
     * 
     * @param type $configuration
     */
    public function __construct($configuration, \Doctrine\DBAL\Connection $connection) {

        parent::__construct($configuration);

        $this->connection = $connection;

        if (!empty($configuration['directory'])) {
            $this->setMode(self::MODE_DIRECTORY);
            $this->setDirectory($configuration['directory']);
        }
        if (!empty($configuration['category'])) {
            $this->setMode(self::MODE_CATEGORY);
            $this->setCategory($configuration['category']);
        }
        if (empty($configuration['directory']) && empty($configuration['category'])) {
            throw Comvos_TYPO3_Filelist_FilelistException::noModeSet();
        }
        if (!empty($configuration['directory']) && !empty($configuration['category'])) {
            throw Comvos_TYPO3_Filelist_FilelistException::modeCouldNotBeSet();
        }
        $this->initFileListFromDam();
    }

    public function getFileForSingleview($filename) {
        $file = new Symfony\Component\Finder\SplFileInfo(PATH_site . $filename, preg_replace('/[^\/]+$/', '', $filename), $filename);

        $this->getFileAccessValidator()->setCommonQueryBuilder($this->getCommonQuerybuilder());

        if (!$this->getFileAccessValidator()->validateFile($file)) {
            throw Comvos_TYPO3_Filelist_FilelistException::fileAccessForbidden();
        }

        $file->meta = $this->getCommonQuerybuilder()
                        ->andWhere("file_path like :singlefile_path")
                        ->andWhere("file_name like :file_name")
                        ->setParameter('singlefile_path', $file->getRelativePath())
                        ->setParameter('file_name', $file->getFilename())
                        ->execute()->fetch();

        return $file;
    }

    /**
     * 
     * @return \Doctrine\DBAL\Query\QueryBuilder preconfigured querybuilder for fetching files
     */
    protected function getCommonQuerybuilder() {

        $commonQueryBuilder = $this->connection->createQueryBuilder()
                ->select('*')
                ->from('tx_dam', 'damfile')
        ;

        if ($this->isInDirectoryMode()) {
            $commonQueryBuilder->andWhere('file_path like :file_path')
                    ->setParameter('file_path', $this->getDirectory());
        }
        if ($this->isInCategoryMode()) {
            $commonQueryBuilder
                    ->join('damfile', 'tx_dam_mm_cat', 'mmcat', 'mmcat.uid_local=damfile.uid')
            ;

            //@todo solve with "IN" 
            $cats = explode(',', $this->getCategory());
            $crits = array();
            foreach ($cats as $nr => $category) {
                $commonQueryBuilder
                        ->setParameter('category_' . $nr, $category);
                $crits[] = 'mmcat.uid_foreign = :category_' . $nr;
            }
            $commonQueryBuilder
                    ->andWhere(implode(' OR ', $crits));
        }
        return $commonQueryBuilder;
    }

    protected function initFileListFromDam() {



        $damcount = $this->getCommonQuerybuilder();

        $this->setCount($damcount
                        ->execute()
                        ->rowCount());

        $this->setLastpage(max(1, ceil($this->getCount() / $this->getEntriesPerPage())));
        if ($this->getPage() >= $this->getLastpage()) {
            $this->setPage($this->getLastpage());
        }

        $damfilesQB = $this->getCommonQuerybuilder();
        $damfiles = $damfilesQB
                ->setFirstResult(($this->getPage() - 1) * $this->getEntriesPerPage())
                ->setMaxResults($this->getEntriesPerPage())
                ->orderBy('damfile.title', 'ASC')
                ->execute()
                ->fetchAll();

        foreach ($damfiles as $file) {
            $newFile = new Symfony\Component\Finder\SplFileInfo(PATH_site . $file['file_path'] . $file['file_name'], $file['file_path'], $file['file_path'] . $file['file_name']);
            $newFile->meta = $file;
            $this->files[] = $newFile;
        }
    }

    public function getDirectory() {
        return $this->directory;
    }

    public function setDirectory($directory) {
        $this->directory = $directory;
    }

    public function getCategory() {
        return $this->category;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function getMode() {
        return $this->mode;
    }

    public function setMode($mode) {
        $this->mode = $mode;
    }

    public function isInDirectoryMode() {
        return $this->mode === self::MODE_DIRECTORY;
    }

    public function isInCategoryMode() {
        return $this->mode === self::MODE_CATEGORY;
    }

}

?>
