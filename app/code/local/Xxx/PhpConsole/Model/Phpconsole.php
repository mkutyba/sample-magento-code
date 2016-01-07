<?php

class Xxx_PhpConsole_Model_Phpconsole
{
    protected $_enabled = false;
    protected $_handler = null;
    protected $_key;

    public function __construct()
    {
    }

    public function isEnabled()
    {
        if (!(Mage::getStoreConfig('dev/debug/phpconsole') && Mage::helper('core')->isDevAllowed())) {
            $this->_enabled = false;
        } else {
            $this->_enabled = true;
        }
        return $this->_enabled;
    }

    public function _init()
    {
        if (!$this->isEnabled()) return;

        if (is_null($this->_handler)) {
            $this->_key = Mage::getStoreConfig('general/store_information/name');

            $this->_handler = PhpConsole\Handler::getInstance();
            $this->_handler->start();

            $this->_handler->getConnector()->setSourcesBasePath($_SERVER['DOCUMENT_ROOT']); // so files paths on client will be shorter (optional)

            $this->_handler->getConnector()->getDebugDispatcher()->setDumper(
                new PhpConsole\Dumper(20, 1000, 1000) // set levelLimit, itemsCountLimit, itemSizeLimit
            );

            // $this->configureEvalProvider();
        }
    }

    public function log($value, $trace = 0)
    {
        if (!$this->isEnabled()) return;

        $this->_init();
        $this->_handler->getConnector()->getDebugDispatcher()->detectTraceAndSource = (bool)$trace;
        $this->_handler->debug($value, $this->_key);
    }

    private function configureEvalProvider()
    {
        $connector = PhpConsole\Connector::getInstance();
        $connector->setPassword('123');
        // Configure eval provider
        $evalProvider = $connector->getEvalDispatcher()->getEvalProvider();
        $evalProvider->addSharedVar('post', $_POST); // so "return $post" code will return $_POST
        $evalProvider->setOpenBaseDirs(array(__DIR__)); // see http://php.net/open-basedir
        $connector->startEvalRequestsListener(); // must be called in the end of all configurations
    }
}
