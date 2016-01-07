<?php

class Xxx_Catalog_Model_Pricelist extends Mage_Core_Model_Abstract
{
    protected $_pricelistCode = null;
    protected $_groupId = null;

    protected function _construct()
    {
        $this->_init("xxx_catalog/pricelist");
    }

    protected function _getPricelistCollection()
    {
        $pricelistCode = explode('-', $this->_pricelistCode);
        $groupName = $pricelistCode[0];
        $groupId = (sizeof($pricelistCode) > 1) ? $pricelistCode[1] : null;

        /** @var Xxx_Catalog_Model_Mysql4_Pricelist_Collection $collection */
        $collection = $this->getCollection();

        $collection
            ->addFieldToFilter('group_name', array('eq' => $groupName))
            ->setOrder('width', 'ASC')
            ->setOrder('height', 'ASC');

        if (!is_null($groupId)) {
            $collection->addFieldToFilter('group_id', array('eq' => $groupId));
        }
        if (!is_null($this->_groupId)) {
            $collection->addFieldToFilter('group_id', array('eq' => $this->_groupId));
        }

        return $collection;
    }

    public function getPricelist($pricelistCode, $groupId = null)
    {
        $this->_pricelistCode = $pricelistCode;
        $this->_groupId = $groupId;

        $collection = $this->_getPricelistCollection();

        $pricelistArray = $collection->getData();

        return $pricelistArray;
    }

    public function getPriceForSize($pricelistCode, $width, $height, $groupId = null)
    {
        $this->_pricelistCode = $pricelistCode;
        $this->_groupId = $groupId;

        $collection = $this->_getPricelistCollection();

        $collection
            ->addFieldToFilter('width', array('gteq' => floatval($width)))
            ->addFieldToFilter('height', array('gteq' => floatval($height)))
            ->setOrder('width', 'ASC')
            ->setOrder('height', 'ASC')
            ->setPageSize(1);

        return $collection->getFirstItem()->getPrice();

    }

    public function getDistinctGroupId($pricelistCode)
    {
        $this->_pricelistCode = $pricelistCode;
        $collection = $this->_getPricelistCollection();

        $collection
            ->distinct(true)
            ->addFieldToSelect('group_id');

        return $collection->getData();
    }
}
