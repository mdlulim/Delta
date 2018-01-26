<?php
/**
 * Consnet_ManageCustomer extension
 */
namespace Consnet\ManageCustomer\Block\Adminhtml\Customer\Edit;

/**
 * @method Tabs setTitle(\string $title)
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Customer Information'));
    }
}
