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
 * Description of FilelistException
 *
 * @author nsaleh
 */
class Comvos_TYPO3_Filelist_FilelistException extends \Exception {

    public static function noModeSet() {
        return new self("Neither directory nor category have been set.");
    }

    public static function modeCouldNotBeSet() {
        return new self("Directory and category have been set. You must choose one of both.");
    }

    public static function noDirectorySet() {
        return new self("Directory has not been set.");
    }

    public static function fileAccessForbidden() {
        return new self("You are not allowed to access this file.");
    }
    
    public static function noAccessValidator() {
        return new self("You must provide an accessvalidator.");
    }

}

?>
