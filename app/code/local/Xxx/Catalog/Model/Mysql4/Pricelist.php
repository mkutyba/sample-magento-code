<?php

class Xxx_Catalog_Model_Mysql4_Pricelist extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("xxx_catalog/pricelist", "pricelist_id");
    }

}