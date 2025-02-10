<?php

use CMWishlist\WishlistModule;
use CMWishlist\WishlistInserttag;

$GLOBALS['BE_MOD']['accounts']['member']['tables'][] = 'tl_catalog_wishlist';

$GLOBALS['TL_HOOKS']['catalogManagerViewQuery'][] = [WishlistModule::class, 'setQuery'];
$GLOBALS['TL_HOOKS']['catalogManagerInitializeView'][] = [WishlistModule::class, 'initialize'];
$GLOBALS['TL_HOOKS']['catalogManagerRenderCatalog'][] = [WishlistModule::class, 'renderCatalog'];
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = [WishlistInserttag::class, 'getInsertTagValue'];