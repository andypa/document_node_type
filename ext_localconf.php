<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {
	// Add new field type to NodeFactory
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1449406603] = [
		'nodeName' => 'bootstrapSwitchElement',
		'priority' => '70',
		'class' => \SimpleTYPO3\DocumentNodeType\Form\Element\BootstrapSwitchElement::class,
	];
}
