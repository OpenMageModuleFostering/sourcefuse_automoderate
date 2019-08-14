<?php

class Sourcefuse_Automoderate_Model_System_Config_Source_Customergroup {

    public function toOptionArray() {
        $groups = Mage::getResourceModel('customer/group_collection')
                        ->setRealGroupsFilter()
                        ->loadData()->toOptionArray();
        array_unshift($groups, array('value' => '', 'label' => Mage::helper('adminhtml')->__('--- Please Select ---')));
        return $groups;
    }

}
