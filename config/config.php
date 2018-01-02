<?php

$GLOBALS['TL_HOOKS']['catalogManagerInitializeView'][] = [ 'CMWishlist\CatalogViewWishlist', 'initialize' ];
$GLOBALS['TL_HOOKS']['catalogManagerRenderCatalog'][] = [ 'CMWishlist\CatalogViewWishlist', 'renderCatalog' ];