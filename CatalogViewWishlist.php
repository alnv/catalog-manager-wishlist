<?php

namespace CMWishlist;

class CatalogViewWishlist {


    protected $blnUseWishlist = false;


    public function initialize( &$objCatalogView ) {

        $this->blnUseWishlist = $objCatalogView->catalogUseWishlist ? true : false;

        $objCatalogView->objMainTemplate->useWishlist = $this->blnUseWishlist;
    }


    public function renderCatalog( &$arrCatalog ) {

        if ( $this->blnUseWishlist ) {

            $arrCatalog['catalogUseWishlist'] = $this->blnUseWishlist;
        }
    }
}