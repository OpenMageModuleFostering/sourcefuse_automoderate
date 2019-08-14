<?php

class Sourcefuse_Automoderate_Helper_Data extends Mage_Core_Helper_Abstract {

    public function checkCustomerGroup() {
        $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $reviewGroups = explode(',', Mage::getStoreConfig(Sourcefuse_Automoderate_Model_Automoderate::XML_ALLOWED_CUSTOMER_GROUP));
        if (Mage::getSingleton('customer/session')->isLoggedIn() && in_array($groupId, $reviewGroups)) {
            return TRUE;
        }
    }
    
    public function checkTagCustomerGroup() {
        $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $reviewGroups = explode(',', Mage::getStoreConfig(Sourcefuse_Automoderate_Model_Automoderate::XML_TAG_ALLOWED_CUSTOMER_GROUP));
        if (Mage::getSingleton('customer/session')->isLoggedIn() && in_array($groupId, $reviewGroups)) {
            return TRUE;
        }
    }

}
