<?php

namespace CMWishlist;

class WishlistInserttag extends \Frontend {


    public function getInsertTagValue( $strTag ) {

        $arrTables = [];
        $arrTags = explode( '::', $strTag );

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

            if ( is_array( $arrTables ) && !empty( $arrTables ) ) $objWishlistView->setExplicit( $arrTables );

            return $objWishlistView->render();
        }

        return false;
    }
}