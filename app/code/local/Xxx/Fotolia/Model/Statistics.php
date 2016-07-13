<?php

class Xxx_Fotolia_Model_Statistics extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init("xxx_fotolia/statistics");
    }

    public function loadByDateAndHour($date, $hour)
    {
        /** @var Xxx_Fotolia_Model_Mysql4_Statistics_Collection $collection */
        $collection = $this->getCollection()->addFieldToFilter('date', $date)->addFieldToFilter('hour', $hour)->setPageSize(1);
        if ($collection->getSize()) {
            return $collection->getFirstItem();
        }
        return $collection->getNewEmptyItem()->setDate($date)->setHour($hour);
    }
}
