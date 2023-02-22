<?php

$GLOBALS['BE_MOD']['accounts']['member']['tables'][] = 'tl_catalog_wishlist';

$GLOBALS['TL_HOOKS']['catalogManagerViewQuery'][] = ['CMWishlist\WishlistModule', 'setQuery'];
$GLOBALS['TL_HOOKS']['catalogManagerInitializeView'][] = ['CMWishlist\WishlistModule', 'initialize'];
$GLOBALS['TL_HOOKS']['catalogManagerRenderCatalog'][] = ['CMWishlist\WishlistModule', 'renderCatalog'];
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = ['CMWishlist\WishlistInserttag', 'getInsertTagValue'];