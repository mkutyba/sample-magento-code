<?php

require_once('lib/fotolia/fotolia-api.php');

class Xxx_Fotolia_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $api = null;
    protected $lang = Fotolia_Api::LANGUAGE_ID_PL_PL;

    function __construct()
    {
        if ($this->api === null) {
            $this->api = new Fotolia_Api($this->_getApiKey());
        }
    }

    private function _getApiKey()
    {
        return Mage::getStoreConfig("fotolia/account/api_key");
    }

    private function _getUsername()
    {
        return Mage::getStoreConfig("fotolia/account/username");
    }

    private function _getPassword()
    {
        return Mage::getStoreConfig("fotolia/account/password");
    }

    protected function _loginUser()
    {
        $this->api->loginUser($this->_getUsername(), $this->_getPassword());
        return $this;
    }

    public function getCredits()
    {
        $result = $this->_loginUser()->api->getUserData();
        return $result['nb_credits'];
    }

    public function getMediaData($mediaId, $thumbnailSize = 110, $cacheResult = true)
    {
        if ($cacheResult) {
            $cache = Mage::app()->getCacheInstance();
            $cacheKey = date('y_W') . "_fotolia_getmediadata_{$mediaId}_{$thumbnailSize}";
            $value = $cache->load($cacheKey);
            if ($value) {
                $value = unserialize($value);
            } else {
                $value = '';
            }
        } else {
            $value = '';
        }
        if (is_array($value)) {
            $result = $value;
        } else {
            if ($thumbnailSize == 1000 || $thumbnailSize == 240) {
                try {
                    $result = $this->api->getMediaData($mediaId, 400);
                } catch (Exception $e) {
                    return false;
                }
                $result['thumbnail_url'] = str_replace('/400_', "/{$thumbnailSize}_", $result['thumbnail_url']);
                if ($result['width'] > $result['height']) {
                    $result['thumbnail_height'] = (int)round($result['height'] * $thumbnailSize / $result['width']);
                    $result['thumbnail_width'] = $thumbnailSize;
                } else {
                    $result['thumbnail_width'] = (int)round($result['width'] * $thumbnailSize / $result['height']);
                    $result['thumbnail_height'] = $thumbnailSize;
                }
            } else {
                if ($thumbnailSize == "all") {
                    try {
                        $result = $this->api->getMediaData($mediaId, 110, $this->lang);
                    } catch (Exception $e) {
                        return false;
                    }
                } else {
                    try {
                        $result = $this->api->getMediaData($mediaId, $thumbnailSize, $this->lang);
                    } catch (Exception $e) {
                        return false;
                    }
                }
                if ($thumbnailSize == "all") {
                    $result = $this->_prepareAdditionalThumbSizes($result);
                }
            }

            if ($cacheResult) {
                $cache->save(serialize($result), $cacheKey);
            }
        }


        return $result;
    }

    private function _prepareAdditionalThumbSizes($data)
    {
        if (isset($data['thumbnail_url'])) {
            $data['thumbnail_30_url'] = str_replace('/110_', '/30_', $data['thumbnail_url']);
            $data['thumbnail_30_width'] = $data['thumbnail_width'] * 30 / 110;
            $data['thumbnail_30_height'] = $data['thumbnail_height'] * 30 / 110;
            $data['thumbnail_110_url'] = $data['thumbnail_url'];
            $data['thumbnail_110_width'] = $data['thumbnail_width'];
            $data['thumbnail_110_height'] = $data['thumbnail_height'];
            $data['thumbnail_160_url'] = str_replace('/110_', '/160_', $data['thumbnail_url']);
            $data['thumbnail_160_width'] = $data['thumbnail_width'] * 160 / 110;
            $data['thumbnail_160_height'] = $data['thumbnail_height'] * 160 / 110;
            $data['thumbnail_240_url'] = str_replace('/110_', '/240_', $data['thumbnail_url']);
            $data['thumbnail_240_width'] = $data['thumbnail_width'] * 240 / 110;
            $data['thumbnail_240_height'] = $data['thumbnail_height'] * 240 / 110;
            $data['thumbnail_400_url'] = str_replace('/110_', '/400_', $data['thumbnail_url']);
            $data['thumbnail_400_width'] = $data['thumbnail_width'] * 400 / 110;
            $data['thumbnail_400_height'] = $data['thumbnail_height'] * 400 / 110;
        }

        return $data;
    }

    public function getMediaCost($mediaId, $size)
    {
        $mediaData = $this->getMediaData($mediaId);
        $licenses = $mediaData['licenses'];
        foreach ($licenses as $key => $value) {
            if ($value['name'] == $size) {
                return $value['price'];
            }
        }
        return false;
    }
}
