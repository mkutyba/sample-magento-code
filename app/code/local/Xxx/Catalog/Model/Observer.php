<?php

class Xxx_Catalog_Model_Observer
{
    public function setCurrentCategory(Varien_Event_Observer $observer)
    {
        $action = $observer->getAction();
        if ($action->getFullActionName() != 'catalog_product_view') {
            return;
        }
        if (Mage::app()->getRequest()->getParam('c', 0)) {
            return;
        }

        $currentProduct = Mage::registry('current_product');

        if (!$currentProduct) {
            return;
        }

        $catIds = $currentProduct->getCategoryIds();

        if (!is_array($catIds) || !count($catIds)) {
            return;
        }

        $category = $this->_getHighestLevelCategory($catIds);

        if ($category && $category->getId()) {
            Mage::unregister('current_category');
            Mage::register('current_category', $category);
        }
    }

    protected function _getHighestLevelCategory($catIds)
    {
        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->addAttributeToFilter('entity_id', array('in' => $catIds));
        $collection->setOrder('level', 'desc');
        if ($first = $collection->getFirstItem()) {
            return $first;
        }

        return false;
    }
    
    /**
     * change product price for mtm products (made to measure)
     *
     * @param Varien_Event_Observer $observer
     */
    public function mtmProductModifyAddToCart(Varien_Event_Observer $observer)
    {
        $item = $observer->getQuoteItem();
        $this->_mtmProductModifyPrice($item);
    }

    /**
     * change product price for mtm products (made to measure)
     *
     * @param Varien_Event_Observer $observer
     */
    public function mtmProductModifyUpdateItem(Varien_Event_Observer $observer)
    {
        $item = $observer->getItem();
        $this->_mtmProductModifyPrice($item);
    }

    protected function _mtmProductModifyPrice($item)
    {
        // Ensure we have the parent item, if it has one
        $item = ($item->getParentItem() ? $item->getParentItem() : $item);

        $price = $this->_getPriceByItem($item);

        if (!$price) return;

        $item->setCustomPrice($price);
        $item->setOriginalCustomPrice($price);
    }

    protected function _getGraphicPrice($buyRequest)
    {
        $price = 0;
        $graphicCategory = (isset($buyRequest['dnr_graphic_category']) ? $buyRequest['dnr_graphic_category'] : null);
        $graphicSource = (isset($buyRequest['dnr_graphic_source']) ? $buyRequest['dnr_graphic_source'] : null);
        $graphicId = (isset($buyRequest['dnr_graphic_id']) ? $buyRequest['dnr_graphic_id'] : null);

        if ($graphicSource == 'fotolia') {
            /** @var Xxx_Fotolia_Helper_Data $helper */
            $helper = Mage::helper('fotolia');
            $fotoliaPrice = (
                $helper->getMediaCost($graphicId, 'V') ? $helper->getMediaCost($graphicId, 'V') : (
                    $helper->getMediaCost($graphicId, 'XXL') ? $helper->getMediaCost($graphicId, 'XXL') : $helper->getMediaCost($graphicId, 'XL')
                )
            );
            if ($fotoliaPrice) {
                $price = $fotoliaPrice * 3.5;
            }
        }

        return $price;
    }

    protected function _getPriceByItem(Mage_Sales_Model_Quote_Item $item)
    {
        $product = $item->getProduct();
        if (!$product->hasMtmPricelist() || $product->getMtmPricelist() == '') {
            return false;
        }

        $pricelist = $product->getAttributeText('mtm_pricelist');
        $helper = Mage::helper('catalog/product_configuration');
        $options = $helper->getCustomOptions($item);
        $options = array_column($options, 'value', 'option_type');

        $model = Mage::getModel("xxx_catalog/pricelist");
        $width = $options['width'];
        $height = $options['height'];
        $buyRequest = $product->getCustomOption('info_buyRequest')->getValue();
        $buyRequest = unserialize($buyRequest);

        $mtmParameters = [];

        foreach ($buyRequest as $key => $value) {
            if (strpos($key, 'mtm_') === 0) {
                $mtmParameters[] = $value;
            }
        }

        $price = 0;
        $price += $this->_getGraphicPrice($buyRequest);

        if (count($mtmParameters) > 0) {
            $paramsChain = implode('-', $mtmParameters);
            $price += $model->getPriceForSize($pricelist, $width, $height, $paramsChain);
            return $price;
        }

        // in case of wrong (incomplete) pricelist loaded by user, prevent setting product price to 0
        $price = $price > 0 ? $price : 999.99;
        return $price;
    }


    protected function _getPrintedProductInCartThumbnail($item)
    {
        $item = ($item->getParentItem() ? $item->getParentItem() : $item);

        if (!$item) return false;

        $product = $item->getProduct();
        $buyRequest = $product->getCustomOption('info_buyRequest')->getValue();
        $buyRequest = unserialize($buyRequest);
        $imageFile = (isset($buyRequest['dnr_image_processed_file']) ? $buyRequest['dnr_image_processed_file'] : (isset($buyRequest['dnr_image_file']) ? $buyRequest['dnr_image_file'] : null));

        return $imageFile;
    }

}
