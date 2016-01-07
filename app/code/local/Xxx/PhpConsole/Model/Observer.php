<?php

class Xxx_PhpConsole_Model_Observer
{
    public function initPhpConsole()
    {
        Mage::helper('phpconsole')->getPhpConsole()->_init();
    }
}
