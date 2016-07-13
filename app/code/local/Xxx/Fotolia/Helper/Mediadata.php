<?php

class Xxx_Fotolia_Helper_Mediadata extends Mage_Core_Helper_Abstract
{
    protected $_fotoliaCategory1Id = false;
    protected $_fotoliaCategory2Id = false;
    protected $_fotoliaSearchQuery = false;
    protected $_fotoliaContentType = false;
    protected $_fotoliaWithPeople = false;
    protected $_fotoliaOrientation = false;
    protected $_fotoliaIsolated = false;
    protected $_fotoliaColors = false;
    protected $_fotoliaGalleryId = false;
    protected $_fotoliaIdsList = false;

    public function getFotoliaCategoryParams(Mage_Catalog_Model_Category $category)
    {
        $this->_fotoliaCategory1Id = $category->getFotoliaCategory1Id();
        $this->_fotoliaCategory2Id = $category->getFotoliaCategory2Id();
        $this->_fotoliaSearchQuery = $category->getFotoliaSearchQuery();
        $this->_fotoliaContentType = Mage::getSingleton('eav/config')->getAttribute('catalog_category', 'fotolia_content_type')->getSource()->getOptionText($category->getData('fotolia_content_type'));
        $this->_fotoliaWithPeople = Mage::getSingleton('eav/config')->getAttribute('catalog_category', 'fotolia_with_people')->getSource()->getOptionText($category->getData('fotolia_with_people'));
        $this->_fotoliaOrientation = Mage::getSingleton('eav/config')->getAttribute('catalog_category', 'fotolia_orientation')->getSource()->getOptionText($category->getData('fotolia_orientation'));
        $this->_fotoliaIsolated = Mage::getSingleton('eav/config')->getAttribute('catalog_category', 'fotolia_isolated')->getSource()->getOptionText($category->getData('fotolia_isolated'));
        $this->_fotoliaColors = $category->getFotoliaColors();
        $this->_fotoliaGalleryId = false; //todo
        $this->_fotoliaIdsList = $category->getFotoliaIdsList();
    }

    public function getMediaData($params, $query, $limit, $offset) {
        $mediaData = '';

        if (($this->_fotoliaCategory1Id || $this->_fotoliaCategory2Id) && $this->_fotoliaSearchQuery) { // cat id & custom params
            Mage::helper('phpconsole')->log('getMediaFromCategoryAndParamsAndQuery');
            $mediaData = Mage::helper('fotolia')->getMediaFromCategoryAndParamsAndQuery(
                $this->_fotoliaCategory1Id,
                $this->_fotoliaCategory2Id,
                $params, $query, $limit, $offset);
        }

        if (!($this->_fotoliaCategory1Id || $this->_fotoliaCategory2Id) && $this->_fotoliaSearchQuery) {  // custom params
            Mage::helper('phpconsole')->log('getMediaFromParamsAndQuery');
            $mediaData = Mage::helper('fotolia')->getMediaFromParamsAndQuery(
                $params, $query, $limit, $offset);
        }

        if (($this->_fotoliaCategory1Id || $this->_fotoliaCategory2Id) && !$this->_fotoliaSearchQuery) {  // cat id
            if ($query) {
                Mage::helper('phpconsole')->log('getMediaFromCategoryAndQuery with query');
                $mediaData = Mage::helper('fotolia')->getMediaFromCategoryAndQuery(
                    $this->_fotoliaCategory1Id,
                    $this->_fotoliaCategory2Id,
                    $query, $limit, $offset);
            } else {
                Mage::helper('phpconsole')->log('getMediaFromCategoryAndQuery w/o query');
                $mediaData = Mage::helper('fotolia')->getMediaFromCategoryAndQuery(
                    $this->_fotoliaCategory1Id,
                    $this->_fotoliaCategory2Id,
                    null, $limit, $offset);
            }
        }

        if (!($this->_fotoliaCategory1Id || $this->_fotoliaCategory2Id) && !$this->_fotoliaSearchQuery && !$this->_fotoliaIdsList) { // only query - main category (entrance page)
            if ($query) {
                if (Mage::app()->getFrontController()->getAction()->getFullActionName() != 'catalogsearch_result_index'
                    || Mage::app()->getRequest()->getParam('include_graphics', false)) {
                    Mage::helper('phpconsole')->log('getMediaFromQuery');
                    $mediaData = Mage::helper('fotolia')->getMediaFromQuery($query, $limit, $offset);
                }
            }
        }

        return $mediaData;
    }

    public function getMediaDataFromIds($query) {
        $mediaData = '';

        if ($this->_fotoliaIdsList) {
            $mediaData = Mage::helper('fotolia')->getMediaFromIdsList($this->_fotoliaIdsList);

            if ($query) {
                foreach ($mediaData as $key => $value) {
                    $mediaTitleAndKeywords = $value['title'];
                    if (is_array($value['keywords']) || is_object($value['keywords'])) {
                        foreach ($value['keywords'] as $v) {
                            if (is_array($v)) {
                                $mediaTitleAndKeywords .= " " . implode(" ", $v);
                            } else {
                                $mediaTitleAndKeywords .= " " . $v;
                            }
                        }
                    }
                    if (strpos($mediaTitleAndKeywords, $query)===false) {
                        unset($mediaData[$key]);
                    }
                }
            }
        }

        return $mediaData;
    }

    public function prepareFotoliaSearchParams()
    {
        $params = array();
        if ($this->_fotoliaContentType !== false) {
            $params['content_type'] = explode(',', $this->_fotoliaContentType);
        }
        if ($this->_fotoliaSearchQuery) {
            $params['words'] = $this->_fotoliaSearchQuery;
        }
        if ($this->_fotoliaWithPeople !== false) {
            $params['has_releases'] = (bool)$this->_fotoliaWithPeople;
        }
        if ($this->_fotoliaOrientation !== false) {
            $params['orientation'] = $this->_fotoliaOrientation;
        }
        if ($this->_fotoliaIsolated) {
            $params['isolated'] = $this->_fotoliaIsolated;
        }
        if ($this->_fotoliaColors) {
            $params['colors'] = $this->_fotoliaColors;
        }
        if ($this->_fotoliaGalleryId !== false) {
            $params['gallery_id'] = $this->_fotoliaGalleryId;
        }

        return $params;
    }
}
