<?php

class Xxx_Catalog_Block_Product_List_Similar extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_noform';

    protected $_itemCollection;

    /**
     * prepare similar products collection, based on attribute 'textile_collection'
     */
    protected function _prepareData()
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('product');
        $currentCategory = Mage::registry('current_category');
        $textileCollection = $product->getTextileCollection();

        $this->_itemCollection = false;

        $collection2 = Mage::getModel('catalog/product')->getCollection();
        $collection2
            ->setPageSize(20)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter("textile_collection", array('eq' => $textileCollection));

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection2);

        if ($currentCategory) {
            $collection1 = $currentCategory->getProductCollection();
            $collection1
                ->setPageSize(20)
                ->addAttributeToSelect('*')
                ->addAttributeToFilter("textile_collection", array('eq' => $textileCollection));
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection1);

            $mergedIds = array_merge($collection1->getAllIds(), $collection2->getAllIds());

            if (!empty($mergedIds)) {
                $mergedCollection = Mage::getResourceModel('catalog/product_collection')
                    ->addFieldToFilter('entity_id', $mergedIds)
                    ->addAttributeToSelect('*')
                    ->setPageSize(20);

                $this->_itemCollection = $mergedCollection;
            } else {
                $this->_itemCollection = $collection2;
            }

        } else {
            $this->_itemCollection = $collection2;
        }

        $this->_itemCollection->addFieldToFilter('entity_id', array('neq' => $product->getId()));
        $this->_itemCollection->load();

        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    public function getItems()
    {
        return $this->_itemCollection;
    }

    /**
     * Get tags array for saving cache
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(parent::getCacheTags(), $this->getItemsTags($this->getItems()));
    }
}
