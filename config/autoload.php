<?php

ClassLoader::addNamespace( 'CMWishlist' );

ClassLoader::addClasses([

    'CMWishlist\CatalogViewWishlist' => 'system/modules/catalog-manager-wishlist/CatalogViewWishlist.php'
]);

TemplateLoader::addFiles([

    'ctlg_view_table' => 'system/modules/catalog-manager-wishlist/templates',
    'ctlg_view_teaser' => 'system/modules/catalog-manager-wishlist/templates',
    'mod_catalog_table' => 'system/modules/catalog-manager-wishlist/templates',
    'mod_catalog_universal' => 'system/modules/catalog-manager-wishlist/templates'
]);