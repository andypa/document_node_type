<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Document Node Type');
if ('BE' === TYPO3_MODE) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        $_EXTKEY,
        'setup',
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:document_node_type/Configuration/TypoScript/setup.txt">'
    );
}

//$GLOBALS['TCA']['pages']['columns'] = \FluidTYPO3\Flux\Utility\RecursiveArrayUtility::mergeRecursiveOverrule(
//    $GLOBALS['TCA']['pages']['columns'],
//    [
//        'tx_fed_page_controller_action' => [
//            'exclude' => 1,
//            'label' => 'Document Type',
//            'config' => [
//                'type' => 'user',
//                'userFunc' => \SimpleTYPO3\DocumentNodeType\Backend\PageLayoutSelector::class.'->renderField',
//            ],
//        ],
//        'tx_fed_page_controller_action_sub' => [
//            'exclude' => 1,
//            'label' => 'LLL:EXT:fluidpages/Resources/Private/Language/locallang.xlf:pages.tx_fed_page_controller_action_sub',
//            'config' => [
//                'type' => 'user',
//                'userFunc' => \SimpleTYPO3\DocumentNodeType\Backend\PageLayoutSelector::class.'->renderField',
//            ],
//        ],
//
//    ]
//);
$columns = [
    'php_tree_stop',
    'editlock',
    'hidden',
    'extendToSubpages',
    'nav_hide',
    'no_cache',
    'no_search',
    'is_siteroot',
    'tx_realurl_pathsegment',
    'tx_realurl_pathoverride',
    'tx_realurl_exclude',
    'tx_realurl_nocache',
];
foreach ($columns as $column) {
    $GLOBALS['TCA']['pages']['columns'][$column]['config']['renderType'] = 'bootstrapSwitchElement';

    $GLOBALS['TCA']['pages']['columns'][$column]['config']['type'] = 'input';
    unset($GLOBALS['TCA']['pages']['columns'][$column]['config']['items']);
}
$GLOBALS['TCA']['pages']['columns']['tx_fed_page_flexform']['label'] = 'Document Properties';