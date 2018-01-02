<?php

$GLOBALS['TL_DCA']['tl_module']['palettes']['catalogUniversalView'] = str_replace( 'catalogUseSocialSharingButtons;', 'catalogUseSocialSharingButtons;{wishlist_legend},catalogUseWishlist;', $GLOBALS['TL_DCA']['tl_module']['palettes']['catalogUniversalView'] );

$GLOBALS['TL_DCA']['tl_module']['fields']['catalogUseWishlist'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_module']['catalogUseWishlist'],
    'inputType' => 'checkbox',

    'eval' => [

        'tl_class' => 'clr',
        'submitOnChange' => true,
    ],

    'exclude' => true,
    'sql' => "char(1) NOT NULL default ''"
];