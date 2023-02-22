<?php

$GLOBALS['TL_DCA']['tl_member']['config']['ctable'][] = 'tl_catalog_wishlist';

$GLOBALS['TL_DCA']['tl_member']['list']['operations']['wishlist'] = [
    'icon' => 'folderO.svg',
    'href' => 'table=tl_catalog_wishlist'
];