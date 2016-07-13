<?php

class Xxx_Catalog_Block_Product_View_Printed extends Mage_Catalog_Block_Product_View_Abstract
{
    public function getSubcategoriesCollection($categoryId)
    {
        $cats = Mage::getModel('catalog/category')->load($categoryId)->getChildrenCategories();

        $cats = Mage::getModel('catalog/category')
            ->getCollection(true)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('description')
            ->addAttributeToSelect('thumbnail')
            ->addAttributeToSelect('fotolia_thumb')
            ->setOrder('position', Varien_Data_Collection::SORT_ORDER_ASC)
            ->addIdFilter(array_keys($cats));

        return $cats;
    }

    public function getProductsFromCategory($categoryId)
    {
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->setStoreId($this->getStoreId())
            ->addCategoryFilter(Mage::getModel('catalog/category')->load($categoryId));

        return $collection;
    }

    public function getVisualizationParams($pricelist)
    {
        return Mage::helper('xxx_catalog')->getVisualizationParams($pricelist);
    }

    public function getCurrentImage()
    {
        return Mage::helper('xxx_catalog')->getCurrentImage();
    }

    public function getCurrentCategory()
    {
        return Mage::helper('xxx_catalog')->getCurrentCategory();
    }

    public function getCurrentImageSource()
    {
        return Mage::helper('xxx_catalog')->getCurrentImageSource();
    }

    public function getCurrentDnrQuery()
    {
        return Mage::helper('xxx_catalog')->getCurrentDnrQuery();
    }

    public function getCurrentCategoryName()
    {
        return Mage::helper('xxx_catalog')->getCurrentCategoryName();
    }

    public function getCropConfig()
    {
        if ($params = Mage::registry('dnr_crop_config')) {
            return $params;
        }
        return [];
    }
}
