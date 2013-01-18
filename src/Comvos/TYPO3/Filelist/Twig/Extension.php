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
 *
 * @author nsaleh
 */
class Comvos_TYPO3_Filelist_Twig_Extension extends Twig_Extension {

    public function __construct($plugin = null) {
        $this->plugin = $plugin;
    }

    /**
     * FE Plugin 
     * @var tslib_pibase
     */
    protected $plugin = null;

    public function getPlugin() {
        return $this->plugin;
    }

    public function setPlugin($plugin) {
        $this->plugin = $plugin;
    }

    public function getName() {
        return 'comvostypo3filelist';
    }

    public function getFilters() {
        return array(
            't3securefile' => new Twig_Filter_Method($this, 't3securefile'),
            't3crypt' => new Twig_Filter_Method($this, 't3crypt'),
        );
    }

    public function t3crypt($string){
        return $this->plugin->getEncryptionTool()->encrypt($string);
    }

    public function t3securefile($filename) {
        $encryptedFilename = $this->plugin->getEncryptionTool()->encrypt($filename);
        $link='&tx_comvosfilelist_pi1[action]=stream&tx_comvosfilelist_pi1[file]='.$encryptedFilename;
        $cHash=t3lib_div::generateCHash($link); 
        return '/comvosfilelist/' . $GLOBALS['TSFE']->id .'/'. $encryptedFilename.'/'.$cHash;
    }

}

?>
