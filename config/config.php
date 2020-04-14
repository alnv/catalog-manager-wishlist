<?php

$GLOBALS['TL_HOOKS']['catalogManagerViewQuery'][] = ['CMWishlist\WishlistModule', 'setQuery'];
$GLOBALS['TL_HOOKS']['catalogManagerInitializeView'][] = ['CMWishlist\WishlistModule', 'initialize'];
$GLOBALS['TL_HOOKS']['catalogManagerRenderCatalog'][] = ['CMWishlist\WishlistModule', 'renderCatalog'];
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = ['CMWishlist\WishlistInserttag', 'getInsertTagValue'];