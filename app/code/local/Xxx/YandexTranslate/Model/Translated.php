<?php

use Yandex\Translate\Translator;
use Yandex\Translate\Exception;

/**
 * @method string getString()
 * @method Xxx_YandexTranslate_Model_Translated setString(string $value)
 * @method string getLanguage()
 * @method Xxx_YandexTranslate_Model_Translated setLanguage(string $value)
 * @method string getTranslated()
 * @method Xxx_YandexTranslate_Model_Translated setTranslated(int $value)
 */
class Xxx_YandexTranslate_Model_Translated extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init("yandextranslate/translated");
    }

    public function translate()
    {
        $this->setTranslated(false);

        $clone = Mage::getModel('yandextranslate/translated')->_loadTranslated($this->getString(), $this->getLanguage());
        if ($clone->getId()) {
            return $clone;
        }

        $this->_translateByApi();
        return $this;
    }

    protected function _loadTranslated($string, $language)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('string', $string)
            ->addFieldToFilter('language', $language);
        return $collection->getFirstItem();
    }

    protected function _translateByApi()
    {
        try {
            /** @var Yandex\Translate\Translator $translator */
            $translator = new Translator(Xxx_YandexTranslate_Helper_Data::YANDEX_TRANSLATE_API_KEY);
            /** @var Yandex\Translate\Translation $translation */
            $translation = $translator->translate($this->getString(), $this->getLanguage());
            $this->setTranslated($translation->__toString())->save();
        } catch (Exception $e) {
            Mage::logException($e);
            // fallback
            $this->setTranslated($this->getString());
        }
    }
}