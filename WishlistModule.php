<?php

namespace CMWishlist;

use CatalogManager\CatalogController;
use CatalogManager\Toolkit;

class WishlistModule extends CatalogController
{

    protected $blnPersist = false;

    protected $strTable = '';
    protected $intColIndex = 0;
    protected $blnUseWishlist = false;

    public function __construct()
    {

        parent::__construct();
        $this->import('Database');
    }

    public function initialize(&$objCatalogView)
    {

        $this->strTable = $objCatalogView->catalogTablename;
        $this->blnUseWishlist = (bool) $objCatalogView->wishlistWidget;
        $this->blnPersist = (bool) $objCatalogView->wishlistPersistStorage;

        $objCatalogView->objMainTemplate->useWishlist = $this->blnUseWishlist;
        $objCatalogView->objMainTemplate->wishlistLabel = $GLOBALS['TL_LANG']['MSC']['CATALOG_MANAGER']['wishlist'];
        $objCatalogView->objMainTemplate->wishlistCss = $objCatalogView->wishlistEnableFilter ? ' filtered-wishlist' : '';

        if ($this->blnUseWishlist) {

            $GLOBALS['TL_JAVASCRIPT']['wishlistJs'] = 'system/modules/catalog-manager-wishlist/assets/wishlist.js';
        }

        if ($this->blnUseWishlist && \Input::get('wishlist_type')) {

            if (!$this->validateInput()) return null;

            $objStorage = new \CMWishlist\Storage($this->blnPersist);
            $arrTables = $objStorage->getTables();

            if (!is_array($arrTables)) $arrTables = [];

            if ($this->strTable && !in_array($this->strTable, $arrTables)) {

                $arrTables[] = $objCatalogView->catalogTablename;
            }

            $objStorage->setTables($arrTables);

            switch (\Input::get('wishlist_type')) {

                case 'add_to_wishlist':

                    $this->addToWishlist();
                    $this->request($objCatalogView);

                    break;

                case 'remove_from_wishlist':

                    $this->removeFromWishlist();
                    $this->request($objCatalogView);

                    break;
            }
        }
    }

    public function renderCatalog(&$arrCatalog, $strTablename, $objCatalogView)
    {

        if ($this->blnUseWishlist) {

            $strCss = '';
            $this->intColIndex++;
            $strAmountValue = '1';
            $blnInWishlist = false;
            $this->blnPersist = (bool) $objCatalogView->wishlistPersistStorage;

            $objStorage = new \CMWishlist\Storage($this->blnPersist);
            $arrSession = $objStorage->getByTable($strTablename);

            if (!Toolkit::isEmpty($arrSession)) {
                if (isset($arrSession['amounts']) && isset($arrSession['amounts'][$arrCatalog['id']]) && $arrSession['amounts'][$arrCatalog['id']]) {
                    $strAmountValue = $arrSession['amounts'][$arrCatalog['id']];
                }

                if (isset($arrSession['ids']) && in_array($arrCatalog['id'], $arrSession['ids'])) {
                    $blnInWishlist = true;
                    $strCss .= ' added-to-wishlist';
                }
            }

            $arrCatalog['wishlistCss'] = $strCss;
            $arrCatalog['isInWishlist'] = $blnInWishlist;
            $arrCatalog['wishlistAmountValue'] = $strAmountValue;

            $arrCatalog['wishlistAddButton'] = true;
            $arrCatalog['wishlistTable'] = $strTablename;
            $arrCatalog['wishlistIndex'] = $this->intColIndex;
            $arrCatalog['useWishlist'] = $this->blnUseWishlist;
            $arrCatalog['wishlistID'] = md5($arrCatalog['id'] . $strTablename);
            $arrCatalog['wishlistAmount'] = (bool) $objCatalogView->wishlistAmount;
            $arrCatalog['wishlistDisableRemoveButton'] = (bool) $objCatalogView->wishlistDisableRemoveButton;
            $arrCatalog['wishlistPersistStorage'] = $this->blnPersist;

            if ($blnInWishlist && !$arrCatalog['wishlistAmount']) $arrCatalog['wishlistAddButton'] = false;

            $arrCatalog['wishlistAmountLabel'] = $GLOBALS['TL_LANG']['MSC']['CATALOG_MANAGER']['wishlistAmount'];
            $arrCatalog['wishlistAddDeleteLabel'] = $GLOBALS['TL_LANG']['MSC']['CATALOG_MANAGER']['wishlistDeleteButton'];
            $arrCatalog['wishlistAddButtonLabel'] = $blnInWishlist ?
                $GLOBALS['TL_LANG']['MSC']['CATALOG_MANAGER']['wishlistUpdateButton'] :
                $GLOBALS['TL_LANG']['MSC']['CATALOG_MANAGER']['wishlistAddButton'];

            $strTemplate = 'wishlist_form_list';

            if ($objCatalogView->enableTableView) {
                $strTemplate = 'wishlist_form_table';
            }

            $objTemplate = new \FrontendTemplate($strTemplate);
            $objTemplate->setData($arrCatalog);

            $arrCatalog['wishlistForm'] = $objTemplate->parse();
        }
    }

    public function setQuery($arrQuery, $objCatalogView)
    {

        if (!$objCatalogView->wishlistEnableFilter) return $arrQuery;

        $this->blnPersist = (bool) $objCatalogView->wishlistPersistStorage;

        $objStorage = new \CMWishlist\Storage($this->blnPersist);
        $arrSession = $objStorage->getByTable($objCatalogView->catalogTablename);

        if (Toolkit::isEmpty($arrSession)) $arrSession = ['ids' => ['0']];
        if (!isset($arrSession['ids']) || empty($arrSession['ids'])) $arrSession['ids'] = ['0'];

        $arrQuery['where'][] = [
            'field' => 'id',
            'operator' => 'contain',
            'value' => $arrSession['ids']
        ];

        return $arrQuery;
    }

    protected function addToWishlist()
    {

        $objStorage = new \CMWishlist\Storage($this->blnPersist);
        $objStorage->setData($this->strTable, $this->getWishlistData());
    }

    protected function removeFromWishlist()
    {

        $objStorage = new \CMWishlist\Storage($this->blnPersist);
        $arrSession = $objStorage->getByTable($this->strTable);

        if (!Toolkit::isEmpty($arrSession)) {

            if (isset($arrSession['amounts'])) {
                unset($arrSession['amounts'][\Input::get('wishlist_id')]);
            }

            if (is_array($arrSession['ids']) && in_array(\Input::get('wishlist_id'), $arrSession['ids'])) {
                $intKey = array_search(\Input::get('wishlist_id'), $arrSession['ids']);
                unset($arrSession['ids'][$intKey]);
                $objStorage->removeData($this->strTable, \Input::get('wishlist_id'));
            }

            $objStorage->setData($this->strTable, $arrSession);
        }
    }

    protected function request($objCatalogView)
    {

        if (\Input::get('wishlist_ajax')) {

            $objEntity = $this->Database->prepare(sprintf('SELECT * FROM %s WHERE id=?', $this->strTable))->limit(1)->execute(\Input::get('wishlist_id'));
            $arrCatalog = $objEntity->row();

            $this->renderCatalog($arrCatalog, $this->strTable, $objCatalogView);

            header('Content-Type: application/json');

            echo json_encode([
                'id' => md5(\Input::get('wishlist_id') . $this->strTable),
                'reload' => $arrCatalog['wishlistForm']
            ], 512);

            exit;
        }

        $strRedirect = preg_replace('/[&,?]wishlist_type=remove_from_wishlist/', '', \Environment::get('request'));
        $strRedirect = preg_replace('/[&,?]wishlist_type=add_to_wishlist/', '', $strRedirect);
        $strRedirect = preg_replace('/[&,?]wishlist_amount=[^&]*/i', '', $strRedirect);
        $strRedirect = preg_replace('/[&,?]wishlist_table=[^&]*/i', '', $strRedirect);
        $strRedirect = preg_replace('/[&,?]wishlist_ajax=[^&]*/i', '', $strRedirect);
        $strRedirect = preg_replace('/[&,?]wishlist_id=[^&]*/i', '', $strRedirect);

        \Controller::redirect($strRedirect);
    }

    protected function validateInput()
    {

        if (!$this->Database->tableExists($this->strTable)) return false;

        $objRow = $this->Database->prepare(sprintf('SELECT id FROM %s WHERE id = ?', $this->strTable))->execute(\Input::get('wishlist_id'));

        return (bool) $objRow->numRows;
    }

    protected function getWishlistData()
    {

        $arrIds = [];
        $arrAmounts = [];
        $objStorage = new \CMWishlist\Storage($this->blnPersist);
        $arrSession = $objStorage->getByTable($this->strTable);

        if (!Toolkit::isEmpty($arrSession)) {

            $arrIds = $arrSession['ids'];
            $arrAmounts = $arrSession['amounts'];
        }

        if (\Input::get('wishlist_id') && !in_array(\Input::get('wishlist_id'), $arrIds)) {

            $arrIds[] = \Input::get('wishlist_id');
        }

        if (\Input::get('wishlist_id')) {

            $arrAmounts[\Input::get('wishlist_id')] = \Input::get('wishlist_amount') ? \Input::get('wishlist_amount') : '1';
        }

        return [
            'table' => $this->strTable,
            'amounts' => $arrAmounts,
            'ids' => $arrIds
        ];
    }
}