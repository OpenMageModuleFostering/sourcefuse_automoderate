<?php

class Sourcefuse_Automoderate_Model_Observer {

    public function autoModerateProductReviews($observer) {

        $autoModerate = Mage::getStoreConfig(Sourcefuse_Automoderate_Model_Automoderate::XML_ENABLE_AUTO_APPROVE, Mage::app()->getStore());
        $successfulMessage = Mage::getStoreConfig(Sourcefuse_Automoderate_Model_Automoderate::XML_PRODUCT_REVIEW_MESSAGE, Mage::app()->getStore());
        // die("Auto Approve : ".$autoModerate."<br /> Succesfull Message : ".$successfulMessage);

        $status = Mage_Review_Model_Review::STATUS_PENDING;
        if ($autoModerate == 1)
            $status = Mage_Review_Model_Review::STATUS_APPROVED;
        if ($autoModerate == 0)
            $status = Mage_Review_Model_Review::STATUS_PENDING;
        $checkCustomerGroup = Mage::helper('automoderate')->checkCustomerGroup();

        if ($checkCustomerGroup) {
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            $connection->update(
                    Mage::getSingleton('core/resource')->getTableName('review'), array('status_id' => $status), array('review_id = ?' => $observer->getObject()->getReviewId()));
        }


        /*
          Mage::getSingleton("review/session")->getMessages(true);
          Mage::getSingleton("core/session")->getMessages(true);
         */

        Mage::getSingleton('core/session')->addSuccess($successfulMessage);


        return $this;
    }

}
