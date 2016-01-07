<?php

class Xxx_CrosssellExtended_Block_Checkout_Cart_Crosssell extends Mage_Checkout_Block_Cart_Crosssell
{
    /**
     * Get crosssell items
     *
     * @return array
     */
    public function getItems()
    {
        if (count(parent::getItems())) {
            return parent::getItems();
        }

        $items = $this->getData('items');
        if (is_null($items) || (is_array($items) && !count($items))) {
            $items = array();

            $showDefaultProducts = Mage::getStoreConfig('catalog/crosssell/show_default_products');
            if ($showDefaultProducts) {
                $defaultProducts = trim(Mage::getStoreConfig('catalog/crosssell/default_products'));
                $defaultProducts = preg_replace('/\s+/', '', $defaultProducts);
                if ($defaultProducts) {
                    $defaultProducts = explode(',', $defaultProducts);
                    if (count($defaultProducts)) {
                        $randomOrder = Mage::getStoreConfig('catalog/crosssell/show_random');
                        $collection = $this->_getDefaultCollection($defaultProducts, $randomOrder);
                        foreach ($collection as $item) {
                            $items[] = $item;
                        }
                    }
                }
            }

            $this->setData('items', $items);
        }
        return $items;
    }

    /**
     * Get default products collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link_Product_Collection
     */
    protected function _getDefaultCollection($defaultProducts, $randomOrder)
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addStoreFilter()
            ->setPageSize($this->_maxItemCount)
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('entity_id', array(
                'in' => $defaultProducts,
            ));
        $this->_addProductAttributesAndPrices($collection);

        if ($randomOrder) {
            $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
        } else {
            $collection->getSelect()->order(new Zend_Db_Expr('FIELD(e.entity_id, ' . implode(',', $defaultProducts) . ')'));
        }

        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);

        return $collection;
    }
}