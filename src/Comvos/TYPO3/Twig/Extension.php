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
 * Description of Typo3_Twig_Extension
 *
 * @author nsaleh
 */
class Comvos_TYPO3_Twig_Extension extends Twig_Extension {
    
    
    public function __construct($plugin=null) {
        $this->plugin=$plugin;
    }


    /**
     * FE Plugin 
     * @var tslib_pibase
     */
    protected $plugin=null;
    
    public function getPlugin() {
        return $this->plugin;
    }

    public function setPlugin($plugin) {
        $this->plugin = $plugin;
    }

        
    public function getName() {
        return 'comvostypo3';
    }

    public function getFunctions() {
        
        return array(
            'typolink' => new Twig_Function_Method( $this , 'typolink' ),
            'previewImage' => new Twig_Function_Method( $this , 'previewImage' ),
            'includeStylesheet' => new Twig_Function_Method( $this , 'includeStylesheet' ),
            'includeJavascript' => new Twig_Function_Method( $this , 'includeJavascript' ),
        );
        
    }
    public function getFilters() {
        return array(
            't3trans' =>   new Twig_Filter_Method( $this , 't3trans'   ),
            't3webpath' => new Twig_Filter_Method( $this , 't3webpath' ),
        );
    }
    public function t3trans( $alt='' , $key=''){
        
        return $this->plugin->pi_getLL($key,$alt);
        
    }
    public function t3webpath( $abspath ){
        
        return str_replace(PATH_site, '', (string) $abspath);
        
    }
    
    public function typolink($pageId, $options = array()) {

        $cObj = t3lib_div::makeInstance('tslib_cObj');

        $conf = array_merge(array(
            'parameter' => $pageId,
            // We must add cHash because we use parameters
            'useCacheHash' => true,
            // We want link only
            'returnLast' => 'url',
                ), $options);
        $url = $cObj->typoLink('', $conf);
        return $url;
    }

    public function previewImage($filename, $options = array(
    )
    ) {
        $defaults = array(
            'width' => 50,
            'height' => 50,
            'folder' => ''
        );
        $options = array_merge($defaults, $options);
        $imageProc = t3lib_div::makeInstance('t3lib_stdGraphic');

        $imageProc->init();

        $imageProc->tempPath = PATH_site . 'typo3temp/';
        if ($options['folder']) {
            if(!file_exists($imageProc->tempPath.$options['folder'])){
                mkdir($imageProc->tempPath.$options['folder'],0775,true);
            }
            $imageProc->tempPath .= $options['folder'] . '/';
        }

        $ret = $imageProc->imageMagickConvert($filename, 'jpg', $options['width'], $options['height']);
        return str_replace(PATH_site, '/', $ret[3]);
    }

    public function includeStylesheet($filename, $media = 'screen') {
        $GLOBALS['TSFE']->additionalHeaderData['twigconnector'] .= '<link rel="stylesheet"  media="' . $media . '"  type="text/css" href="' . $filename . '" />';
    }

    public function includeJavascript($filename) {
        $GLOBALS['TSFE']->additionalHeaderData['twigconnector'] .= '<script type="text/javascript" src="' . $filename . '"></script>';
    }

}

?>
