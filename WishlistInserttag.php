<?php

namespace CMWishlist;

class WishlistInserttag extends \Frontend {

    protected function getSession() {

        if (version_compare('4.4', VERSION, '<=')) {
            return \System::getContainer()->get('session');
        }
        return \Session::getInstance();
    }

    public function getInsertTagValue($strTag) {

        $arrTables = [];
        $arrTags = explode('::', $strTag);

        if ( empty($arrTags) || !is_array($arrTags)) {
            return false;
        }

        if (isset($arrTags[0]) && $arrTags[0] == 'WISHLIST') {

            $arrSettings = [
                'noJoins' => false,
                'noParentJoin' => false,
                'template' => ''
            ];

            $objWishlistView = new WishlistView();
            $arrChunks = explode('?', urldecode($arrTags[2]??''), 2);
            $strSource = \StringUtil::decodeEntities($arrChunks[1]??'');
            $strSource = str_replace( '[&]', '&', $strSource );
            $arrParams = explode('&', $strSource);

            if (!empty(array_filter($arrParams))) {
                foreach ($arrParams as $strParam) {
                    list($strKey, $strOption) = explode('=', $strParam);
                    switch ($strKey) {
                        case 'tables':
                            $arrOnlyTables = explode(',', $strOption);
                            if (!empty($arrOnlyTables) && is_array($arrOnlyTables)) {
                                $arrTables = $arrOnlyTables;
                            }
                            break;
                        case 'template':
                            $arrSettings['template'] = $strOption;
                            break;
                        case 'noJoins':
                            $arrSettings['noJoins'] = $strOption ? true : false;
                            break;
                        case 'noParentJoin':
                            $arrSettings['noParentJoin'] = $strOption ? true : false;
                            break;
                    }
                }
            }

            if (!empty($arrTables)) {
                $objWishlistView->setExplicit($arrTables);
            }

            return $objWishlistView->render($arrSettings);
        }

        if ( isset( $arrTags[0] ) && $arrTags[0] == 'WISHLIST_AMOUNT' && $arrTags[1] ) {
            $numReturn = 0;
            $objSession = $this->getSession();
            $arrTables = $objSession->get('wishlist_tables');

            if (!\Database::getInstance()->tableExists($arrTags[1])) {
                return $numReturn;
            }

            if (!is_array($arrTables)) $arrTables = [];
            if (in_array($arrTags[1], $arrTables)) {
                $arrValue = $objSession->get( 'wishlist_' . $arrTags[1]);
                if (isset($arrValue['ids'])) {
                    foreach ($arrValue['ids'] as $strId) {
                        if (\Database::getInstance()->prepare('SELECT * FROM ' . $arrTags[1] . ' WHERE id=? AND invisible!=?')->limit(1)->execute($strId, '1')->numRows) {
                            $numReturn = $numReturn + 1;
                        }
                    }
                }
            }
            return $numReturn;
        }

        return false;
    }
}