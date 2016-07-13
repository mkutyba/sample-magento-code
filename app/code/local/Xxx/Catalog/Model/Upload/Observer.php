<?php

class Xxx_Catalog_Model_Upload_Observer
{
    public function attachUploadedFilesToCustomerStep1(Varien_Event_Observer $observer)
    {
        $oldOwner = Mage::getSingleton('customer/session')->getSessionId();
        Mage::unregister('upload_old_owner');
        Mage::register('upload_old_owner', $oldOwner);
    }

    public function attachUploadedFilesToCustomerStep2(Varien_Event_Observer $observer)
    {
        $oldOwner = Mage::registry('upload_old_owner');
        $newOwner = Mage::getSingleton('customer/session')->getCustomerId();
        $uploads = Mage::getModel('xxx_catalog/upload')->getCollection()->addFieldToFilter('owner', $oldOwner);
        if ($uploads->getSize()) {
            foreach ($uploads as $upload) {
                $upload->setOwner($newOwner)->save();
            }
        }
        Mage::unregister('upload_old_owner');
    }
}
