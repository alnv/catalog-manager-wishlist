<?php

ClassLoader::addNamespace( 'CMWishlist' );

ClassLoader::addClasses([

    'CMWishlist\WishlistView' => 'system/modules/catalog-manager-wishlist/WishlistView.php',
    'CMWishlist\WishlistModule' => 'system/modules/catalog-manager-wishlist/WishlistModule.php',
    'CMWishlist\WishlistInserttag' => 'system/modules/catalog-manager-wishlist/WishlistInserttag.php'
]);

TemplateLoader::addFiles([

    'wishlist_view' => 'system/modules/catalog-manager-wishlist/templates',
    'wishlist_form' => 'system/modules/catalog-manager-wishlist/templates',
    'ctlg_view_table' => 'system/modules/catalog-manager-wishlist/templates',
    'ctlg_view_master' => 'system/modules/catalog-manager-wishlist/templates',
    'ctlg_view_teaser' => 'system/modules/catalog-manager-wishlist/templates',
    'mod_catalog_table' => 'system/modules/catalog-manager-wishlist/templates'
]);