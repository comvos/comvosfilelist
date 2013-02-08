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
use Symfony\Component\Filesystem\Filesystem;

/**
 * 
 * @author nsaleh
 */
class Comvos_TYPO3_Filelist_Cachehandler {

    /**
     * Handle clearCachePostProc hook 
     *
     * @param	array		@see clearCache 
     * @param	object		@see clearCache 
     * @return	void
     */
    public function handleCache($confArray, $objArray) {

        $tmpfolder = PATH_site . 'typo3temp/comvosfilelist';
        $finder = new Finder();

        $finder->in($tmpfolder)->name('*.php');
        $pageid = null;
        if (is_int($confArray['cacheCmd'])) {
            $pageid = $confArray['cacheCmd'];
        }
        if (isset($confArray['uid_page']) && is_int($confArray['uid_page'])) {
            $pageid = $confArray['uid_page'];
        }
        //no specific page => remove all images 
        if (!is_int($confArray['cacheCmd']) && (!isset($confArray['uid_page']) || empty($confArray['uid_page']))) {
            $finder->name('*.jpg');
        }
        $fs = new Filesystem();
        $fs->remove($finder->files());

        //specific page remove only these thumbs
        if ($pageid && file_exists($tmpfolder . '/' . $pageid)) {
            $finder = new Finder();

            $finder->in($tmpfolder . '/' . $pageid)->name('*.jpg');
            $fs->remove($finder->files());
        }
    }

}
