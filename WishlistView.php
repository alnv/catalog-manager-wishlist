<?php

namespace CMWishlist;

use CatalogManager\CatalogController;
use CatalogManager\Toolkit;

class WishlistView extends CatalogController {

    protected $arrWishlist = [];
    protected $blnExplicit = false;
    protected $arrExplicitTables = [];
    protected $arrWishlistTables = [];


    public function __construct() {

        parent::__construct();

        $this->import( 'SQLQueryBuilder' );
        $this->import( 'CatalogFieldBuilder' );

        $objSession = \Session::getInstance();

        if ( is_array( $objSession->get( 'wishlist_tables' ) ) && !empty( $objSession->get( 'wishlist_tables' ) ) ) {

            $this->arrWishlistTables = $objSession->get( 'wishlist_tables' );
        }

        $this->getWishlistData();
    }


    public function setExplicit( $arrTables ) {

        $arrExplicitTables = [];
        $this->blnExplicit = true;

        foreach ( $arrTables as $strTable ) {

            if ( in_array( $strTable, $this->arrWishlistTables ) ) {

                $arrExplicitTables[] = $strTable;
            }
        }

        $this->arrExplicitTables = $arrExplicitTables;
    }


    public function render() {

        $arrData = [];
        $arrTables = $this->arrWishlistTables;
        $objTemplate = new \FrontendTemplate( 'wishlist_view' );

        if ( !empty( $this->arrExplicitTables ) ) $arrTables = $this->arrExplicitTables;

        foreach ( $arrTables as $strTable ) {

            $this->CatalogFieldBuilder->initialize( $strTable );
            $arrCatalog = $this->CatalogFieldBuilder->getCatalog();
            $arrFields = $this->CatalogFieldBuilder->getCatalogFields( false, $this );

            $arrRow = $this->getRow( $strTable, $arrCatalog, $arrFields );

            if ( is_bool( $arrRow ) && $arrRow === false ) continue;

            $arrData[ $strTable ] = [];
            $arrData[ $strTable ]['rows'] = $arrRow;
            $arrData[ $strTable ]['table'] = $this->getTable( $strTable, $arrCatalog );
        }

        $objTemplate->setData( [ 'data' => $arrData, 'wishlist' => $this->arrWishlist ] );

        return $objTemplate->parse();
    }


    protected function getTable( $strTable, $arrCatalog ) {

        return [

            'table' => $strTable
        ];
    }


    protected function getRow( $strTable, $arrCatalog, $arrFields ) {

        $arrWishlist = $this->arrWishlist[ $strTable ];
        $arrIds = $arrWishlist['ids'];
        $arrQuery = [];

        if ( empty( $arrIds ) ) $arrIds = ['0'];

        $arrQuery['table'] = $strTable;

        if ( is_array( $arrCatalog['operations'] ) && in_array( 'invisible', $arrCatalog['operations'] ) && !BE_USER_LOGGED_IN ) {

            $dteTime = \Date::floorToMinute();

            $arrQuery['where'][] = [

                'field' => 'tstamp',
                'operator' => 'gt',
                'value' => '0'
            ];

            $arrQuery['where'][] = [

                [
                    'value' => '',
                    'field' => 'start',
                    'operator' => 'equal'
                ],

                [
                    'field' => 'start',
                    'operator' => 'lte',
                    'value' => $dteTime
                ]
            ];

            $arrQuery['where'][] = [

                [
                    'value' => '',
                    'field' => 'stop',
                    'operator' => 'equal'
                ],

                [
                    'field' => 'stop',
                    'operator' => 'gt',
                    'value' => $dteTime
                ]
            ];

            $arrQuery['where'][] = [

                'field' => 'invisible',
                'operator' => 'not',
                'value' => '1'
            ];
        }

        $arrQuery['where'][] = [

            'field' => 'id',
            'value' => $arrIds,
            'operator' => 'contain'
        ];

        $arrCatalogs = [];
        $objEntities = $this->SQLQueryBuilder->execute( $arrQuery );

        if ( !$objEntities->numRows ) return false;

        while ( $objEntities->next() ) {

            $arrCatalog = $objEntities->row();
            $arrCatalog['origin'] = $arrCatalog;
            $arrCatalog['wishlistTable'] = $strTable;
            $arrCatalog['wishlistAmountValue'] = $arrWishlist['amounts'][ $arrCatalog['id'] ] ? $arrWishlist['amounts'][ $arrCatalog['id'] ] : '1';
            $arrCatalog = Toolkit::parseCatalogValues( $arrCatalog, $arrFields, true );

            $arrCatalogs[] = $arrCatalog;
        }

        return $arrCatalogs;
    }


    protected function getWishlistData() {

        if ( empty( $this->arrWishlistTables ) ) return null;

        $objSession = \Session::getInstance();

        foreach ( $this->arrWishlistTables as $strTable ) {

            $arrWishlist = $objSession->get( 'wishlist_' . $strTable );

            if ( Toolkit::isEmpty( $arrWishlist ) ) continue;
            if ( is_array( $arrWishlist ) && !isset( $arrWishlist['ids'] ) ) continue;

            $this->arrWishlist[ $strTable ] = $arrWishlist;
        }
    }
}