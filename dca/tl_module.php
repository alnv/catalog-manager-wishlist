<?php

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'wishlistWidget';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['wishlistWidget'] = 'wishlistEnableFilter,wishlistAmount,wishlistDisableRemoveButton';
$GLOBALS['TL_DCA']['tl_module']['palettes']['catalogUniversalView'] = str_replace( 'catalogUseSocialSharingButtons;', 'catalogUseSocialSharingButtons;{wishlist_legend},wishlistWidget;', $GLOBALS['TL_DCA']['tl_module']['palettes']['catalogUniversalView'] );

$GLOBALS['TL_DCA']['tl_module']['fields']['wishlistWidget'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_module']['wishlistWidget'],
    'inputType' => 'checkbox',

    'eval' => [

        'tl_class' => 'w50',
        'submitOnChange' => true
    ],

    'exclude' => true,
    'sql' => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['wishlistAmount'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_module']['wishlistAmount'],
    'inputType' => 'checkbox',

    'eval' => [

        'tl_class' => 'w50'
    ],

    'exclude' => true,
    'sql' => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['wishlistDisableRemoveButton'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_module']['wishlistDisableRemoveButton'],
    'inputType' => 'checkbox',

    'eval' => [

        'tl_class' => 'w50'
    ],

    'exclude' => true,
    'sql' => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['wishlistEnableFilter'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_module']['wishlistEnableFilter'],
    'inputType' => 'checkbox',

    'eval' => [

        'tl_class' => 'w50'
    ],

    'exclude' => true,
    'sql' => "char(1) NOT NULL default ''"
];