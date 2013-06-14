<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi1'] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi1'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_pi1', 'FILE:EXT:' . $_EXTKEY . '/pi1/PluginConfigurationStructure.xml');


t3lib_extMgm::addPlugin(array(
    'LLL:EXT:comvosfilelist/locallang_db.xml:tt_content.list_type_pi1',
    $_EXTKEY . '_pi1',
    t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
        ), 'list_type');


$comvosfilelistExtensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['comvosfilelist']);
if (isset($comvosfilelistExtensionConfiguration['add_mm_dam_filelist_plugin']) && !empty($comvosfilelistExtensionConfiguration['add_mm_dam_filelist_plugin'])) {
    $TCA['tt_content']['types']['list']['subtypes_excludelist']['mm_dam_filelist_pi1'] = 'layout,select_key,pages';
    $TCA['tt_content']['types']['list']['subtypes_addlist']['mm_dam_filelist_pi1'] = 'pi_flexform';
    t3lib_extMgm::addPiFlexFormValue('mm_dam_filelist_pi1', 'FILE:EXT:comvosfilelist/mm_dam_filelist/flexform_ds_pi1.xml');
    t3lib_extMgm::addPlugin(Array('LLL:EXT:comvosfilelist/mm_dam_filelist/locallang_db.xml:tt_content.list_type_pi1', 'mm_dam_filelist_pi1'), 'list_type');
}
if (TYPO3_MODE === 'BE') {
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_comvosfilelist_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY) . 'pi1/class.tx_comvosfilelist_pi1_wizicon.php';

    //autoloader from composer

    if (t3lib_extMgm::isLoaded('cundd_composer') && file_exists(t3lib_extMgm::extPath('cundd_composer').'vendor/autoload.php')) {
        require_once t3lib_extMgm::extPath('cundd_composer') . 'Classes/Autoloader.php';
        Tx_CunddComposer_Autoloader::register();
    } else {
        require t3lib_extMgm::extPath('comvosfilelist') . 'vendor/autoload.php';
    }

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['comvosfilelist'] = array('Comvos_TYPO3_Filelist_SecurefolderReport');
}

t3lib_extMgm::addStaticFile($_EXTKEY, 'static/default/', 'default');
?>