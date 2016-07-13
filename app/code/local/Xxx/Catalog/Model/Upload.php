<?php
/**
 * @method int getUploadId()
 * @method Xxx_Catalog_Model_Upload setUploadId(int $value)
 * @method string getFilename()
 * @method Xxx_Catalog_Model_Upload setFilename(string $value)
 * @method string getMd5()
 * @method Xxx_Catalog_Model_Upload setMd5(string $value)
 * @method string getOwner()
 * @method Xxx_Catalog_Model_Upload setOwner(string $value)
 */
class Xxx_Catalog_Model_Upload extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('xxx_catalog/upload');
    }

}