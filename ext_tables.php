<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Document Node Type');
if ('BE' === TYPO3_MODE) {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript($_EXTKEY, 'setup', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:document_node_type/Configuration/TypoScript/setup.txt">');
}

//
$GLOBALS['TCA']['pages']['columns'] = \FluidTYPO3\Flux\Utility\RecursiveArrayUtility::mergeRecursiveOverrule($GLOBALS['TCA']['pages']['columns'], array(
	'tx_fed_page_controller_action' => array (
		'exclude' => 1,
		'label' => 'Layout',
		'config' => array (
			'type' => 'user',
			'userFunc' => \SimpleTYPO3\DocumentNodeType\Backend\PageLayoutSelector::class . '->renderField'
		)
	),
	'tx_fed_page_controller_action_sub' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:fluidpages/Resources/Private/Language/locallang.xlf:pages.tx_fed_page_controller_action_sub',
		'config' => array (
			'type' => 'user',
			'userFunc' => \SimpleTYPO3\DocumentNodeType\Backend\PageLayoutSelector::class . '->renderField'
		)
	),

));