<?php

class Xxx_Catalog_Model_Mysql4_Upload extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('xxx_catalog/upload', 'upload_id');
    }

}