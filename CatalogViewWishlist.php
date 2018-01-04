<?php

namespace CMWishlist;

use CatalogManager\CatalogController;
use CatalogManager\Toolkit;

class CatalogViewWishlist extends CatalogController {


    protected $blnUseWishlist = false;


    public function __construct() {

        parent::__construct();

        $this->import( 'Database' );
    }


    public function initialize( &$objCatalogView ) {

        $this->blnUseWishlist = $objCatalogView->wishlistWidget ? true : false;
        $objCatalogView->objMainTemplate->useWishlist = $this->blnUseWishlist;

        if ( $this->blnUseWishlist && \Input::get('wishlist_type') ) {

            if ( !$this->validateInput() ) return null;

            switch ( \Input::get('wishlist_type') ) {

                case 'add_to_wishlist':

                    $this->addToWishlist();
                    $this->request();

                    break;

                case 'remove_from_wishlist':

                    $this->removeFromWishlist();
                    $this->request();

                    break;
            }
        }
    }


    public function renderCatalog( &$arrCatalog, $strTablename, $objCatalogView ) {

        if ( $this->blnUseWishlist ) {

            $arrCss = [];
            $strAmountValue = '1';
            $blnInWishlist = false;

            $objSession = \Session::getInstance();
            $arrSession = $objSession->get( $objCatalogView->catalogTablename );

            if ( !Toolkit::isEmpty( $arrSession ) ) {

                if ( isset( $arrSession['amounts'] ) && $arrSession['amounts'][ $arrCatalog['id'] ] ) {

                    $strAmountValue = $arrSession['amounts'][ $arrCatalog['id'] ];
                }

                if ( isset( $arrSession['ids'] ) && in_array( $arrCatalog['id'], $arrSession['ids'] ) ) {

                    $blnInWishlist = true;
                    $arrCss[] = ' added-to-wishlist';
                }
            }

            $arrCatalog['isInWishlist'] = $blnInWishlist;
            $arrCatalog['wishlistCss'] = implode( ',', $arrCss );
            $arrCatalog['wishlistAmountValue'] = $strAmountValue;

            $arrCatalog['wishlistAddButton'] = true;
            $arrCatalog['useWishlist'] = $this->blnUseWishlist;
            $arrCatalog['wishlistTable'] = $objCatalogView->catalogTablename;
            $arrCatalog['wishlistAmount'] = $objCatalogView->wishlistAmount ? true : false;
            $arrCatalog['wishlistDisableRemoveButton'] = $objCatalogView->wishlistDisableRemoveButton ? true : false;

            if (  $blnInWishlist && !$arrCatalog['wishlistAmount'] ) $arrCatalog['wishlistAddButton'] = false;

            $arrCatalog['wishlistAmountLabel'] = 'Anzahl';
            $arrCatalog['wishlistAddButtonLabel'] = $blnInWishlist ? 'Ändern' : 'Zur Wunschliste hinzufügen';
        }
    }


    public function setQuery( $arrQuery, $objCatalogView ) {

        if ( !$objCatalogView->wishlistEnableFilter ) return $arrQuery;

        $objSession = \Session::getInstance();
        $arrSession = $objSession->get( $objCatalogView->catalogTablename );

        if ( !Toolkit::isEmpty( $arrSession ) ) {

            if ( is_array( $arrSession['ids'] ) && !empty( $arrSession['ids'] ) ) {

                $arrQuery['where'][] = [

                    'field' => 'id',
                    'operator' => 'contain',
                    'value' => $arrSession['ids']
                ];
            }
        }

        return $arrQuery;
    }


    protected function addToWishlist() {

        $objSession = \Session::getInstance();
        $objSession->set( \Input::get('wishlist_table'), $this->getWishlistData() );
    }


    protected function removeFromWishlist() {

        $objSession = \Session::getInstance();
        $arrSession = $objSession->get( \Input::get('wishlist_table') );

        if ( !Toolkit::isEmpty( $arrSession ) ) {

            if ( isset( $arrSession['amounts'] ) ) {

                unset( $arrSession['amounts'][ \Input::get('wishlist_id') ] );
            }

            if ( is_array( $arrSession['ids'] ) && in_array( \Input::get('wishlist_id'), $arrSession['ids'] ) ) {

                $intKey = array_search( \Input::get('wishlist_id'), $arrSession['ids'] );
                unset( $arrSession['ids'][ $intKey ] );
            }

            $objSession->set( \Input::get('wishlist_table'), $arrSession );
        }
    }


    protected function request() {

        $strRedirect = preg_replace( '/[&,?]wishlist_type=remove_from_wishlist/', '', \Environment::get('request') );
        $strRedirect = preg_replace( '/[&,?]wishlist_type=add_to_wishlist/', '', $strRedirect );
        $strRedirect = preg_replace( '/[&,?]wishlist_amount=[^&]*/i', '', $strRedirect );
        $strRedirect = preg_replace( '/[&,?]wishlist_table=[^&]*/i', '', $strRedirect );
        $strRedirect = preg_replace( '/[&,?]wishlist_id=[^&]*/i', '', $strRedirect );

        \Controller::redirect( $strRedirect );
    }


    protected function validateInput() {

        if ( !$this->Database->tableExists( \Input::get('wishlist_table') ) ) return false;

        $objRow = $this->Database->prepare( sprintf( 'SELECT id FROM %s WHERE id = ?', \Input::get('wishlist_table') ) )->execute( \Input::get('wishlist_id') );

        return $objRow->numRows ? true : false;
    }


    protected function getWishlistData() {

        $arrIds = [];
        $arrAmounts = [];
        $objSession = \Session::getInstance();
        $arrSession = $objSession->get( \Input::get('wishlist_table') );

        if ( !Toolkit::isEmpty( $arrSession ) ) {

            $arrIds = $arrSession['ids'];
            $arrAmounts = $arrSession['amounts'];
        }

        if ( \Input::get('wishlist_id') && !in_array( \Input::get('wishlist_id'), $arrIds ) ) {

            $arrIds[] = \Input::get('wishlist_id');
        }

        if ( \Input::get('wishlist_id') ) {

            $arrAmounts[ \Input::get('wishlist_id') ] = \Input::get('wishlist_amount') ? \Input::get('wishlist_amount') : '1';
        }

        return [

            'table' => \Input::get('wishlist_table'),
            'amounts' => $arrAmounts,
            'ids' => $arrIds
        ];
    }
}