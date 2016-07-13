<?php

class Xxx_YandexTranslate_Helper_Data extends Mage_Core_Helper_Abstract
{
    const DEFAULT_LANG = "pl";
    const YANDEX_TRANSLATE_API_KEY = "___cut";

    public function translate($string, $lang = self::DEFAULT_LANG)
    {
        /** @var $model Xxx_YandexTranslate_Model_Translated */
        $model = Mage::getModel('yandextranslate/translated');
        return $model->setString($string)->setLanguage($lang)->translate()->getTranslated();
    }
}
