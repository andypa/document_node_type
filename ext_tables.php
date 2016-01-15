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

$GLOBALS['TCA']['pages']['columns'] = \FluidTYPO3\Flux\Utility\RecursiveArrayUtility::mergeRecursiveOverrule(
    $GLOBALS['TCA']['pages']['columns'],
    [
        'tx_fed_page_controller_action' => [
            'exclude' => 1,
            'label' => 'Document Type Layout',
            'config' => [
                'type' => 'user',
                'userFunc' => \SimpleTYPO3\DocumentNodeType\Backend\PageLayoutSelector::class.'->renderField',
            ],
        ],
        'tx_fed_page_controller_action_sub' => [
            'exclude' => 1,
            'label' => 'SubDocuments Layout',
            'config' => [
                'type' => 'user',
                'userFunc' => \SimpleTYPO3\DocumentNodeType\Backend\PageLayoutSelector::class.'->renderField',
            ],
        ],

    ]
);
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

	if(isset($GLOBALS['TCA']['pages_language_overlay']['columns'][$column])) {
		$GLOBALS['TCA']['pages_language_overlay']['columns'][$column]['config']['renderType'] = 'bootstrapSwitchElement';

		$GLOBALS['TCA']['pages_language_overlay']['columns'][$column]['config']['type'] = 'input';
		unset($GLOBALS['TCA']['pages_language_overlay']['columns'][$column]['config']['items']);
	}

}
$GLOBALS['TCA']['pages']['columns']['tx_fed_page_flexform']['label'] = 'Document Properties';


$GLOBALS['TCA']['pages']['palettes'] = \FluidTYPO3\Flux\Utility\RecursiveArrayUtility::mergeRecursiveOverrule(
    $GLOBALS['TCA']['pages']['palettes'],
    [
        'document_node_type' => [
            'showitem' => 'doktype;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.doktype_formlabel,module;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.module_formlabel,is_siteroot;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.is_siteroot_formlabel, no_search;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.no_search_formlabel, editlock;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.editlock_formlabel, php_tree_stop;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.php_tree_stop_formlabel',
        ],
        'meta_area' => [
            'showitem' => '	abstract;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.abstract_formlabel,
					keywords;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.keywords_formlabel,
					description;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.description_formlabel',
        ],
        'document_title' => [
            'showitem' => 'title;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.title_formlabel,
					nav_title;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.nav_title_formlabel,
					hidden;Power Off,
            		nav_hide;Hide in menus',
        ],
        'path' => [
            'showitem' => '	tx_realurl_pathsegment,
							tx_realurl_pathoverride,
							tx_realurl_exclude,
							tx_realurl_nocache,
							--linebreak--,
							url_scheme;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.url_scheme_formlabel,
							target;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.target_formlabel,
							alias;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.alias_formlabel,
							',
        ],
		'external' => [
			'showitem' => 'urltype;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.urltype_formlabel, url;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.url_formlabel',
		],
		'shortcut' => [
			'showitem' => 'shortcut_mode;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.shortcut_mode_formlabel,shortcut;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.shortcut_formlabel',
		],
		'mountpoint' => [
			'showitem' => 'mount_pid_ol;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.mount_pid_ol_formlabel,mount_pid;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.mount_pid_formlabel',
		]
    ]
);


$common = '
			--div--;SEO Properties,
				--palette--;Path;path,
				--palette--;Meta content;meta_area,
				--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.editorial;editorial,
			--div--;Document Node Properties,
				--palette--;Document Node Properties;document_node_type,
				--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.language;language,
				--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.caching;caching,
				--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.config;config,
			--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.access,
			--div--;Sub documents,
				tx_fed_page_controller_action_sub,tx_fed_page_flexform_sub,
			--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.extended,';
$GLOBALS['TCA']['pages']['types'] = \FluidTYPO3\Flux\Utility\RecursiveArrayUtility::mergeRecursiveOverrule(
    $GLOBALS['TCA']['pages']['types'],
    [
        // normal
        (string)\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_DEFAULT => [
            'showitem' => '
        		--div--;Properties,
        			--palette--;Document Title;document_title,
					tx_fed_page_controller_action,
					tx_fed_page_flexform,
					categories,
					--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.media;media,
		' . $common,
        ],
        // external URL
        (string)\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_LINK => [
            'showitem' => '
            --div--;Properties,
            	--palette--;Document Title;document_title,
            	--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.external;external,
            	categories,
				--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.media;media,
		' . $common,
        ],
        // shortcut
        (string)\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_SHORTCUT => [
            'showitem' => '
            --div--;Properties,
       			--palette--;Document Title;document_title,
           		--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.shortcut;shortcut,
				categories,
				--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.media;media,
			' . $common,
        ],
        // mount page
        (string)\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_MOUNTPOINT => [
            'showitem' => '
				--div--;Properties,
					--palette--;Document Title;document_title,
            		--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.mountpoint;mountpoint,
				categories,
				--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.media;media,
			' . $common,
        ],
		// Folder
		(string)\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_SYSFOLDER => [
			'showitem' => '
        		--div--;Properties,
        			--palette--;Document Title;document_title,
					categories,
					--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.media;media,
			' . $common,
		],
        // spacer
        (string)\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_SPACER => [
            'showitem' => '
        		--div--;Properties,
        			--palette--;Document Title;document_title,
				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.access,
					--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.visibility;visibility,
					--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.access;access,
				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.behaviour,
					--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.miscellaneous;adminsonly,
				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.extended,
			',
        ],

        // Trash
        (string)\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_RECYCLER => [
            'showitem' => '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.standard;standard,
					--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.title;titleonly,
				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.access,
					--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.visibility;hiddenonly,
				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.behaviour,
					--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.miscellaneous;adminsonly,
				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.extended,
		',
        ],
    ]
);

//$GLOBALS['TCA']['pages_language_overlay']  = $GLOBALS['TCA']['pages'] ;
