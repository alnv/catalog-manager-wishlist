<?php

ClassLoader::addNamespace( 'CMWishlist' );

ClassLoader::addClasses([
    'CMWishlist\WishlistView' => 'system/modules/catalog-manager-wishlist/WishlistView.php',
    'CMWishlist\WishlistModule' => 'system/modules/catalog-manager-wishlist/WishlistModule.php',
    'CMWishlist\WishlistInserttag' => 'system/modules/catalog-manager-wishlist/WishlistInserttag.php'
]);

TemplateLoader::addFiles([
    'wishlist_view' => 'system/modules/catalog-manager-wishlist/templates',
    'wishlist_form_list' => 'system/modules/catalog-manager-wishlist/templates',
    'wishlist_form_table' => 'system/modules/catalog-manager-wishlist/templates',
    'ctlg_view_table_wishlist' => 'system/modules/catalog-manager-wishlist/templates',
    'mod_catalog_table_wishlist' => 'system/modules/catalog-manager-wishlist/templates'
]);