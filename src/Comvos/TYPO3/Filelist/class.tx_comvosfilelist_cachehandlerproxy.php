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
 * 
 * @author nsaleh
 */
class tx_comvosfilelist_cachehandlerproxy {

    /**
     * Handle clearCachePostProc hook 
     *
     * @param	array		@see clearCache 
     * @param	object		@see clearCache 
     * @return	void
     */
    public function handleCache($confArray, $objArray) {

        //autoloader from composer
        $autoloader = require t3lib_extMgm::extPath('comvosfilelist') . 'vendor/autoload.php';
        $autoloader->add('Comvos_', t3lib_extMgm::extPath('comvosfilelist') . 'src/');
        $autoloader->add('Comvos', t3lib_extMgm::extPath('comvosfilelist') . 'src/');
        $cachehandler = new Comvos_TYPO3_Filelist_Cachehandler();
        $cachehandler->handleCache($confArray, $objArray);
    }

}
