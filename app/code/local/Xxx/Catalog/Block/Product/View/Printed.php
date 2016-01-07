<?php

class Xxx_Catalog_Block_Product_View_Printed extends Mage_Catalog_Block_Product_View_Abstract
{
    const COLLECTIONS_ID = 1468;

    /**
     * @param $categoryId
     * @return mixed returns collection of subcategories containing print patterns
     */
    public function getSubcategoriesCollection($categoryId)
    {
        $categories = Mage::getModel('catalog/category')->load($categoryId)->getChildrenCategories();

        /** @var Mage_Catalog_Model_Category $model */
        $model = Mage::getModel('catalog/category');
        $categories = $model
            ->getCollection(true)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('description')
            ->addAttributeToSelect('thumbnail')
            ->addAttributeToSelect('fotolia_thumb')
            ->setOrder('position', Varien_Data_Collection::SORT_ORDER_ASC)
            ->addIdFilter(array_keys($categories));

        return $categories;
    }

    /**
     * @return mixed collection of subcategories containing print patterns, from master category
     */
    public function getCollectionsCategories()
    {
        return $this->getSubcategoriesCollection(self::COLLECTIONS_ID);
    }

    /**
     * @param $categoryId
     * @return mixed
     */
    public function getProductsFromCategory($categoryId)
    {
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->setStoreId($this->getStoreId())
            ->addCategoryFilter(Mage::getModel('catalog/category')->load($categoryId));

        return $collection;
    }
}
