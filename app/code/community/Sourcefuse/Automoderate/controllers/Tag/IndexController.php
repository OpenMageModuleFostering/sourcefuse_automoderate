<?php

require_once "Mage/Tag/controllers/IndexController.php";

class Sourcefuse_Automoderate_Tag_IndexController extends Mage_Tag_IndexController {

    /**
     * Saving tag and relation between tag, customer, product and store
     */
    public function saveAction() {
        $customerSession = Mage::getSingleton('customer/session');
        if (!$customerSession->authenticate($this)) {
            return;
        }
        $tagName = (string) $this->getRequest()->getQuery('productTagName');
        $productId = (int) $this->getRequest()->getParam('product');

        if (strlen($tagName) && $productId) {
            $session = Mage::getSingleton('catalog/session');
            $product = Mage::getModel('catalog/product')
                    ->load($productId);
            if (!$product->getId()) {
                $session->addError($this->__('Unable to save tag(s).'));
            } else {
                try {
                    $customerId = $customerSession->getCustomerId();
                    $storeId = Mage::app()->getStore()->getId();

                    $tagModel = Mage::getModel('tag/tag');
                    /** Check customer group * */
                    if (Mage::getStoreConfig(Sourcefuse_Automoderate_Model_Automoderate::XML_ENABLE_TAG_AUTO_APPROVE)) {
                        $checkTagCustomerGroup = Mage::helper('automoderate')->checkTagCustomerGroup();
                    }
                    $tagStatus = isset($checkTagCustomerGroup) ? $tagModel->getApprovedStatus() : $tagModel->getPendingStatus();

                    // added tag relation statuses
                    $counter = array(
                        Mage_Tag_Model_Tag::ADD_STATUS_NEW => array(),
                        Mage_Tag_Model_Tag::ADD_STATUS_EXIST => array(),
                        Mage_Tag_Model_Tag::ADD_STATUS_SUCCESS => array(),
                        Mage_Tag_Model_Tag::ADD_STATUS_REJECTED => array()
                    );
                    $tagNamesArr = $this->_cleanTags($this->_extractTags($tagName));
                    foreach ($tagNamesArr as $tagName) {
                        // unset previously added tag data
                        $tagModel->unsetData()
                                ->loadByName($tagName);

                        if (!$tagModel->getId()) {
                            $tagModel->setName($tagName)
                                    ->setFirstCustomerId($customerId)
                                    ->setFirstStoreId($storeId)
                                    ->setStatus($tagStatus)
                                    ->save();
                        }
                        $relationStatus = $tagModel->saveRelation($productId, $customerId, $storeId);
                        $counter[$relationStatus][] = $tagName;
                    }
                    $this->_fillMessageBox($counter);
                } catch (Exception $e) {
                    Mage::logException($e);
                    $session->addError($this->__('Unable to save tag(s).'));
                }
            }
        }
        $this->_redirectReferer();
    }
    
    
    public function validateAction() {
        $response = array();
        $response['success'] = true;
//        $checkCustomerGroup = FALSE;
        $tags = $this->getRequest()->getPost();

        /** Check customer group * */
        $checkCustomerGroup = Mage::helper('automoderate')->checkTagCustomerGroup();

            if ($checkCustomerGroup) {

            /** tags check * */
            $blockDetail = $this->compareBlockings($tags['productTagName']);
            if ($blockDetail) {
                $response['success'] = false;
                $response['prdt-tag-name'] = 'Please remove these words: <b>' . $blockDetail . '</b>';
            }

        }
        echo Mage::helper('core')->jsonEncode($response);
    }

    public function compareBlockings($content) {
        $blockingContents = Mage::getStoreConfig(Sourcefuse_Automoderate_Model_Automoderate::XML_TAG_FILTER, Mage::app()->getStore());
        $ary1 = explode(' ', $content);
        $ary2 = explode(',', $blockingContents);
        $ary1 = array_map('strtolower', $ary1);
        $ary2 = array_map('strtolower', $ary2);
        $ary1 = array_map('trim', $ary1);
        $ary2 = array_map('trim', $ary2);
        $common = implode(', ', array_intersect($ary1, $ary2));
        return $common;
    }


}
