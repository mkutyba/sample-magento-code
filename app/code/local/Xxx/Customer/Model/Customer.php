<?php

/**
 * Customer model rewrite
 */
class Xxx_Customer_Model_Customer extends Xxx_Customer_Model_Customer_Abstract
{
    // old salt
    const SOME_PREVIOUS_COMPANY_SALT = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

    /**
     * Validate password with salted hash
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        //two methods: old accounts (version from Some Previous Company) and new standard accounts
        $hash = $this->getPasswordHash();
        if (!$hash) {
            return false;
        }
        return ($hash == md5($password . self::SOME_PREVIOUS_COMPANY_SALT) || parent::validatePassword($password));
    }

    /**
     * Hash customer password
     *
     * @param   string $password
     * @param   int $salt
     * @return  string
     */
    public function hashPassword($password, $salt = null)
    {
        if ($salt === false) {
            return md5($password . self::SOME_PREVIOUS_COMPANY_SALT);
        } else {
            return Mage::helper('core')->getHash($password, !is_null($salt) ? $salt : 2);
        }
    }

}
