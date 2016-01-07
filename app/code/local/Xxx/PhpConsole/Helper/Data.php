<?php

class Xxx_PhpConsole_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * usage Mage::helper('phpconsole')->log("message or $variable");
     *
     * @param $value
     * @param int $trace
     * @return $this
     */
    public function log($value, $trace = 0)
    {
        $this->getPhpConsole()->log($value, $trace);
        return $this;
    }

    /**
     * usage Mage::helper('phpconsole')->debug("message or $variable");
     *
     * @param $value
     * @return $this
     */
    public function debug($value)
    {
        if ($value instanceof Varien_Object) {
            $value = $value->debug();
        }

        $this->log($value);
        return $this;
    }

    public function getPhpConsole()
    {
        return Mage::getSingleton('phpconsole/phpconsole');
    }

    /**
     * @deprecated use log($value, true) instead
     */
    public function logTrace($value)
    {
        PC::getConnector()->getDebugDispatcher()->detectTraceAndSource = true;
        PC::debug($value);
        PC::getConnector()->getDebugDispatcher()->detectTraceAndSource = false;
    }
}
