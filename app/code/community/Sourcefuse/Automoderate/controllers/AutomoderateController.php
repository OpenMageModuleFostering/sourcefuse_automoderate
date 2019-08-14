<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Review
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer reviews controller
 *
 * @category    Mage
 * @package     Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Sourcefuse_Automoderate_AutomoderateController extends Mage_Core_Controller_Front_Action {

    public function validateAction() {
        $response = array();
        $response['success'] = true;
//        $checkCustomerGroup = FALSE;
        $details = $this->getRequest()->getPost();

        /** Check customer group * */
        $checkCustomerGroup = Mage::helper('automoderate')->checkCustomerGroup();

        if ($checkCustomerGroup) {

            /** detail review check * */
            $blockDetail = $this->compareBlockings($details['detail']);
            if ($blockDetail) {
                $response['success'] = false;
                $response['detail-message'] = 'Please remove these words: <b>' . $blockDetail . '</b>';
            }

            /** nickname ckeck * */
            $blockNickname = $this->compareBlockings($details['nickname']);
            if ($blockNickname) {
                $response['success'] = false;
                $response['nickname-message'] = 'Please remove these words: <b>' . $blockNickname . '</b>';
            }

            /** summary ckeck * */
            $blockTitle = $this->compareBlockings($details['title']);
            if ($blockTitle) {
                $response['success'] = false;
                $response['summary-message'] = 'Please remove these words: <b>' . $blockTitle . '</b>';
            }
//            echo Mage::helper('core')->jsonEncode($response);
        }
        echo Mage::helper('core')->jsonEncode($response);
    }

    public function compareBlockings($content) {
        $blockingContents = Mage::getStoreConfig(Sourcefuse_Automoderate_Model_Automoderate::XML_REVIEW_FILTER, Mage::app()->getStore());
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
