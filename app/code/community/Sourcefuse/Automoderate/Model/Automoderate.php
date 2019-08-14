<?php

class Sourcefuse_Automoderate_Model_Automoderate extends Mage_Core_Model_Abstract
{
    const XML_ENABLE_AUTO_APPROVE = 'automoderate_section/automoderate_group/automoderateproductreviews';
    const XML_PRODUCT_REVIEW_MESSAGE = 'automoderate_section/automoderate_group/automoderateproductreviewmessage';
    const XML_ALLOWED_CUSTOMER_GROUP = 'automoderate_section/automoderate_group/allowed_customer_group';
    const XML_REVIEW_FILTER = 'automoderate_section/automoderate_group/automoderate_reviews_filter';
    
    const XML_ENABLE_TAG_AUTO_APPROVE = 'automoderate_section/automoderate_tag/automoderatetag';
    const XML_TAG_FILTER = 'automoderate_section/automoderate_tag/automoderate_tag_filter';
    const XML_TAG_ALLOWED_CUSTOMER_GROUP = 'automoderate_section/automoderate_tag/allowed_customer_group';

    public function _construct()
    {
        parent::_construct();
        $this->_init('automoderate/automoderate');
    }
}