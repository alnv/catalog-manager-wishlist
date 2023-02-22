<?php

namespace CMWishlist;

class Storage
{

    protected $strUserId = null;
    protected $blnPersist = false;

    public function __construct($blnPersist = false)
    {

        $this->strUserId = \FrontendUser::getInstance()->id;
        $this->blnPersist = $blnPersist && $this->strUserId;
    }

    public function getTables()
    {
        $arrTables = [];

        switch ($this->blnPersist) {
            case true:

                $objUser = \FrontendUser::getInstance();
                $objDatabase = \Database::getInstance()->prepare('SELECT DISTINCT `table` FROM tl_catalog_wishlist WHERE pid=?')->execute($objUser->id);
                while ($objDatabase->next()) {
                    if (!in_array($objDatabase->table, $arrTables)) {
                        $arrTables[] = $objDatabase->table;
                    }
                }

                break;
            case false:
                $objSession = $this->getSession();
                $arrTables = $objSession->get('wishlist_tables') ?: [];
                break;
        }

        return $arrTables;
    }

    public function getByTable($strTable)
    {

        $arrData = [];

        switch ($this->blnPersist) {
            case true:

                $objUser = \FrontendUser::getInstance();
                $objEntities = \Database::getInstance()
                    ->prepare('SELECT * FROM tl_catalog_wishlist WHERE pid=? AND `table`=? ORDER BY created_at')
                    ->execute($objUser->id, $strTable);

                $arrData = [
                    'ids' => [],
                    'amounts' => [],
                    'table' => $strTable
                ];

                while ($objEntities->next()) {

                    if (in_array($objEntities->identifier, $arrData['ids'])) {
                        continue;
                    }

                    $arrData['amounts'][$objEntities->identifier] = $objEntities->units;
                    $arrData['ids'][] = $objEntities->identifier;
                }

                break;
            case false:
                $objSession = $this->getSession();
                $arrData = $objSession->get('wishlist_' . $strTable) ?: [];
                break;
        }

        return $arrData;
    }

    public function removeData($strTable, $strIdentifier) {

        if (!$this->blnPersist) {
            return null;
        }

        $objUser = \FrontendUser::getInstance();
        \Database::getInstance()
            ->prepare('DELETE FROM tl_catalog_wishlist WHERE pid=? AND `table`=? AND `identifier`=?')
            ->execute($objUser->id, $strTable, $strIdentifier);
    }

    public function setData($strTable, $arrData) {

        switch ($this->blnPersist) {
            case true:

                $objUser = \FrontendUser::getInstance();
                $arrIds = $arrData['ids'] ?? [];

                foreach ($arrIds as $strId) {

                    $objEntity = \Database::getInstance()
                        ->prepare('SELECT * FROM tl_catalog_wishlist WHERE pid=? AND `table`=? AND `identifier`=?')
                        ->limit(1)
                        ->execute($objUser->id, $strTable, $strId);

                    if ($objEntity->numRows) {

                        \Database::getInstance()
                            ->prepare('UPDATE tl_catalog_wishlist %s WHERE id=?')
                            ->set([
                                'tstamp' => time(),
                                'units' => (int) $arrData['amounts'][$strId] ?? 1
                            ])
                            ->limit(1)
                            ->execute($objEntity->id);
                    } else {

                        \Database::getInstance()->prepare('INSERT INTO tl_catalog_wishlist %s')->set(
                            [
                                'tstamp' => time(),
                                'created_at' => time(),
                                'units' => (int) $arrData['amounts'][$strId] ?? 1,
                                'table' => $strTable,
                                'pid' => $objUser->id,
                                'identifier' => $strId
                            ]
                        )->execute();
                    }
                }

                break;
            case false:
                $objSession = $this->getSession();
                $objSession->set('wishlist_' . $strTable, $arrData);
                break;
        }
    }

    public function setTables($arrTables) {

        switch ($this->blnPersist) {
            case true:
                //
                break;
            case false:
                $objSession = $this->getSession();
                $objSession->set('wishlist_tables', $arrTables);
                break;
        }
    }

    protected function getSession()
    {

        return \System::getContainer()->get('session');
    }
}