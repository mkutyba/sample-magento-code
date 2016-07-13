<?php

class Xxx_YandexTranslate_Model_Resource_Translated extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("yandextranslate/translated", "translated_id");
    }
}