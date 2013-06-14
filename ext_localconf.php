<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (!t3lib_extMgm::isLoaded('cundd_composer')) {
    require t3lib_extMgm::extPath('comvosfilelist') . 'vendor/autoload.php';
}

$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][]='EXT:comvosfilelist/src/Comvos/TYPO3/Filelist/class.tx_comvosfilelist_cachehandlerproxy.php:&tx_comvosfilelist_cachehandlerproxy->handleCache';

$comvosfilelistExtensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['comvosfilelist']);
if(isset($comvosfilelistExtensionConfiguration['add_mm_dam_filelist_plugin']) && !empty($comvosfilelistExtensionConfiguration['add_mm_dam_filelist_plugin'])){
    t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_comvosfilelist_pi1.php', '_pi1', 'list_type', 1);
    t3lib_extMgm::addPItoST43('mm_dam_filelist','pi1/class.tx_comvosfilelist_pi1.php','_pi1','list_type',1);
}
?>