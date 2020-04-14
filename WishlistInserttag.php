<?php

namespace CMWishlist;

class WishlistInserttag extends \Frontend {

    public function getInsertTagValue($strTag) {
        $arrTables = [];
        $arrTags = explode('::', $strTag);

        if ( empty( $arrTags ) || !is_array( $arrTags ) ) {
            return false;
        }

        if ( isset( $arrTags[0] ) && $arrTags[0] == 'WISHLIST' ) {

            $objWishlistView = new WishlistView();
            $arrChunks = explode('?', urldecode( $arrTags[2] ), 2 );
            $strSource = \StringUtil::decodeEntities( $arrChunks[1] );
            $strSource = str_replace( '[&]', '&', $strSource );
            $arrParams = explode( '&', $strSource );

            foreach ( $arrParams as $strParam ) {

                list($strKey, $strOption) = explode('=', $strParam);
                switch ( $strKey ) {
                    case 'tables':
                        $arrOnlyTables = explode( ',', $strOption );
                        if ( !empty( $arrOnlyTables ) && is_array( $arrOnlyTables ) ) {
                            $arrTables = $arrOnlyTables;
                        }
                        break;
                }
            }

            if ( is_array( $arrTables ) && !empty( $arrTables ) ) {
                $objWishlistView->setExplicit( $arrTables );
            }

            return $objWishlistView->render();
        }

        if ( isset( $arrTags[0] ) && $arrTags[0] == 'WISHLIST_AMOUNT' && $arrTags[1] ) {
            $numReturn = 0;
            $objSession = \Session::getInstance();
            $arrTables = $objSession->get( 'wishlist_tables' );

            if ( !is_array( $arrTables ) ) $arrTables = [];
            if ( in_array( $arrTags[1], $arrTables ) ) {
                $arrValue = $objSession->get( 'wishlist_' . $arrTags[1] );
                if ( isset( $arrValue['ids'] ) ) {
                    $numReturn = count( $arrValue['ids'] );
                }
            }
            return $numReturn;
        }

        return false;
    }
}