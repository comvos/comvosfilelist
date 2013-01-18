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
class Comvos_TYPO3_Filelist_EncryptionTool {

    private $secretKey=null;
    
    public function __construct($secretKey) {
        $this->secretKey = $secretKey;
    }
    private function makeValidateable($value) {
        $container = new stdClass();
        $container->value = $value;
        $encoded = json_encode($container);
        ;
        if (!$encoded) {
            throw new Exception('Could not make Value validateable!');
        }
        return $encoded;
    }

    private function fnEncrypt($sValue) {
        $sValue = $this->makeValidateable($sValue);
        return trim(str_replace(array('/', '+', '='), array('_', '-', '$'), base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->secretKey, $sValue, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)))));
    }

    private function fnDecrypt($sValue) {

        return $this->getValidatedValue(trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->secretKey, base64_decode(str_replace(array('_', '-', '$'), array('/', '+', '='), $sValue)), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }

    private function getValidatedValue($value) {

        $container = json_decode($value);

        if (!is_object($container) && !isset($container->value)) {

            throw new Exception('Decryption failed');
            ;
        }

        return $container->value;
    }

    public function encrypt($filename) {

        $extconf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['comvosfilelist']);
        if (!$extconf['enryptionKey']) {
            throw new Exception('comvosfilelist: You MUST set an enryptionKey in extension configuration (extensionmanager).');
        }
        return $this->fnEncrypt($filename);
    }

    public function decrypt($value) {

        if (!$value) {
            return $value;
        }

        return $this->fnDecrypt($value);
    }

}

?>
