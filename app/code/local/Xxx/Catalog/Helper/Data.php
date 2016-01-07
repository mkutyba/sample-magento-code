<?php

class Xxx_Catalog_Helper_Data
{
    /**
     * get product two images to be used in listing
     */
    public function getProductTwoImages($product)
    {
        if ($product->getFotoliaThumb()) {
            return array($product->getFotoliaThumb());
        }
        // it is needed to reload product, because not all attributes are available
        $product = Mage::getModel('catalog/product')->load($product->getId());
        $output = array();
        $output[] = $product->getSmallImage();

        // if small_image2 is set - use it
        if ($product->getSmallImage2() != 'no_selection' && $product->getSmallImage2() && $product->getSmallImage2() != $product->getSmallImage()) {
            $output[] = $product->getSmallImage2();
        } else {
            $_media = $product->getMediaGalleryImages();
            foreach ($_media as $image) {
                if ($image->getFile() != $product->getSmallImage()) {
                    $output[] = $image->getFile();
                    break;
                }
            }
        }
        return $output;
    }

    /**
     * get category path in readable form
     */
    public function getCurrentCategoryPath()
    {
        if (is_string(Mage::registry('current_category_path'))) {
            return Mage::registry('current_category_path');
        }
        if (Mage::registry('current_category')) {
            $category = Mage::registry('current_category')->getName();

            $breadcrumbs = Mage::app()->getLayout()->getBlock('breadcrumbs');
            $cacheKeyInfo = $breadcrumbs->getCacheKeyInfo();
            $breadcrumbs = unserialize(base64_decode($cacheKeyInfo['crumbs']));
            if (is_array($breadcrumbs)) {
                $breadcrumbs = array_column($breadcrumbs, 'label');
                array_shift($breadcrumbs);
                array_pop($breadcrumbs);
                $category = implode("/", $breadcrumbs);
            }
            Mage::unregister('current_category_path');
            Mage::register('current_category_path', $category);
            return $category;
        }
        return false;
    }

}
