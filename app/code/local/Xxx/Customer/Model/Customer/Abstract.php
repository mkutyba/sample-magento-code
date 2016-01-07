<?php

if (class_exists('Magestore_Onestepcheckout_Model_Customer_Customer')) {
    class Xxx_Customer_Model_Customer_Abstract extends Magestore_Onestepcheckout_Model_Customer_Customer {}
} else {
    class Xxx_Customer_Model_Customer_Abstract extends Mage_Adminhtml_Block_System_Config_Edit {}
}