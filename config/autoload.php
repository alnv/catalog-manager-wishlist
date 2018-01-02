<?php

ClassLoader::addNamespace( 'CMWishlist' );

ClassLoader::addClasses([

    'CMWishlist\CatalogViewWishlist' => 'system/modules/catalog-manager-wishlist/CatalogViewWishlist.php'
]);

TemplateLoader::addFiles([

    'ctlg_view_table_wl' => 'system/modules/catalog-manager-wishlist/templates',
    'ctlg_view_teaser_wl' => 'system/modules/catalog-manager-wishlist/templates',
    'mod_catalog_table_wl' => 'system/modules/catalog-manager-wishlist/templates',
    'mod_catalog_universal_wl' => 'system/modules/catalog-manager-wishlist/templates'
]);